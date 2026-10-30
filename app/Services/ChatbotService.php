<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    /**
     * Process a candidate's message and generate a grounded, safe response.
     */
    public function reply(string $rawMessage, array $history = []): string
    {
        $message = trim(strip_tags($rawMessage));
        $identifier = auth()->check() ? 'user_' . auth()->id() : 'ip_' . request()->ip();

        // Layer 1 & 2: Local Pre-filter & Guardrails
        $filterError = $this->runPreFilters($message, $identifier);
        if ($filterError !== null) {
            return $filterError;
        }

        // Layer 3: Direct User Context Intent (Application Status)
        if ($this->isAskingApplicationStatus($message)) {
            return $this->handleApplicationStatusQuery();
        }

        // Layer 4 & 5: AI Engine (Gemini) with grounded job data & strict guardrails
        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return $this->handleOfflineResponse($message);
        }

        return $this->callGeminiApi($message, $history, $apiKey);
    }

    /**
     * Layer 1 & 2 Filter: Rate limiting, length check, profanity, and out-of-scope detection.
     */
    protected function runPreFilters(string $message, string $identifier): ?string
    {
        // 1. Rate Limiting (6 requests/minute)
        $rateKey = 'chatbot_rate:' . $identifier;
        if (RateLimiter::tooManyAttempts($rateKey, 6)) {
            $seconds = RateLimiter::availableIn($rateKey);
            return app()->getLocale() === 'en'
                ? "Please wait {$seconds} seconds before sending your next question."
                : "Mohon tunggu {$seconds} detik sebelum mengirim pertanyaan berikutnya.";
        }
        RateLimiter::hit($rateKey, 60);

        // 2. Length Validation
        if (mb_strlen($message) < 2) {
            return app()->getLocale() === 'en'
                ? "Please type your question regarding vacancies or recruitment processes."
                : "Silakan ketik pertanyaan Anda seputar lowongan atau proses rekrutmen.";
        }
        if (mb_strlen($message) > 300) {
            return app()->getLocale() === 'en'
                ? "Your question is too long (maximum 300 characters). Please shorten it so we can process it properly."
                : "Pertanyaan Anda terlalu panjang (maksimal 300 karakter). Mohon persingkat agar dapat kami proses dengan baik.";
        }

        // 3. Local Profanity / Abuse Filter
        $badWords = ['anjing', 'bangsat', 'babi', 'tolol', 'goblok', 'kontol', 'memek', 'kampret', 'bajingan', 'pantek', 'fuck', 'shit', 'bitch', 'asshole'];
        foreach ($badWords as $bad) {
            if (stripos($message, $bad) !== false) {
                return app()->getLocale() === 'en'
                    ? "Please use polite and professional language when interacting with Mika Assistant."
                    : "Mohon gunakan bahasa yang sopan dan profesional saat berinteraksi dengan Mika Assistant.";
            }
        }

        // 4. Out-of-Scope Pre-check (Fast regex intercept to save AI token & prevent jailbreaks)
        $offTopicPatterns = [
            '/\b(resep|recipe|masakan|cooking|kuliner)\b/i',
            '/\b(puisi|poem|cerpen|pantun|lirik|lyrics|dongeng|lelucon|joke)\b/i',
            '/\b(coding|script|python|php|javascript|html|c\+\+|java)\b/i',
            '/\b(prabowo|jokowi|ganjar|anies|partai|pemilu|pilpres|election|president)\b/i',
            '/\b(matematika|math|rumus|fisika|physics|tugas\s*sekolah|homework)\b/i',
            '/(abaikan\s*(aturan|instruksi)|jailbreak|mode\s*dan|forget\s*previous)/i',
        ];

        foreach ($offTopicPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return app()->getLocale() === 'en'
                    ? "Hello! I am Mika Assistant, dedicated to helping you with job vacancies, requirements, and recruitment processes at Mika Career. Please feel free to ask questions about your career journey."
                    : "Halo! Saya Mika Assistant, khusus membantu seputar lowongan pekerjaan, kualifikasi, dan proses seleksi di Mika Career. Silakan ajukan pertanyaan terkait karir Anda.";
            }
        }

        return null;
    }

    /**
     * Check if user is asking about their personal job application status.
     */
    protected function isAskingApplicationStatus(string $message): bool
    {
        return (bool) preg_match('/(status|progres|progress|tahap|stage|pengumuman|result)\s*(lamaran|pendaftaran|aplikasi|application|interview)/i', $message)
            || (bool) preg_match('/(lamaran|aplikasi|application)\s*(saya|ku|status)/i', $message)
            || (bool) preg_match('/my\s*(job\s*)?application/i', $message);
    }

    /**
     * Answer application status query based on Auth state (Privacy Layer 3).
     */
    protected function handleApplicationStatusQuery(): string
    {
        $isEn = app()->getLocale() === 'en';

        if (!auth()->check()) {
            return $isEn
                ? "To check your application status, please Log In to your Mika Career account first and navigate to Application History."
                : "Untuk mengecek status lamaran, silakan Masuk (Login) ke akun Mika Career Anda terlebih dahulu, lalu buka menu Riwayat Lamaran.";
        }

        $user = auth()->user();
        $applications = JobApplication::whereHas('profile', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with('job')->latest('applied_at')->limit(3)->get();

        if ($applications->isEmpty()) {
            return $isEn
                ? "Hello {$user->name}, you currently have no active application records. Please explore the Vacancies menu to start applying."
                : "Halo {$user->name}, saat ini Anda belum memiliki riwayat lamaran aktif. Silakan jelajahi menu Lowongan Kerja untuk mulai melamar.";
        }

        $reply = $isEn
            ? "Hello {$user->name}, here is the latest status of your job application(s):\n\n"
            : "Halo {$user->name}, berikut status lamaran terbaru Anda:\n\n";

        foreach ($applications as $app) {
            $jobTitle = ($isEn && !empty($app->job?->title_en)) ? $app->job->title_en : ($app->job->title ?? ($isEn ? 'Position Not Found' : 'Posisi Tidak Ditemukan'));
            $status = $app->status ?? ($isEn ? 'Under Review' : 'Dalam Review');
            $date = $app->applied_at ? $app->applied_at->format('d M Y') : '-';
            $dateLabel = $isEn ? 'Submitted:' : 'Diajukan:';
            $reply .= "• **{$jobTitle}**\n  Status: *{$status}* ({$dateLabel} {$date})\n";
        }

        $reply .= $isEn
            ? "\nYou can view full details on your Applicant Dashboard."
            : "\nAnda dapat melihat detail lengkapnya di Dashboard Pelamar.";

        return $reply;
    }

    /**
     * Graceful fallback when GEMINI_API_KEY is not yet configured.
     */
    protected function handleOfflineResponse(string $message): string
    {
        $isEn = app()->getLocale() === 'en' || (bool) preg_match('/(job|opening|position|vacancy|apply|hiring)/i', $message);

        if (preg_match('/(loker|lowongan|posisi|kerjaan|buka|tersedia|job|opening|position|vacancy|hiring)/i', $message)) {
            $jobs = Job::active()->select('title', 'title_en', 'employment_type', 'location')->limit(5)->get();
            if ($jobs->isEmpty()) {
                return $isEn
                    ? "Currently there are no active job openings. Please stay tuned to our Careers page!"
                    : "Saat ini belum ada lowongan kerja yang sedang dibuka. Pantau terus halaman Karir kami ya!";
            }
            $res = $isEn
                ? "Here are some of the active openings at Mika Career:\n\n"
                : "Berikut beberapa posisi yang sedang aktif dibuka di Mika Career:\n\n";

            foreach ($jobs as $j) {
                $title = ($isEn && !empty($j->title_en)) ? $j->title_en : $j->title;
                $res .= "• **{$title}** ({$j->employment_type} - {$j->location})\n";
            }
            $res .= $isEn
                ? "\nPlease click the Vacancies menu above to view complete qualifications and apply."
                : "\nSilakan klik menu Lowongan Kerja di atas untuk melihat kualifikasi lengkap dan melamar.";
            return $res;
        }

        if (preg_match('/(cara\s*melamar|daftar|tahapan|seleksi|syarat|how\s*to\s*apply|application\s*process|steps)/i', $message)) {
            return $isEn
                ? "How to apply at Mika Career:\n1. Log In / Register as a job seeker.\n2. Complete your profile and upload your CV (PDF format).\n3. Open the Vacancies menu, select your preferred role, and click 'Apply Now'.\n\nSelection Stages: Administration Review → Technical/Online Assessment → HR & User Interview → Offering Letter → Onboarding."
                : "Cara melamar di Mika Career:\n1. Masuk / Daftar akun pencari kerja.\n2. Lengkapi profil dan unggah CV (format PDF).\n3. Buka menu Lowongan Kerja, pilih posisi yang cocok, lalu klik tombol 'Lamar'.\n\nTahapan seleksi: Seleksi Administrasi → Tes/Psikotes → Wawancara → Penawaran (Offering).";
        }

        return $isEn
            ? "Hello! I am Mika Assistant. You can ask me about open vacancies, how to apply, or required qualifications. How may I assist you today?"
            : "Halo! Saya Mika Assistant. Anda dapat menanyakan lowongan yang sedang buka, cara melamar, atau kualifikasi yang dibutuhkan. Ada yang bisa saya bantu?";
    }
    }

    /**
     * Send prompt with grounded database context to Gemini API.
     */
    protected function callGeminiApi(string $message, array $history, string $apiKey): string
    {
        $model = config('services.gemini.model', 'gemini-flash-lite-latest');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        // Collect live active jobs for grounded context (RAG-lite)
        $activeJobs = Job::active()
            ->with(['department:id,name', 'degrees:id,name', 'majors:id,name'])
            ->select('id', 'title', 'department_id', 'employment_type', 'experience_level', 'location', 'deadline')
            ->limit(12)
            ->get();

        $jobListText = $activeJobs->map(function ($j) {
            $dept = $j->department->name ?? 'Umum';
            $dl = $j->deadline ? $j->deadline : 'Terbuka';
            $edu = $j->degrees->pluck('name')->implode(', ');
            $eduText = $edu ? " | Pendidikan: {$edu}" : '';
            return "- {$j->title} (Divisi: {$dept}, Tipe: {$j->employment_type}, Lokasi: {$j->location}, Level: {$j->experience_level}, Batas Lamaran: {$dl}{$eduText})";
        })->implode("\n");

        if (empty($jobListText)) {
            $jobListText = "Saat ini tidak ada lowongan yang sedang aktif.";
        }

        // Auto-read external documents from storage/app/knowledge/ (.txt, .md)
        $externalKnowledge = $this->getExternalKnowledge();
        $externalSection = $externalKnowledge ? "\n\nDOKUMEN TENTANG PERUSAHAAN (DARI FILE KNOWLEDGE):\n{$externalKnowledge}" : '';

        $systemInstruction = <<<TEXT
Nama: Mika Assistant
Peran: Asisten Virtual Rekrutmen Resmi PT Mitra Karya Analitika (Mika Career).

KONTEKS LOWONGAN AKTIF SAAT INI (DATABASE REAL-TIME):
{$jobListText}{$externalSection}

PANDUAN & BASIS PENGETAHUAN PERUSAHAAN (KNOWLEDGE BASE):
1. Profil & Lokasi Perusahaan:
   - Perusahaan: PT Mitra Karya Analitika (Mika Career), perusahaan teknologi, analitika, dan manufaktur berorientasi masa depan.
   - Lokasi Kerja: Sesuai posisi (Semarang, Pemalang, Jawa Tengah).
   - Hari & Jam Kerja: Senin - Jumat (08.00 - 17.00 WIB).

2. Alur Pendaftaran & Seleksi:
   - Langkah Melamar:
     1. Buat akun / Masuk ke portal Mika Career.
     2. Lengkapi Data Profil (Data Diri, Pendidikan, Pengalaman) dan unggah CV dalam format PDF (maks. 2MB).
     3. Masuk ke menu Lowongan Kerja, pilih posisi yang diminati, lalu klik tombol 'Lamar Sekarang'.
   - Tahapan Seleksi: 1. Seleksi Berkas/Administrasi → 2. Tes Teknis/Psikotes Online → 3. Wawancara HR & User → 4. Penawaran Kerja (Offering Letter) → 5. Onboarding.
   - Estimasi Pengumuman Berkas: Sekitar 7 - 14 hari kerja setelah batas waktu lowongan berakhir. Pelamar dapat memantau status secara berkala di Dashboard Pelamar.

3. Kebijakan Lamaran:
   - Apakah boleh melamar lebih dari 1 posisi? Boleh, namun disarankan maksimal 2 posisi yang paling relevan dengan kualifikasi.
   - Fresh Graduate & Magang: Lulusan baru (Fresh Graduate) dan mahasiswa tingkat akhir dipersilakan melamar untuk posisi yang bertipe Internship atau Entry Level.
   - Biaya Rekrutmen: Proses rekrutmen di PT Mitra Karya Analitika 100% GRATIS dan tidak dipungut biaya apapun. Hati-hati terhadap segala bentuk penipuan.

4. Fasilitas & Benefit Karyawan:
   - BPJS Kesehatan & BPJS Ketenagakerjaan.
   - Tunjangan kinerja, bonus tahunan, serta program pelatihan & pengembangan karir.

ATURAN KETAT (GUARDRAILS):
1. Anda HANYA boleh menjawab pertanyaan seputar rekrutmen, lowongan kerja aktif, kualifikasi, profil perusahaan, dan alur pendaftaran di Mika Career.
2. JANGAN menjawab pertanyaan non-karir (resep masakan, pemrograman/coding, matematika, politik, lelucon panjang, curhat). TOLAK DENGAN RAMAH: "Maaf, saya asisten khusus rekrutmen Mika Career. Silakan tanyakan hal seputar lowongan atau proses seleksi kerja."
3. Jika pelamar menanyakan lowongan yang tidak tercantum dalam daftar di atas, sampaikan secara jujur bahwa posisi tersebut belum dibuka saat ini dan sarankan memantau halaman Karir secara berkala.
4. Bahasa & Respon Dwibahasa (Bilingual):
   - Jika pengguna bertanya dalam Bahasa Inggris (English), Anda WAJIB menjawab secara fasih, ramah, dan profesional dalam Bahasa Inggris.
   - Jika pengguna bertanya dalam Bahasa Indonesia, jawab dalam Bahasa Indonesia.
   - Gaya Bahasa: Profesional, ramah, percaya diri, padat, dan jelas (maksimal 2-3 paragraf ringkas).
TEXT;

        // Build contents payload (optionally include last 2 conversation turns)
        $contents = [];
        $trimmedHistory = array_slice($history, -4);
        foreach ($trimmedHistory as $item) {
            $role = ($item['role'] ?? 'user') === 'user' ? 'user' : 'model';
            $text = $item['text'] ?? '';
            if (!empty($text)) {
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $text]]
                ];
            }
        }
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $message]]
        ];

        $payload = [
            'system_instruction' => [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.2, // Low temperature for high factual grounding
                'maxOutputTokens' => 500,
            ]
        ];

        try {
            $response = Http::timeout(12)->post($endpoint, $payload);

            if ($response->successful()) {
                $json = $response->json();
                $reply = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if (!empty($reply)) {
                    return trim($reply);
                }
            }

            Log::warning('Chatbot Gemini API response error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // ponytail: fallback to local search if AI API fails or quotas run out
            return $this->handleOfflineResponse($message);

        } catch (\Throwable $e) {
            Log::error('Chatbot Gemini Exception: ' . $e->getMessage());
            return $this->handleOfflineResponse($message);
        }
    }

    /**
     * Read knowledge documents from storage/app/knowledge/ (.txt, .md).
     * Any text placed here is automatically learned by Mika Assistant.
     */
    protected function getExternalKnowledge(): string
    {
        $dir = storage_path('app/knowledge');
        if (!is_dir($dir)) {
            return '';
        }

        $content = '';
        $files = glob($dir . '/*.{txt,md}', GLOB_BRACE) ?: [];
        foreach ($files as $file) {
            $text = trim(@file_get_contents($file) ?: '');
            if (!empty($text)) {
                $filename = basename($file);
                // Support up to 50,000 characters per file (Gemini handles 1M+ context tokens)
                $truncated = mb_substr($text, 0, 50000);
                $content .= "\n[File: {$filename}]\n{$truncated}\n";
            }
        }

        return trim($content);
    }
}

<?php

namespace App\Livewire\Applicant;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\JobApplication;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestAnswer;
use App\Models\QuestionBank;
use App\Models\DiscTestResult;
use App\Models\PapiTestResult;
use App\Services\AspectEvaluationService;
use App\Services\DiscCalculatorService;
use App\Services\PapiKostickCalculatorService;
use App\Services\TestSequenceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApplicantOnlineTest extends Component
{
    use WithFileUploads;

    public $applicationId;
    public $testId;
    public $attemptId;

    // State: 'intro', 'taking', 'completed'
    public $testState = 'intro';

    public $application;
    public $test;
    public $attempt;

    // Sequential test support
    public $allTests = [];    // Semua test dalam lowongan (ordered)
    public $nextTest = null;  // Test berikutnya setelah test ini selesai

    public $questions = [];
    public $currentQuestionIndex = 0;

    // Jawaban user:
    // Pilihan ganda: [ question_id => option_id ]
    // Essay: [ question_id => 'teks jawaban' ]
    // DISC: [ question_id => ['most' => option_id, 'least' => option_id] ]
    public $answers = [];

    // Lampiran file jawaban essay (1 file untuk semua soal uraian)
    public $essayAttachment = null; // ['url' => '...', 'name' => '...', 'size' => 12345]

    // Form tautan khusus video/drive/eksternal (1 link untuk seluruh soal uraian)
    public $essayLink = '';

    // Temporary upload file
    public $essayFile;

    public $timeRemainingSeconds = 0;

    // Biodata Peserta untuk Snapshot Hasil Evaluasi
    public $participantName = '';
    public $participantAge = '';
    public $participantGender = 'male';
    public $testDate = '';

    // Paginasi multi-soal: tampilkan beberapa soal sekaligus (PAPI Kostick: 5, DISC & Ujian Umum: 1)
    public $currentPage = 0;       // Indeks halaman saat ini (0-based)
    public $questionsPerPage = 1;  // Jumlah soal per halaman

    // Paket Soal CBT (Khususnya Standar Tes FAT)
    public $selectedPkg = 1;

    // Modal Konfirmasi Selesai Ujian (Sinkron Server-Client)
    public $showConfirmModal = false;

    // Proctoring & Pelanggaran Layar Penuh (Fullscreen Guard)
    public $violationsCount = 0;
    public $violationLogs = [];

    // Pengaman navigasi (anti klik ganda yang membuat soal melompat)

    public function isFatTest(): bool
    {
        $cat = strtolower($this->test->category?->name ?? '');
        $title = strtolower($this->test->title ?? '');
        return str_contains($cat, 'fat') || str_contains($title, 'fat');
    }

    public function getPackageDefinitions(): array
    {
        return [
            1 => ['id' => 1, 'name' => 'PAKET 1', 'questions' => 100, 'duration' => 240, 'pass_pct' => 61, 'min_correct' => 61],
            2 => ['id' => 2, 'name' => 'PAKET 2', 'questions' => 75,  'duration' => 180, 'pass_pct' => 61, 'min_correct' => 46],
            3 => ['id' => 3, 'name' => 'PAKET 3', 'questions' => 50,  'duration' => 120, 'pass_pct' => 61, 'min_correct' => 31],
            4 => ['id' => 4, 'name' => 'PAKET 4', 'questions' => 25,  'duration' => 60,  'pass_pct' => 61, 'min_correct' => 16],
        ];
    }

    /**
     * Soal bergradasi (SJT) memiliki minimal satu opsi berbobot 1.
     * Soal teknis biner hanya memiliki bobot 2 dan 0.
     */
    protected function hasGradedWeights($question): bool
    {
        $weights = $question->metadata['option_weights'] ?? null;
        if (is_string($weights)) {
            $weights = json_decode($weights, true);
        }

        if (!is_array($weights)) {
            return false;
        }

        foreach ($weights as $weight) {
            if (is_numeric($weight) && (float) $weight === 1.0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ambil soal secara proporsional dari kelompok teknis dan situasional.
     * Tanpa ini, tes 60 soal dari bank 144 soal hanya mengambil 60 soal teknis pertama.
     */
    protected function selectQuestionsForAttempt($pool, int $targetCount)
    {
        if ($targetCount >= $pool->count()) {
            return $pool->values();
        }

        $graded = $pool->filter(fn ($q) => $this->hasGradedWeights($q))->values();
        $binary = $pool->reject(fn ($q) => $this->hasGradedWeights($q))->values();

        // Bank tanpa soal bergradasi: pertahankan perilaku lama (ambil N soal pertama).
        if ($graded->isEmpty() || $binary->isEmpty()) {
            return $pool->take($targetCount)->values();
        }

        $gradedCount = (int) round($targetCount * ($graded->count() / $pool->count()));
        $gradedCount = max(1, min($gradedCount, $graded->count(), $targetCount - 1));
        $binaryCount = $targetCount - $gradedCount;

        if ($binaryCount > $binary->count()) {
            $binaryCount = $binary->count();
            $gradedCount = $targetCount - $binaryCount;
        }

        $selected = $binary->take($binaryCount)->concat($graded->take($gradedCount));

        if ($this->test->is_random) {
            return $selected->shuffle()->values();
        }

        return $selected->sortBy(function ($q) {
            return $q->pivot?->order_number ?? ($q->metadata['number'] ?? $q->id);
        })->values();
    }

    public function mount($applicationId, $testId = null)
    {
        $user = Auth::user();
        if (!$user || !$user->applicantProfile) {
            abort(403, 'Akses tidak diizinkan. Silakan login sebagai pelamar.');
        }

        $this->applicationId = $applicationId;
        $this->application = JobApplication::with(['job.company', 'applicantProfile'])
            ->where('profile_id', $user->applicantProfile->id)
            ->findOrFail($applicationId);

        // Cari test untuk lowongan ini
        if ($testId) {
            $this->test = Test::with(['category', 'questions.options'])->findOrFail($testId);
        } else {
            $this->test = Test::with(['category', 'questions.options'])
                ->where(function ($q) {
                    $q->whereHas('jobs', function ($j) {
                        $j->where('jobs.id', $this->application->job_id);
                    })
                    ->orWhere('job_id', $this->application->job_id)
                    ->orWhere(function ($allJobs) {
                        $allJobs->whereDoesntHave('jobs')
                                ->whereNull('job_id')
                                ->where('test_type', 'recruitment');
                    });
                })
                ->first();
        }

        if (!$this->test) {
            abort(404, 'Paket ujian untuk lowongan ini tidak ditemukan.');
        }

        // VALIDASI GERBANG SEQUENTIAL:
        // Pastikan test sebelumnya sudah diselesaikan sebelum mengakses test ini
        if (!TestSequenceService::canAccessTest($this->application, $this->test)) {
            session()->flash('error', 'Tes ini belum dapat diakses. Selesaikan tes sebelumnya terlebih dahulu.');
            return redirect()->route('profile', ['tab' => 'riwayat']);
        }

        // Inisialisasi data profil pelamar untuk snapshot biodata (otomatis dari data pribadi)
        $applicantProfile = $user->applicantProfile;
        $this->participantName = $applicantProfile?->full_name ?: ($user->name ?: 'Pelamar');
        $rawGender = strtolower($applicantProfile?->gender ?? '');
        $this->participantGender = (str_contains($rawGender, 'perempuan') || str_contains($rawGender, 'wanita') || $rawGender === 'female') ? 'female' : 'male';
        $this->testDate = now()->toDateString();
        $this->participantAge = $applicantProfile?->birth_date ? Carbon::parse($applicantProfile->birth_date)->age : 25;
        if (!$this->participantAge || $this->participantAge < 10) {
            $this->participantAge = 25;
        }

        // Cek apakah pelamar memiliki riwayat pengerjaan sebelumnya
        $existingAttempt = TestAttempt::where('job_application_id', $this->application->id)
            ->where('test_id', $this->test->id)
            ->first();

        // VALIDASI GERBANG SELEKSI BERKAS:
        // Pelamar hanya diizinkan mulai tes jika statusnya sudah Lolos Berkas (Shortlisted), Ditinjau (Reviewed), atau Wawancara (Interview)
        $allowedStatuses = ['reviewed', 'shortlisted', 'interview', 'accepted'];
        $currentAppStatus = strtolower($this->application->status ?? '');

        if (!$existingAttempt && !in_array($currentAppStatus, $allowedStatuses)) {
            session()->flash('error', 'Anda belum dapat mengikuti ujian online. Lamaran Anda masih dalam antrean verifikasi berkas oleh tim rekruter.');
            return redirect()->route('profile', ['tab' => 'riwayat']);
        }

        $this->testId = $this->test->id;

        $categoryName = strtolower($this->test->category?->name ?? '');
        $testTitle    = strtolower($this->test->title ?? '');
        if (str_contains($categoryName, 'papi') || str_contains($testTitle, 'papi')) {
            $this->questionsPerPage = 5;
        } else {
            $this->questionsPerPage = 1;
        }

        // Cek apakah sudah ada attempt sebelumnya
        $existingAttempt = TestAttempt::with(['answers'])
            ->where('job_application_id', $this->application->id)
            ->where('test_id', $this->test->id)
            ->latest('id')
            ->first();

        if ($existingAttempt) {
            $this->attempt = $existingAttempt;
            $this->attemptId = $existingAttempt->id;

            if ($existingAttempt->participant_name) {
                $this->participantName = $existingAttempt->participant_name;
            }
            if ($existingAttempt->participant_age) {
                $this->participantAge = $existingAttempt->participant_age;
            }
            if ($existingAttempt->participant_gender) {
                $this->participantGender = $existingAttempt->participant_gender;
            }
            if ($existingAttempt->test_date) {
                $this->testDate = Carbon::parse($existingAttempt->test_date)->toDateString();
            }

            $this->violationsCount = (int) ($existingAttempt->violations_count ?? 0);
            $this->violationLogs = is_array($existingAttempt->violation_logs)
                ? $existingAttempt->violation_logs
                : (json_decode($existingAttempt->violation_logs ?? '[]', true) ?: []);

            if (in_array($existingAttempt->status, ['completed', 'passed', 'failed'])) {
                $this->testState = 'completed';
                $this->loadQuestionsAndAnswers();
            } elseif ($existingAttempt->status === 'in_progress') {
                // Hitung sisa waktu
                $this->selectedPkg = $existingAttempt->package_id ?: 1;
                $durationSec = ($existingAttempt->duration_minutes ?: ($this->test->duration_minutes ?: 60)) * 60;
                $elapsedSec = Carbon::parse($existingAttempt->started_at)->diffInSeconds(now());
                $remaining = $durationSec - $elapsedSec;

                $this->loadQuestionsAndAnswers();

                if ($remaining <= 0) {
                    $this->finishTestAuto();
                } else {
                    $this->timeRemainingSeconds = (int) $remaining;
                    $this->testState = 'taking';
                }
            }
        }
    }

    public function startTest($packageId = null)
    {
        if ($packageId) {
            $this->selectedPkg = (int) $packageId;
        }

        $user = Auth::user();
        if (!$user) return;
        $applicantProfile = $user->applicantProfile;

        // Ambil otomatis dari data pribadi / profil pelamar tanpa membebani pelamar dengan validasi manual
        if (empty($this->participantName)) {
            $this->participantName = $applicantProfile?->full_name ?: ($user->name ?: 'Pelamar');
        }

        if (empty($this->participantGender)) {
            $rawGender = strtolower($applicantProfile?->gender ?? '');
            $this->participantGender = (str_contains($rawGender, 'perempuan') || str_contains($rawGender, 'wanita') || $rawGender === 'female') ? 'female' : 'male';
        }

        if (empty($this->participantAge)) {
            $this->participantAge = $applicantProfile?->birth_date ? Carbon::parse($applicantProfile->birth_date)->age : 25;
        }
        if (!$this->participantAge || $this->participantAge < 10) {
            $this->participantAge = 25;
        }

        if (empty($this->testDate)) {
            $this->testDate = now()->toDateString();
        }

        try {
            DB::beginTransaction();

            // Cek apakah sudah ada attempt yang tersimpan untuk pelamar & ujian ini
            $existingAttempt = TestAttempt::where('job_application_id', $this->application->id)
                ->where('test_id', $this->test->id)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($existingAttempt) {
                $this->attempt = $existingAttempt;
                $this->attemptId = $existingAttempt->id;
                $this->selectedPkg = $existingAttempt->package_id ?: $this->selectedPkg;
                $this->violationsCount = (int) ($existingAttempt->violations_count ?? 0);
                $this->violationLogs = is_array($existingAttempt->violation_logs)
                    ? $existingAttempt->violation_logs
                    : (json_decode($existingAttempt->violation_logs ?? '[]', true) ?: []);

                if (in_array($existingAttempt->status, ['completed', 'passed', 'failed'])) {
                    $this->testState = 'completed';
                    DB::commit();
                    return;
                }

                if ($existingAttempt->status === 'in_progress') {
                    $existingAttempt->update([
                        'participant_name'   => $this->participantName,
                        'participant_age'    => (int) $this->participantAge,
                        'participant_gender' => $this->participantGender,
                        'test_date'          => $this->testDate,
                    ]);

                    $durationSec = ($existingAttempt->duration_minutes ?: ($this->test->duration_minutes ?: 60)) * 60;
                    $elapsedSec = Carbon::parse($existingAttempt->started_at)->diffInSeconds(now());
                    $remaining = $durationSec - $elapsedSec;

                    $this->loadQuestionsAndAnswers();

                    if ($remaining <= 0) {
                        $this->finishTestAuto();
                    } else {
                        $this->timeRemainingSeconds = (int) $remaining;
                        $this->testState = 'taking';
                        $this->dispatch('test-started', ['remaining' => $this->timeRemainingSeconds]);
                    }

                    DB::commit();
                    return;
                }
            }

            // Bersihkan attempt kosong (in_progress tanpa jawaban) jika ada sebelum membuat attempt baru
            TestAttempt::where('job_application_id', $this->application->id)
                ->where('test_id', $this->test->id)
                ->where('status', 'in_progress')
                ->whereDoesntHave('answers')
                ->delete();

            // Ambil seluruh kumpulan bank soal yang tersedia untuk ujian ini
            $pool = $this->test->questions()->with('options')->get();
            if ($pool->isEmpty()) {
                $pool = QuestionBank::with('options')
                    ->where('category_id', $this->test->category_id)
                    ->get();
            }

            if ($pool->isEmpty()) {
                DB::rollBack();
                session()->flash('error', 'Paket ujian belum memiliki butir soal yang tersedia. Silakan hubungi tim rekruter.');
                return;
            }

            // Pengacakan atau pengurutan awal
            if ($this->test->is_random) {
                $pool = $pool->shuffle();
            } else {
                $pool = $pool->sortBy(function ($q) {
                    return $q->pivot?->order_number ?? ($q->metadata['number'] ?? $q->id);
                })->values();
            }

            // Tentukan jumlah soal dan durasi berdasarkan paket yang dipilih (FAT) atau pengaturan standar ujian
            $isFat = $this->isFatTest();
            $packages = $this->getPackageDefinitions();
            $pkg = $packages[$this->selectedPkg] ?? $packages[1];

            if ($isFat) {
                $targetCount = min($pkg['questions'], $pool->count());
                $durationMinutes = (int) $pkg['duration'];
                $selectedQuestions = $this->selectQuestionsForAttempt($pool, $targetCount);
                $packageId = $pkg['id'];
            } else {
                $targetCount = $this->test->total_questions > 0 ? min($this->test->total_questions, $pool->count()) : $pool->count();
                $durationMinutes = (int) ($this->test->duration_minutes ?: 60);
                $selectedQuestions = $this->selectQuestionsForAttempt($pool, $targetCount);
                $packageId = null;
            }

            $questionIds = $selectedQuestions->pluck('id')->values()->all();

            $attempt = TestAttempt::create([
                'user_id'            => $user->id,
                'attempt_type'       => 'applicant',
                'participant_name'   => $this->participantName,
                'participant_age'    => (int) $this->participantAge,
                'participant_gender' => $this->participantGender,
                'test_date'          => $this->testDate,
                'job_application_id' => $this->application->id,
                'test_id'            => $this->test->id,
                'package_id'         => $packageId,
                'duration_minutes'   => $durationMinutes,
                'question_ids'       => $questionIds,
                'started_at'         => now(),
                'status'             => 'in_progress',
            ]);

            $this->attempt = $attempt;
            $this->attemptId = $attempt->id;
            $this->questions = $selectedQuestions->values()->toArray();
            $this->timeRemainingSeconds = $durationMinutes * 60;
            $this->testState = 'taking';

            $this->dispatch('test-started', ['remaining' => $this->timeRemainingSeconds]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memulai ujian: ' . $e->getMessage());
        }
    }

    public function loadQuestionsAndAnswers()
    {
        // 1. Jika attempt sudah ada dan memiliki question_ids, muat hanya soal-soal tersebut sesuai urutan tersimpan
        if ($this->attempt && !empty($this->attempt->question_ids)) {
            $qIds = (array) $this->attempt->question_ids;
            $questionsMap = QuestionBank::with('options')
                ->whereIn('id', $qIds)
                ->get()
                ->keyBy('id');

            $ordered = collect();
            foreach ($qIds as $qid) {
                if (isset($questionsMap[$qid])) {
                    $ordered->push($questionsMap[$qid]);
                }
            }
            $linkedQuestions = $ordered;
        } else {
            // Ambil soal yang terikat pada test atau fallback ke category
            $linkedQuestions = $this->test->questions()->with('options')->get();

            if ($linkedQuestions->isEmpty()) {
                $query = QuestionBank::with('options')
                    ->where('category_id', $this->test->category_id);

                if ($this->test->is_random) {
                    $query->inRandomOrder();
                } else {
                    $query->orderBy('id', 'asc');
                }

                if ($this->test->total_questions > 0) {
                    $query->take($this->test->total_questions);
                }

                $linkedQuestions = $query->get();
            }

            if ($this->test->is_random) {
                $linkedQuestions = $linkedQuestions->shuffle();
            } else {
                $linkedQuestions = $linkedQuestions->sortBy(function ($q) {
                    return $q->pivot?->order_number ?? ($q->metadata['number'] ?? $q->id);
                })->values();
            }
        }

        $isPapi = ($this->test->category && str_contains(strtolower($this->test->category->name), 'papi'))
            || str_contains(strtolower($this->test->title), 'papi')
            || $linkedQuestions->contains('question_type', 'papi_kostick');

        $isDisc = ($this->test->category && str_contains(strtolower($this->test->category->name), 'disc'))
            || str_contains(strtolower($this->test->title), 'disc')
            || $linkedQuestions->contains('question_type', 'disc');

        if ($isPapi) {
            $this->questionsPerPage = 5;
            $linkedQuestions = $linkedQuestions->sortBy(function ($q) {
                return $q->metadata['number'] ?? $q->id;
            })->values();
        } elseif ($isDisc) {
            $this->questionsPerPage = 1;
            $linkedQuestions = $linkedQuestions->sortBy(function ($q) {
                return $q->metadata['number'] ?? $q->id;
            })->values();
        } else {
            $this->questionsPerPage = 1;
        }

        $this->questions = $linkedQuestions->values()->toArray();

        // Load existing answers jika ada
        if ($this->attemptId) {
            $savedAnswers = TestAnswer::where('attempt_id', $this->attemptId)->get();
            foreach ($savedAnswers as $ans) {
                if ($ans->answer_type === 'most') {
                    $this->answers[$ans->question_id]['most'] = $ans->option_id;
                } elseif ($ans->answer_type === 'least') {
                    $this->answers[$ans->question_id]['least'] = $ans->option_id;
                } elseif ($ans->option_id) {
                    $this->answers[$ans->question_id] = $ans->option_id;
                } elseif ($ans->essay_answer !== null) {
                    $firstEssay = collect($this->questions)->firstWhere('question_type', 'essay');
                    if ($firstEssay && $ans->question_id == $firstEssay['id']) {
                        if (preg_match('/(?:^|\n\n)\[Tautan Lampiran\/Video\]:\s*(https?:\/\/[^\s]+)/i', $ans->essay_answer, $matches)) {
                            $this->essayLink = $matches[1];
                            $cleanText = trim(str_replace($matches[0], '', $ans->essay_answer));
                            $this->answers[$ans->question_id] = $cleanText;
                        } else {
                            $this->answers[$ans->question_id] = $ans->essay_answer;
                        }
                    } else {
                        $this->answers[$ans->question_id] = $ans->essay_answer;
                    }
                }

                // Load attachment dari soal essay pertama yang punya file
                if ($ans->attachment_url && !$this->essayAttachment) {
                    $this->essayAttachment = [
                        'url' => $ans->attachment_url,
                        'name' => $ans->attachment_name ?: 'Lampiran File',
                        'size' => $ans->attachment_size ?: 0,
                        'question_id' => $ans->question_id,
                    ];
                }
            }
        }
    }

    public function selectQuestion($index, $currentQuestionId = null, $currentSelectedOpt = null)
    {
        if (function_exists('store')) {
            store($this)->set('skipRender', false);
        }

        if ($currentQuestionId && $currentSelectedOpt) {
            if (!isset($this->answers[$currentQuestionId]) || $this->answers[$currentQuestionId] != $currentSelectedOpt) {
                $question = collect($this->questions)->firstWhere('id', $currentQuestionId);
                $isMc = $question && in_array($question['question_type'] ?? '', ['multiple_choice', 'papi_kostick']);

                if ($isMc) {
                    $this->saveAnswer($currentQuestionId, $currentSelectedOpt);
                }
            }
        }

        if (isset($this->questions[$index])) {
            $this->currentQuestionIndex = $index;
            $this->currentPage = (int) floor($index / max(1, $this->questionsPerPage));
        }
    }

    /**
     * Selaraskan indeks server ke soal yang benar-benar sedang tampil di browser.
     * Bila snapshot Livewire sempat tertimpa respons request yang datang terlambat,
     * satu klik pengguna tetap dihitung satu langkah dari soal yang ia lihat
     * (bukan melompati soal). Aman dipanggil saat soal sudah sinkron: langsung return.
     */
    protected function alignToClientQuestion($questionId = null): void
    {
        if (!$questionId) {
            return;
        }

        $currentId = $this->questions[$this->currentQuestionIndex]['id'] ?? null;
        if ($currentId == $questionId) {
            return;
        }

        $index = array_search($questionId, array_column($this->questions, 'id'));
        if ($index === false) {
            return;
        }

        $this->currentQuestionIndex = (int) $index;
        $this->currentPage = (int) floor($index / max(1, $this->questionsPerPage));
    }

    public function nextQuestion($currentQuestionId = null, $currentSelectedOpt = null)
    {
        $this->alignToClientQuestion($currentQuestionId);

        if (function_exists('store')) {
            store($this)->set('skipRender', false);
        }

        if ($currentQuestionId && $currentSelectedOpt) {
            if (!isset($this->answers[$currentQuestionId]) || $this->answers[$currentQuestionId] != $currentSelectedOpt) {
                $question = collect($this->questions)->firstWhere('id', $currentQuestionId);
                $isMc = $question && in_array($question['question_type'] ?? '', ['multiple_choice', 'papi_kostick']);

                if ($isMc) {
                    $this->saveAnswer($currentQuestionId, $currentSelectedOpt);
                }
            }
        }

        if ($this->questionsPerPage <= 1) {
            if ($this->currentQuestionIndex < count($this->questions) - 1) {
                $this->currentQuestionIndex++;
                $this->currentPage = $this->currentQuestionIndex;
            }
        } else {
            $totalPages = (int) ceil(count($this->questions) / $this->questionsPerPage);
            if ($this->currentPage < $totalPages - 1) {
                $this->currentPage++;
                $this->currentQuestionIndex = $this->currentPage * $this->questionsPerPage;
            } elseif ($this->currentQuestionIndex < count($this->questions) - 1) {
                $this->currentQuestionIndex++;
            }
        }
    }

    public function prevQuestion($currentQuestionId = null, $currentSelectedOpt = null)
    {
        $this->alignToClientQuestion($currentQuestionId);

        if (function_exists('store')) {
            store($this)->set('skipRender', false);
        }

        if ($currentQuestionId && $currentSelectedOpt) {
            if (!isset($this->answers[$currentQuestionId]) || $this->answers[$currentQuestionId] != $currentSelectedOpt) {
                $question = collect($this->questions)->firstWhere('id', $currentQuestionId);
                $isMc = $question && in_array($question['question_type'] ?? '', ['multiple_choice', 'papi_kostick']);

                if ($isMc) {
                    $this->saveAnswer($currentQuestionId, $currentSelectedOpt);
                }
            }
        }

        if ($this->questionsPerPage <= 1) {
            if ($this->currentQuestionIndex > 0) {
                $this->currentQuestionIndex--;
                $this->currentPage = $this->currentQuestionIndex;
            }
        } else {
            if ($this->currentPage > 0) {
                $this->currentPage--;
                $this->currentQuestionIndex = $this->currentPage * $this->questionsPerPage;
            } elseif ($this->currentQuestionIndex > 0) {
                $this->currentQuestionIndex--;
            }
        }
    }

    protected function syncCurrentQuestionAnswer()
    {
        // Seluruh jawaban awal sudah dimuat ke $this->answers pada loadQuestionsAndAnswers.
        // Jawaban baru langsung tercatat di $this->answers sehingga tidak perlu query database berulang.
        return;
    }

    /**
     * Khusus multiple choice & PAPI saat opsi diklik:
     * Simpan jawaban ke database di latar belakang tanpa re-render template penuh agar delay cepat.
     */
    public function saveChoiceAnswer($questionId, $optionId)
    {
        $this->saveAnswer($questionId, $optionId);
        $this->skipRender();
    }

    /**
     * Khusus pilihan DISC saat opsi diklik:
     * Simpan jawaban ke database di latar belakang tanpa re-render template penuh agar delay cepat.
     */
    public function saveDiscAnswer($questionId, $optionId, $discType)
    {
        $this->saveAnswer($questionId, $optionId, null, $discType);
        $this->skipRender();
    }

    /**
     * Catat pelanggaran pengawasan sesi ujian ke database dan memory.
     */
    public function recordViolation($reason = '', $clientCount = null)
    {
        if ($clientCount !== null && (int)$clientCount > $this->violationsCount) {
            $this->violationsCount = (int)$clientCount;
        } else {
            $this->violationsCount++;
        }

        $log = [
            'violation' => $this->violationsCount,
            'reason'    => $reason ?: 'Keluar dari mode layar penuh / berpindah jendela',
            'time'      => now()->toIso8601String(),
        ];
        $this->violationLogs[] = $log;

        if ($this->attemptId) {
            TestAttempt::where('id', $this->attemptId)->update([
                'violations_count' => $this->violationsCount,
                'violation_logs'   => json_encode($this->violationLogs),
            ]);
        }

        if ($this->violationsCount >= 3) {
            $this->finishTest(true, null, null, $this->violationsCount);
        } else {
            $this->skipRender();
        }
    }

    /**
     * Tentukan poin jawaban pilihan ganda.
     * Jika soal punya metadata.option_weights (soal bergradasi/SJT), pakai bobot opsi terpilih.
     * Selain itu, gunakan aturan lama (benar = poin penuh, salah = 0) agar tes lama tidak berubah.
     */
    protected function resolveChoiceScore(array $question, $optionId): float
    {
        if (($question['question_type'] ?? null) === 'multiple_choice') {
            $weights = $question['metadata']['option_weights'] ?? null;
            if (is_string($weights)) {
                $weights = json_decode($weights, true);
            }

            if (is_array($weights) && !empty($weights)) {
                $optIndex = collect($question['options'] ?? [])->search(
                    fn ($opt) => (string) ($opt['id'] ?? '') === (string) $optionId
                );

                if ($optIndex !== false && isset($weights[$optIndex]) && is_numeric($weights[$optIndex])) {
                    return (float) $weights[$optIndex];
                }

                return 0.0;
            }
        }

        $opt = collect($question['options'] ?? [])->firstWhere('id', $optionId);
        $isCorrect = $opt && !empty($opt['is_correct']);

        return $isCorrect ? (float) ($question['points'] ?: 1) : 0.0;
    }

    public function saveAnswer($questionId, $optionId = null, $essayText = null, $discType = null)
    {
        if (!$this->attemptId) return;

        $question = collect($this->questions)->firstWhere('id', $questionId);
        if (!$question) return;

        if ($question['question_type'] === 'disc' && $discType) {
            // Update local Livewire state agar UI re-render dengan benar
            if (!isset($this->answers[$questionId]) || !is_array($this->answers[$questionId])) {
                $this->answers[$questionId] = [];
            }

            $oppositeType = ($discType === 'most') ? 'least' : 'most';

            // ATURAN 1: Satu baris opsi tidak boleh sekaligus Most dan Least
            // Jika opsi ini sebelumnya dipilih di tipe yang berlawanan, hapus dari tipe berlawanan
            if (isset($this->answers[$questionId][$oppositeType]) && $this->answers[$questionId][$oppositeType] == $optionId) {
                $this->answers[$questionId][$oppositeType] = null;
                TestAnswer::where('attempt_id', $this->attemptId)
                    ->where('question_id', $questionId)
                    ->where('answer_type', $oppositeType)
                    ->delete();
            }

            // ATURAN 2: Set nilai terpilih
            $this->answers[$questionId][$discType] = $optionId;

            // Simpan ke database
            TestAnswer::updateOrCreate(
                [
                    'attempt_id' => $this->attemptId,
                    'question_id' => $questionId,
                    'answer_type' => $discType,
                ],
                [
                    'option_id' => $optionId,
                ]
            );
        } elseif ($question['question_type'] === 'multiple_choice' || $question['question_type'] === 'papi_kostick') {
            $this->answers[$questionId] = $optionId;

            TestAnswer::updateOrCreate(
                [
                    'attempt_id' => $this->attemptId,
                    'question_id' => $questionId,
                ],
                [
                    'option_id' => $optionId,
                    'essay_answer' => null,
                    'score' => $this->resolveChoiceScore($question, $optionId),
                ]
            );
        } elseif ($question['question_type'] === 'essay') {
            $firstEssayQuestion = collect($this->questions)->firstWhere('question_type', 'essay');
            $essayToSave = $essayText;

            // Jika soal ini adalah soal essay pertama dan ada tautan eksternal, sematkan tautan
            if ($firstEssayQuestion && $questionId == $firstEssayQuestion['id'] && !empty($this->essayLink)) {
                $base = trim($essayText ?? '');
                $essayToSave = $base !== ''
                    ? $base . "\n\n[Tautan Lampiran/Video]: " . trim($this->essayLink)
                    : "[Tautan Lampiran/Video]: " . trim($this->essayLink);
            }

            TestAnswer::updateOrCreate(
                [
                    'attempt_id' => $this->attemptId,
                    'question_id' => $questionId,
                ],
                [
                    'option_id' => null,
                    'essay_answer' => $essayToSave,
                    'score' => null, // Dinilai oleh HR
                ]
            );
        }
    }

    public function submitEssayAnswer($questionId)
    {
        $text = $this->answers[$questionId] ?? '';
        $this->saveAnswer($questionId, null, $text);
    }

    /**
     * Otomatis simpan file begitu dipilih oleh pelamar tanpa perlu menekan tombol upload
     */
    public function updatedEssayFile()
    {
        $this->uploadEssayAttachment();
    }

    /**
     * Handle upload file attachment jawaban essay ke Storage Laravel (Max 10MB, All File Types)
     */
    public function uploadEssayAttachment()
    {
        $this->validate([
            'essayFile' => 'required|file|max:10240', // 10MB max
        ], [
            'essayFile.required' => 'Pilih file terlebih dahulu.',
            'essayFile.file' => 'File tidak valid.',
            'essayFile.max' => 'Ukuran file maksimal adalah 10MB.',
        ]);

        if (!$this->essayFile || !$this->attemptId) return;

        // Simpan ke soal essay pertama
        $firstEssayQuestion = collect($this->questions)->firstWhere('question_type', 'essay');
        if (!$firstEssayQuestion) return;
        $questionId = $firstEssayQuestion['id'];

        try {
            $path = $this->essayFile->store('test-answers', 'public');
            $fileUrl = asset('storage/' . $path);
            $originalName = $this->essayFile->getClientOriginalName();
            $fileSize = $this->essayFile->getSize();

            $finalAnswer = $this->answers[$questionId] ?? null;
            if (!empty($this->essayLink)) {
                $base = trim($finalAnswer ?? '');
                $finalAnswer = $base !== ''
                    ? $base . "\n\n[Tautan Lampiran/Video]: " . trim($this->essayLink)
                    : "[Tautan Lampiran/Video]: " . trim($this->essayLink);
            }

            TestAnswer::updateOrCreate(
                [
                    'attempt_id' => $this->attemptId,
                    'question_id' => $questionId,
                ],
                [
                    'attachment_url' => $fileUrl,
                    'attachment_name' => $originalName,
                    'attachment_size' => $fileSize,
                    'essay_answer' => $finalAnswer,
                ]
            );

            $this->essayAttachment = [
                'url' => $fileUrl,
                'name' => $originalName,
                'size' => $fileSize,
                'question_id' => $questionId,
            ];

            $this->essayFile = null;
            session()->flash('message', 'File berhasil diunggah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage());
        }
    }

    /**
     * Hapus lampiran file essay
     */
    public function removeEssayAttachment()
    {
        if (!$this->attemptId || !$this->essayAttachment) return;

        $questionId = $this->essayAttachment['question_id'] ?? null;
        if (!$questionId) return;

        $ans = TestAnswer::where('attempt_id', $this->attemptId)
            ->where('question_id', $questionId)
            ->first();

        if ($ans && $ans->attachment_url) {
            $relativePath = str_replace(asset('storage/'), '', $ans->attachment_url);
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($relativePath);
            }
        }

        TestAnswer::where('attempt_id', $this->attemptId)
            ->where('question_id', $questionId)
            ->update([
                'attachment_url' => null,
                'attachment_name' => null,
                'attachment_size' => null,
            ]);

        $this->essayAttachment = null;
        $this->essayFile = null;

        session()->flash('message', 'Lampiran file berhasil dihapus.');
    }

    /**
     * Simpan form khusus link video/drive/eksternal
     */
    public function saveEssayLink()
    {
        if (!$this->attemptId) return;

        $firstEssayQuestion = collect($this->questions)->firstWhere('question_type', 'essay');
        if (!$firstEssayQuestion) return;
        $questionId = $firstEssayQuestion['id'];

        $link = trim($this->essayLink);
        if (!empty($link)) {
            if (!preg_match('#^https?://#i', $link)) {
                $link = 'https://' . $link;
                $this->essayLink = $link;
            }

            if (!filter_var($link, FILTER_VALIDATE_URL)) {
                $this->addError('essayLink', 'Format tautan URL tidak valid. Pastikan diawali dengan http:// atau https://');
                return;
            }
        }

        $this->resetErrorBag('essayLink');
        $currentAnswer = $this->answers[$questionId] ?? '';
        $this->persistEssayAnswerWithLink($questionId, $currentAnswer, $link);

        if (!empty($link)) {
            session()->flash('link_message', 'Tautan berhasil disimpan.');
        }
    }

    /**
     * Hapus form khusus link
     */
    public function removeEssayLink()
    {
        $this->essayLink = '';
        $this->resetErrorBag('essayLink');

        if (!$this->attemptId) return;

        $firstEssayQuestion = collect($this->questions)->firstWhere('question_type', 'essay');
        if (!$firstEssayQuestion) return;
        $questionId = $firstEssayQuestion['id'];

        $currentAnswer = $this->answers[$questionId] ?? '';
        $this->persistEssayAnswerWithLink($questionId, $currentAnswer, '');

        session()->flash('link_message', 'Tautan berhasil dihapus.');
    }

    /**
     * Helper untuk persistensi jawaban essay dengan tautan
     */
    protected function persistEssayAnswerWithLink($questionId, $textAnswer, $link)
    {
        $text = trim($textAnswer ?? '');
        $text = trim(preg_replace('/(?:^|\n\n)\[Tautan Lampiran\/Video\]:\s*https?:\/\/[^\s]+/i', '', $text));
        $this->answers[$questionId] = $text;

        $finalAnswer = $text;
        if (!empty($link)) {
            $finalAnswer = $text !== ''
                ? $text . "\n\n[Tautan Lampiran/Video]: " . trim($link)
                : "[Tautan Lampiran/Video]: " . trim($link);
        }

        TestAnswer::updateOrCreate(
            [
                'attempt_id' => $this->attemptId,
                'question_id' => $questionId,
            ],
            [
                'option_id' => null,
                'essay_answer' => $finalAnswer !== '' ? $finalAnswer : null,
            ]
        );
    }

    /**
     * Dapatkan daftar butir soal yang belum dijawab atau belum lengkap
     */
    public function getUnansweredQuestions(): array
    {
        $unanswered = [];
        foreach ($this->questions as $index => $q) {
            $qId = $q['id'];
            $qType = $q['question_type'] ?? 'multiple_choice';
            $qNum = $index + 1;

            if ($qType === 'disc') {
                $hasMost = !empty($this->answers[$qId]['most']);
                $hasLeast = !empty($this->answers[$qId]['least']);

                if (!$hasMost && !$hasLeast) {
                    $unanswered[] = [
                        'index' => $index,
                        'number' => $qNum,
                        'reason' => 'P & K belum dipilih',
                        'status' => 'empty',
                    ];
                } elseif (!$hasMost) {
                    $unanswered[] = [
                        'index' => $index,
                        'number' => $qNum,
                        'reason' => 'P (Paling) belum dipilih',
                        'status' => 'partial',
                    ];
                } elseif (!$hasLeast) {
                    $unanswered[] = [
                        'index' => $index,
                        'number' => $qNum,
                        'reason' => 'K (Kurang) belum dipilih',
                        'status' => 'partial',
                    ];
                }
            } elseif ($qType === 'multiple_choice' || $qType === 'papi_kostick') {
                $hasAnswer = isset($this->answers[$qId]) && $this->answers[$qId] !== null && $this->answers[$qId] !== '';
                if (!$hasAnswer) {
                    $unanswered[] = [
                        'index' => $index,
                        'number' => $qNum,
                        'reason' => 'Pernyataan belum dipilih',
                        'status' => 'empty',
                    ];
                }
            } elseif ($qType === 'essay') {
                $firstEssay = collect($this->questions)->firstWhere('question_type', 'essay');
                $isFirstEssay = ($firstEssay && $firstEssay['id'] == $qId);
                $hasText = !empty($this->answers[$qId]) && trim($this->answers[$qId]) !== '';
                $hasFileOrLink = $isFirstEssay && ($this->essayAttachment || !empty($this->essayLink));

                if (!$hasText && !$hasFileOrLink) {
                    $unanswered[] = [
                        'index' => $index,
                        'number' => $qNum,
                        'reason' => 'Jawaban essay belum diisi',
                        'status' => 'empty',
                    ];
                }
            }
        }

        return $unanswered;
    }

    public function openFinishModal($currentQuestionId = null, $currentSelectedOpt = null)
    {
        if (function_exists('store')) {
            store($this)->set('skipRender', false);
        }

        // Jika ada jawaban yang sedang dipilih pada nomor aktif, simpan segera
        if ($currentQuestionId && $currentSelectedOpt) {
            $question = collect($this->questions)->firstWhere('id', $currentQuestionId);
            $isMc = $question && ($question['question_type'] ?? '') === 'multiple_choice';

            if ($isMc) {
                $this->saveAnswer($currentQuestionId, $currentSelectedOpt);
            }
        }

        // Sinkronisasi seluruh jawaban tersimpan dari database agar completedCount 100% mutakhir
        if ($this->attemptId) {
            $dbAnswers = TestAnswer::where('attempt_id', $this->attemptId)->get();
            foreach ($dbAnswers as $ans) {
                if ($ans->answer_type) {
                    $this->answers[$ans->question_id][$ans->answer_type] = $ans->option_id;
                } elseif ($ans->option_id) {
                    $this->answers[$ans->question_id] = $ans->option_id;
                } elseif ($ans->essay_answer) {
                    $this->answers[$ans->question_id] = $ans->essay_answer;
                }
            }
        }

        $this->showConfirmModal = true;
        $this->dispatch('open-confirm-modal');
    }

    public function closeFinishModal()
    {
        $this->showConfirmModal = false;
        $this->dispatch('close-confirm-modal');
    }

    public function goToFirstUnanswered()
    {
        $this->showConfirmModal = false;
        $unanswered = $this->getUnansweredQuestions();
        if (!empty($unanswered)) {
            $this->currentQuestionIndex = $unanswered[0]['index'];
            $this->currentPage = (int) floor($this->currentQuestionIndex / max(1, $this->questionsPerPage));
            $this->syncCurrentQuestionAnswer();
        }
    }

    public function finishTest($isAuto = false, $currentQuestionId = null, $currentSelectedOpt = null, $violationsCount = null)
    {
        if (function_exists('store')) {
            store($this)->set('skipRender', false);
        }

        if (!$this->attemptId) return;

        if ($violationsCount !== null && (int)$violationsCount > $this->violationsCount) {
            $this->violationsCount = (int)$violationsCount;
        }

        // Simpan jawaban aktif nomor terakhir jika dikirimkan bersamaan
        if ($currentQuestionId && $currentSelectedOpt) {
            $question = collect($this->questions)->firstWhere('id', $currentQuestionId);
            $isMc = $question && ($question['question_type'] ?? '') === 'multiple_choice';

            if ($isMc) {
                $this->saveAnswer($currentQuestionId, $currentSelectedOpt);
            }
        }

        // Pastikan simpan jawaban essay yang sedang aktif jika ada
        foreach ($this->questions as $q) {
            if ($q['question_type'] === 'essay' && isset($this->answers[$q['id']])) {
                $this->saveAnswer($q['id'], null, $this->answers[$q['id']]);
            }
        }

        // Sinkronisasi mutakhir seluruh jawaban dari database
        $dbAnswers = TestAnswer::where('attempt_id', $this->attemptId)->get();
        foreach ($dbAnswers as $ans) {
            if ($ans->answer_type) {
                $this->answers[$ans->question_id][$ans->answer_type] = $ans->option_id;
            } elseif ($ans->option_id) {
                $this->answers[$ans->question_id] = $ans->option_id;
            } elseif ($ans->essay_answer) {
                $this->answers[$ans->question_id] = $ans->essay_answer;
            }
        }

        $isDisc = collect($this->questions)->contains('question_type', 'disc');
        $isPapi = collect($this->questions)->contains('question_type', 'papi_kostick');
        $isPsychometric = $isDisc || $isPapi;

        // Jika diselesaikan manual oleh pelamar pada tes psikometri (DISC / PAPI), wajib terisi lengkap
        if (!$isAuto && $isPsychometric) {
            $unanswered = $this->getUnansweredQuestions();
            if (!empty($unanswered)) {
                $this->showConfirmModal = false;
                // Arahkan kursor/tampilan soal ke butir pertama yang belum lengkap
                $this->currentQuestionIndex = $unanswered[0]['index'];
                $this->currentPage = (int) floor($this->currentQuestionIndex / max(1, $this->questionsPerPage));
                $this->syncCurrentQuestionAnswer();

                $count = count($unanswered);
                $details = array_map(function ($item) {
                    return "No. {$item['number']} ({$item['reason']})";
                }, array_slice($unanswered, 0, 5));

                $detailStr = implode(', ', $details);
                if ($count > 5) {
                    $detailStr .= ' ...dan ' . ($count - 5) . ' butir lainnya';
                }

                $errorMessage = $isDisc
                    ? "Tes DISC belum dapat diselesaikan karena masih ada {$count} butir soal yang belum lengkap. Setiap nomor wajib memiliki 1 pilihan P (Paling) dan 1 pilihan K (Kurang). Rincian: [{$detailStr}]."
                    : "Tes PAPI Kostick belum dapat diselesaikan karena masih ada {$count} butir soal yang belum dipilih. Rincian: [{$detailStr}]." ;

                session()->flash('test_error', $errorMessage);
                return;
            }
        }

        $attempt = TestAttempt::with(['answers.question'])->findOrFail($this->attemptId);

        if (in_array($attempt->status, ['completed', 'passed', 'failed'])) {
            $this->showConfirmModal = false;
            $this->attempt = TestAttempt::with(['discTestResult.discProfile', 'papiTestResult'])->find($attempt->id) ?? $attempt;
            $this->testState = 'completed';
            $this->dispatch('test-completed');
            return;
        }

        $startedAt = Carbon::parse($attempt->started_at);
        $finishedAt = now();
        $durationSeconds = (int) round($startedAt->diffInSeconds($finishedAt));

        // Hitung skor pilihan ganda
        $objectiveScore = TestAnswer::where('attempt_id', $attempt->id)
            ->whereHas('question', function ($q) {
                $q->where('question_type', 'multiple_choice');
            })
            ->sum('score');

        $hasEssay = collect($this->questions)->contains('question_type', 'essay');
        $hasDisc = collect($this->questions)->contains('question_type', 'disc');
        $hasPapi = collect($this->questions)->contains('question_type', 'papi_kostick');
        $hasMultipleChoice = collect($this->questions)->contains('question_type', 'multiple_choice');

        if ($hasDisc) {
            try {
                $discService = app(DiscCalculatorService::class);
                $discService->calculate($attempt);
            } catch (\Exception $e) {
                // ignore or log
            }
        }

        if ($hasPapi) {
            try {
                $papiService = app(PapiKostickCalculatorService::class);
                $papiService->calculate($attempt);
            } catch (\Exception $e) {
                // ignore or log
            }
        }

        $passingScore = (float) $this->test->passing_score;

        // Hitung persentase nilai pilihan ganda berdasarkan butir soal yang diujikan
        $maxObjectivePoints = collect($this->questions)->where('question_type', 'multiple_choice')->sum(function ($q) {
            return $q['points'] ?? 1;
        });
        if ($maxObjectivePoints <= 0) {
            $maxObjectivePoints = count($this->questions) ?: 1;
        }

        $scorePercentage = ($objectiveScore / $maxObjectivePoints) * 100;

        if (($hasDisc || $hasPapi) && !$hasEssay && !$hasMultipleChoice) {
            // Tes Kepribadian DISC / PAPI Kostick murni tidak memiliki benar/salah ataupun KKM
            $status = 'completed';
            $totalScore = 100; // Profiling complete
        } elseif ($hasEssay) {
            // Masih perlu dinilai reviewer
            $status = 'completed';
            $totalScore = $objectiveScore;
        } elseif ($passingScore <= 0) {
            // Ujian tanpa batas KKM (KKM 0 = selesai / lolos)
            $status = 'completed';
            $totalScore = round($scorePercentage, 2);
        } else {
            $totalScore = round($scorePercentage, 2);
            $status = ($scorePercentage >= $passingScore) ? 'passed' : 'failed';
        }

        // Dual-Gate Passing Rule: wajib lolos KKM total DAN ambang Etika & Integritas (70).
        if ($status === 'passed' && AspectEvaluationService::calculateAspects($attempt)['failed_ethics']) {
            $status = 'failed';
        }

        $attempt->update([
            'finished_at'      => $finishedAt,
            'duration'         => $durationSeconds,
            'objective_score'  => $objectiveScore,
            'total_score'      => $totalScore,
            'status'           => $status,
            'violations_count' => $this->violationsCount,
            'violation_logs'   => is_array($this->violationLogs) ? json_encode($this->violationLogs) : $this->violationLogs,
        ]);

        // Jika ujian PG murni lulus KKM, otomatis update status lamaran ke 'Shortlisted'
        if ($status === 'passed' && $this->application) {
            if (in_array(strtolower($this->application->status), ['submitted', 'reviewed', 'pending'])) {
                $this->application->update([
                    'status' => 'Shortlisted',
                    'notes'  => 'Lolos Ujian Online dengan skor ' . number_format($totalScore, 1) . ' / KKM: ' . number_format($passingScore, 0) . '. Siap untuk tahap wawancara.',
                ]);

                \App\Models\ApplicationStatusHistory::create([
                    'job_applications_id' => $this->application->id,
                    'status'              => 'Shortlisted',
                    'notes'               => 'Lolos Ujian Online dengan skor ' . number_format($totalScore, 1) . '.',
                    'changed_by'          => Auth::id() ?? 1,
                    'changed_at'          => now(),
                ]);
            }
        }

        $this->showConfirmModal = false;
        $this->attempt = TestAttempt::with(['discTestResult.discProfile', 'papiTestResult'])->find($attempt->id) ?? $attempt;
        $this->testState = 'completed';
        $this->dispatch('test-completed');
    }

    public function finishTestAuto($violationsCount = null)
    {
        $this->finishTest(true, null, null, $violationsCount);
    }

    public function render()
    {
        $discResult = null;
        $papiResult = null;
        $testProgress = [];
        $nextTest = null;
        $unansweredQuestions = [];

        // Optimasi: Hanya muat hasil psikometri & kalkulasi relasi sequence saat ujian sudah selesai (completed)
        if ($this->testState === 'completed') {
            if ($this->attemptId) {
                $discResult = DiscTestResult::with('discProfile')->where('test_attempt_id', $this->attemptId)->first();
                $papiResult = PapiTestResult::where('test_attempt_id', $this->attemptId)->first();
            }

            if ($this->application && $this->application->job) {
                $freshApplication = \App\Models\JobApplication::with(['testAttempts', 'job'])
                    ->find($this->application->id);
                if ($freshApplication) {
                    $testProgress = TestSequenceService::buildTestProgress($freshApplication);
                    if ($this->test) {
                        $nextTest = TestSequenceService::getNextTest($freshApplication, $this->test);
                    }
                }
            }
        }

        // Kalkulasi cepat butir soal terisi saat ujian berlangsung tanpa query berulang
        $totalQuestions = count($this->questions);
        $completedCount = 0;
        foreach ($this->questions as $q) {
            $qid = $q['id'];
            if (($q['question_type'] ?? '') === 'disc') {
                if (!empty($this->answers[$qid]['most']) && !empty($this->answers[$qid]['least'])) {
                    $completedCount++;
                }
            } elseif (isset($this->answers[$qid]) && $this->answers[$qid] !== null && $this->answers[$qid] !== '') {
                $completedCount++;
            }
        }
        $progressPercent = $totalQuestions > 0 ? round(($completedCount / $totalQuestions) * 100) : 0;

        $questionsPerPage = $this->questionsPerPage;
        $currentPage      = $this->currentPage;
        $totalPages       = max(1, (int) ceil($totalQuestions / $questionsPerPage));
        $pageStart        = $currentPage * $questionsPerPage;
        $pageQuestions    = array_slice($this->questions, $pageStart, $questionsPerPage);

        return view('livewire.applicant.online-test', [
            'application'          => $this->application,
            'test'                 => $this->test,
            'questions'            => $this->questions,
            'pageQuestions'        => $pageQuestions,      // Soal di halaman saat ini
            'pageStart'            => $pageStart,          // Indeks global soal pertama di halaman ini
            'currentPage'          => $currentPage,
            'totalPages'           => $totalPages,
            'questionsPerPage'     => $questionsPerPage,
            'currentQuestion'      => $this->questions[$this->currentQuestionIndex] ?? null,
            'attempt'              => $this->attempt,
            'discResult'           => $discResult,
            'papiResult'           => $papiResult,
            'unansweredQuestions'  => $unansweredQuestions,
            'completedCount'       => $completedCount,
            'totalQuestions'       => $totalQuestions,
            'progressPercent'      => $progressPercent,
            'timeRemainingSeconds' => $this->timeRemainingSeconds,
            'testState'            => $this->testState,
            'testProgress'         => $testProgress,
            'nextTest'             => $nextTest,
            'violationsCount'      => $this->violationsCount,
            'violationLogs'        => $this->violationLogs,
        ])->layout('layouts.app');
    }
}

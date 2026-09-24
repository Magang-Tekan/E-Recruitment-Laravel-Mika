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

    // Lampiran essay yang tersimpan di DB: [ question_id => ['url' => '...', 'name' => '...', 'size' => 12345] ]
    public $essayAttachments = [];

    // Temporary upload per question: [ question_id => Livewire TemporaryUploadedFile ]
    public $essayFiles = [];

    public $timeRemainingSeconds = 0;

    // Biodata Peserta untuk Snapshot Hasil Evaluasi
    public $participantName = '';
    public $participantAge = '';
    public $participantGender = 'male';
    public $testDate = '';

    // Paginasi multi-soal: tampilkan beberapa soal sekaligus (khususnya PAPI Kostick)
    public $currentPage = 0;       // Indeks halaman saat ini (0-based)
    public $questionsPerPage = 5;  // Jumlah soal per halaman

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

            if (in_array($existingAttempt->status, ['completed', 'passed', 'failed'])) {
                $this->testState = 'completed';
            } elseif ($existingAttempt->status === 'in_progress') {
                // Hitung sisa waktu
                $durationSec = ($this->test->duration_minutes ?: 60) * 60;
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

    public function startTest()
    {
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

                    $durationSec = ($this->test->duration_minutes ?: 60) * 60;
                    $elapsedSec = Carbon::parse($existingAttempt->started_at)->diffInSeconds(now());
                    $remaining = $durationSec - $elapsedSec;

                    $this->loadQuestionsAndAnswers();

                    if ($remaining <= 0) {
                        $this->finishTestAuto();
                    } else {
                        $this->timeRemainingSeconds = (int) $remaining;
                        $this->testState = 'taking';
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

            $this->loadQuestionsAndAnswers();

            if (empty($this->questions)) {
                DB::rollBack();
                session()->flash('error', 'Paket ujian belum memiliki butir soal yang tersedia. Silakan hubungi tim rekruter.');
                return;
            }

            $attempt = TestAttempt::create([
                'user_id'            => $user->id,
                'attempt_type'       => 'applicant',
                'participant_name'   => $this->participantName,
                'participant_age'    => (int) $this->participantAge,
                'participant_gender' => $this->participantGender,
                'test_date'          => $this->testDate,
                'job_application_id' => $this->application->id,
                'test_id'            => $this->test->id,
                'started_at'         => now(),
                'status'             => 'in_progress',
            ]);

            $this->attempt = $attempt;
            $this->attemptId = $attempt->id;
            $this->timeRemainingSeconds = ($this->test->duration_minutes ?: 60) * 60;
            $this->testState = 'taking';

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memulai ujian: ' . $e->getMessage());
        }
    }

    public function loadQuestionsAndAnswers()
    {
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

        $isPapi = ($this->test->category && str_contains(strtolower($this->test->category->name), 'papi'))
            || str_contains(strtolower($this->test->title), 'papi')
            || $linkedQuestions->contains('question_type', 'papi_kostick');

        if ($isPapi) {
            $linkedQuestions = $linkedQuestions->sortBy(function ($q) {
                return $q->metadata['number'] ?? $q->id;
            })->values();
        } elseif ($this->test->is_random) {
            $linkedQuestions = $linkedQuestions->shuffle();
        } else {
            $linkedQuestions = $linkedQuestions->sortBy(function ($q) {
                return $q->pivot?->order_number ?? ($q->metadata['number'] ?? $q->id);
            })->values();
        }

        $this->questions = $linkedQuestions->toArray();

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
                    $this->answers[$ans->question_id] = $ans->essay_answer;
                }

                if ($ans->attachment_url) {
                    $this->essayAttachments[$ans->question_id] = [
                        'url' => $ans->attachment_url,
                        'name' => $ans->attachment_name ?: 'Lampiran File',
                        'size' => $ans->attachment_size ?: 0,
                    ];
                }
            }
        }
    }

    public function selectQuestion($index)
    {
        if (isset($this->questions[$index])) {
            $this->currentQuestionIndex = $index;
            $this->currentPage = (int) floor($index / $this->questionsPerPage);
        }
    }

    public function nextQuestion()
    {
        $totalPages = (int) ceil(count($this->questions) / $this->questionsPerPage);
        if ($this->currentPage < $totalPages - 1) {
            $this->currentPage++;
            $this->currentQuestionIndex = $this->currentPage * $this->questionsPerPage;
        } elseif ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function prevQuestion()
    {
        if ($this->currentPage > 0) {
            $this->currentPage--;
            $this->currentQuestionIndex = $this->currentPage * $this->questionsPerPage;
        } elseif ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
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

            // Check correct
            $isCorrect = false;
            $opt = collect($question['options'])->firstWhere('id', $optionId);
            if ($opt && !empty($opt['is_correct'])) {
                $isCorrect = true;
            }

            TestAnswer::updateOrCreate(
                [
                    'attempt_id' => $this->attemptId,
                    'question_id' => $questionId,
                ],
                [
                    'option_id' => $optionId,
                    'essay_answer' => null,
                    'score' => $isCorrect ? ($question['points'] ?: 1) : 0,
                ]
            );
        } elseif ($question['question_type'] === 'essay') {
            TestAnswer::updateOrCreate(
                [
                    'attempt_id' => $this->attemptId,
                    'question_id' => $questionId,
                ],
                [
                    'option_id' => null,
                    'essay_answer' => $essayText,
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
    public function updatedEssayFiles($value, $key)
    {
        $questionId = (int) $key;
        if ($questionId) {
            $this->uploadEssayAttachment($questionId);
        }
    }

    /**
     * Handle upload file attachment jawaban essay ke Storage Laravel (Max 10MB, All File Types)
     */
    public function uploadEssayAttachment($questionId)
    {
        $this->validate([
            'essayFiles.' . $questionId => 'required|file|max:10240', // 10MB max
        ], [
            'essayFiles.' . $questionId . '.required' => 'Pilih file terlebih dahulu.',
            'essayFiles.' . $questionId . '.file' => 'File tidak valid.',
            'essayFiles.' . $questionId . '.max' => 'Ukuran file maksimal adalah 10MB. Untuk file video atau file besar, silakan gunakan tautan Google Drive.',
        ]);

        $file = $this->essayFiles[$questionId] ?? null;
        if (!$file) return;

        try {
            // Simpan ke storage/app/public/test-answers
            $path = $file->store('test-answers', 'public');
            $fileUrl = asset('storage/' . $path);

            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();

            // Simpan ke database
            $answer = TestAnswer::updateOrCreate(
                [
                    'attempt_id' => $this->attemptId,
                    'question_id' => $questionId,
                ],
                [
                    'attachment_url' => $fileUrl,
                    'attachment_name' => $originalName,
                    'attachment_size' => $fileSize,
                    'essay_answer' => $this->answers[$questionId] ?? null,
                ]
            );

            // Update local state
            $this->essayAttachments[$questionId] = [
                'url' => $fileUrl,
                'name' => $originalName,
                'size' => $fileSize,
            ];

            // Reset input temporary file
            unset($this->essayFiles[$questionId]);

            session()->flash('message', 'File berhasil diunggah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage());
        }
    }

    /**
     * Hapus lampiran file essay
     */
    public function removeEssayAttachment($questionId)
    {
        if (!$this->attemptId) return;

        $ans = TestAnswer::where('attempt_id', $this->attemptId)
            ->where('question_id', $questionId)
            ->first();

        if ($ans && $ans->attachment_url) {
            // Hapus file fisik jika ada di local storage
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

        unset($this->essayAttachments[$questionId]);
        unset($this->essayFiles[$questionId]);

        session()->flash('message', 'Lampiran file berhasil dihapus.');
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
                $hasText = !empty($this->answers[$qId]) && trim($this->answers[$qId]) !== '';
                $hasAttachment = !empty($this->essayAttachments[$qId]);
                if (!$hasText && !$hasAttachment) {
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

    public function finishTest($isAuto = false)
    {
        if (!$this->attemptId) return;

        // Pastikan simpan jawaban essay yang sedang aktif jika ada
        foreach ($this->questions as $q) {
            if ($q['question_type'] === 'essay' && isset($this->answers[$q['id']])) {
                $this->saveAnswer($q['id'], null, $this->answers[$q['id']]);
            }
        }

        // Jika diselesaikan manual oleh pelamar, pastikan seluruh jawaban terisi lengkap
        if (!$isAuto) {
            $unanswered = $this->getUnansweredQuestions();
            if (!empty($unanswered)) {
                // Arahkan kursor/tampilan soal ke butir pertama yang belum lengkap
                $this->currentQuestionIndex = $unanswered[0]['index'];

                $count = count($unanswered);
                $details = array_map(function ($item) {
                    return "No. {$item['number']} ({$item['reason']})";
                }, array_slice($unanswered, 0, 5));

                $detailStr = implode(', ', $details);
                if ($count > 5) {
                    $detailStr .= ' ...dan ' . ($count - 5) . ' butir lainnya';
                }

                $isDisc = collect($this->questions)->contains('question_type', 'disc');
                $errorMessage = $isDisc
                    ? "Tes DISC belum dapat diselesaikan karena masih ada {$count} butir soal yang belum lengkap. Setiap nomor wajib memiliki 1 pilihan P (Paling) dan 1 pilihan K (Kurang). Rincian: [{$detailStr}]."
                    : "Ujian belum dapat diselesaikan karena masih ada {$count} butir soal yang belum dijawab. Rincian: [{$detailStr}].";

                session()->flash('test_error', $errorMessage);
                return;
            }
        }

        $attempt = TestAttempt::with(['answers.question'])->findOrFail($this->attemptId);

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
            $totalScore = $objectiveScore;
        } else {
            $totalScore = $objectiveScore;
            $status = ($totalScore >= $passingScore) ? 'passed' : 'failed';
        }

        $attempt->update([
            'finished_at' => $finishedAt,
            'duration' => $durationSeconds,
            'objective_score' => $objectiveScore,
            'total_score' => $totalScore,
            'status' => $status,
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

        $this->attempt = TestAttempt::with(['discTestResult.discProfile', 'papiTestResult'])->find($attempt->id) ?? $attempt;
        $this->testState = 'completed';
    }

    public function finishTestAuto()
    {
        $this->finishTest(true);
    }

    public function render()
    {
        $discResult = null;
        $papiResult = null;
        if ($this->attemptId) {
            $discResult = DiscTestResult::with('discProfile')->where('test_attempt_id', $this->attemptId)->first();
            $papiResult = PapiTestResult::where('test_attempt_id', $this->attemptId)->first();
        }

        $unansweredQuestions = ($this->testState === 'taking') ? $this->getUnansweredQuestions() : [];
        $totalQuestions = count($this->questions);
        $unansweredCount = count($unansweredQuestions);
        $completedCount = max(0, $totalQuestions - $unansweredCount);
        $progressPercent = $totalQuestions > 0 ? round(($completedCount / $totalQuestions) * 100) : 0;

        $questionsPerPage = $this->questionsPerPage;
        $currentPage      = $this->currentPage;
        $totalPages       = max(1, (int) ceil($totalQuestions / $questionsPerPage));
        $pageStart        = $currentPage * $questionsPerPage;
        $pageQuestions    = array_slice($this->questions, $pageStart, $questionsPerPage);

        // Sequential test: hitung progress semua test dalam lowongan & test berikutnya
        $testProgress = [];
        $nextTest = null;
        if ($this->application && $this->application->job) {
            // Reload application dengan testAttempts terbaru
            $freshApplication = \App\Models\JobApplication::with(['testAttempts', 'job'])
                ->find($this->application->id);
            if ($freshApplication) {
                $testProgress = TestSequenceService::buildTestProgress($freshApplication);
                if ($this->test && $this->testState === 'completed') {
                    $nextTest = TestSequenceService::getNextTest($freshApplication, $this->test);
                }
            }
        }

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
        ])->layout('layouts.app');
    }
}

<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestAnswer;
use App\Models\QuestionBank;
use App\Models\DiscTestResult;
use App\Models\PapiTestResult;
use App\Services\DiscCalculatorService;
use App\Services\PapiKostickCalculatorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmployeeOnlineTest extends Component
{
    use WithFileUploads;

    public $testId;
    public $attemptId;
    
    // State: 'intro', 'taking', 'completed'
    public $testState = 'intro';

    public $test;
    public $attempt;

    public $questions = [];
    public $currentQuestionIndex = 0;

    // Paginasi multi-soal: tampilkan beberapa soal sekaligus (PAPI Kostick: 5, DISC: 1)
    public $currentPage = 0;       // Indeks halaman saat ini (0-based)
    public $questionsPerPage = 5;  // Jumlah soal per halaman

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

    // Biodata Peserta Sebelum Mulai Ujian
    public $participantName = '';
    public $participantAge = '';
    public $participantGender = 'male';
    public $testDate = '';

    public function mount($testId)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Akses tidak diizinkan. Silakan login terlebih dahulu.');
        }

        $this->testId = $testId;
        $this->test = Test::with(['category', 'department', 'questions.options'])->findOrFail($testId);

        $categoryName = strtolower($this->test->category?->name ?? '');
        $testTitle    = strtolower($this->test->title ?? '');
        if (str_contains($categoryName, 'disc') || str_contains($testTitle, 'disc')) {
            $this->questionsPerPage = 1;
        } elseif (str_contains($categoryName, 'papi') || str_contains($testTitle, 'papi')) {
            $this->questionsPerPage = 5;
        } else {
            $this->questionsPerPage = 5;
        }

        // Inisialisasi default biodata dari profil karyawan
        $employeeProfile = $user->employeeProfile;
        $this->participantName = $employeeProfile?->full_name ?? ($user->name ?? '');
        $this->participantGender = $employeeProfile?->gender ?? 'male';
        $this->testDate = now()->toDateString();
        
        if ($employeeProfile?->birth_date) {
            $this->participantAge = Carbon::parse($employeeProfile->birth_date)->age;
        }

        // Cek apakah sudah ada attempt sebelumnya
        $existingAttempt = TestAttempt::with(['answers'])
            ->where('user_id', $user->id)
            ->where('test_id', $this->test->id)
            ->where('attempt_type', 'employee')
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

        $this->validate([
            'participantName'   => 'required|string|max:255',
            'participantAge'    => 'required|numeric|min:15|max:99',
            'participantGender' => 'required|in:male,female',
            'testDate'          => 'required|date',
        ], [
            'participantName.required'   => 'Nama lengkap wajib diisi.',
            'participantAge.required'    => 'Usia wajib diisi.',
            'participantAge.numeric'     => 'Usia harus berupa angka.',
            'participantAge.min'         => 'Usia minimal adalah 15 tahun.',
            'participantAge.max'         => 'Usia maksimal adalah 99 tahun.',
            'participantGender.required' => 'Jenis kelamin wajib dipilih.',
            'testDate.required'          => 'Tanggal pelaksanaan tes wajib diisi.',
            'testDate.date'              => 'Format tanggal tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $existingAttempt = TestAttempt::where('user_id', $user->id)
                ->where('test_id', $this->test->id)
                ->where('attempt_type', 'employee')
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

            // Bersihkan attempt kosong jika ada sebelum membuat attempt baru
            TestAttempt::where('user_id', $user->id)
                ->where('test_id', $this->test->id)
                ->where('attempt_type', 'employee')
                ->where('status', 'in_progress')
                ->whereDoesntHave('answers')
                ->delete();

            $this->loadQuestionsAndAnswers();

            if (empty($this->questions)) {
                DB::rollBack();
                session()->flash('error', 'Paket ujian belum memiliki butir soal yang tersedia. Silakan hubungi tim admin / HR.');
                return;
            }

            $attempt = TestAttempt::create([
                'user_id'            => $user->id,
                'attempt_type'       => 'employee',
                'participant_name'   => $this->participantName,
                'participant_age'    => (int) $this->participantAge,
                'participant_gender' => $this->participantGender,
                'test_date'          => $this->testDate,
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
        } elseif ($this->test->is_random) {
            $linkedQuestions = $linkedQuestions->shuffle();
        } else {
            $linkedQuestions = $linkedQuestions->sortBy(function ($q) {
                return $q->pivot?->order_number ?? ($q->metadata['number'] ?? $q->id);
            })->values();
        }

        $this->questions = $linkedQuestions->map(function ($q) {
            return [
                'id' => $q->id,
                'question' => $q->question_text ?? ($q->question ?? ''),
                'question_text' => $q->question_text ?? ($q->question ?? ''),
                'question_type' => $q->question_type,
                'points' => $q->points ?? 1,
                'image_path' => $q->image_path ?? null,
                'options' => $q->options->map(function ($opt) {
                    return [
                        'id' => $opt->id,
                        'option_text' => $opt->option_text,
                        'is_correct' => $opt->is_correct ?? false,
                    ];
                })->toArray(),
            ];
        })->toArray();

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

    public function selectQuestion($index)
    {
        if (isset($this->questions[$index])) {
            $this->currentQuestionIndex = $index;
            // Pastikan halaman mengikuti soal yang dipilih
            $this->currentPage = (int) floor($index / $this->questionsPerPage);
        }
    }

    public function nextQuestion()
    {
        $totalPages = (int) ceil(count($this->questions) / $this->questionsPerPage);
        if ($this->currentPage < $totalPages - 1) {
            $this->currentPage++;
            $this->currentQuestionIndex = $this->currentPage * $this->questionsPerPage;
        }
    }

    public function prevQuestion()
    {
        if ($this->currentPage > 0) {
            $this->currentPage--;
            $this->currentQuestionIndex = $this->currentPage * $this->questionsPerPage;
        }
    }

    public function saveAnswer($questionId, $optionId = null, $essayText = null, $discType = null)
    {
        if (!$this->attemptId) return;

        $question = collect($this->questions)->firstWhere('id', $questionId);
        if (!$question) return;

        if ($question['question_type'] === 'disc' && $discType) {
            if (!isset($this->answers[$questionId]) || !is_array($this->answers[$questionId])) {
                $this->answers[$questionId] = [];
            }

            $oppositeType = ($discType === 'most') ? 'least' : 'most';

            // ATURAN: Baris opsi yang sama tidak boleh sekaligus Most dan Least
            if (isset($this->answers[$questionId][$oppositeType]) && $this->answers[$questionId][$oppositeType] == $optionId) {
                $this->answers[$questionId][$oppositeType] = null;
                TestAnswer::where('attempt_id', $this->attemptId)
                    ->where('question_id', $questionId)
                    ->where('answer_type', $oppositeType)
                    ->delete();
            }

            $this->answers[$questionId][$discType] = $optionId;

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

    public function updatedEssayFile()
    {
        $this->uploadEssayAttachment();
    }

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

        try {
            $path = $this->essayFile->store('test-answers', 'public');
            $fileUrl = asset('storage/' . $path);
            $originalName = $this->essayFile->getClientOriginalName();
            $fileSize = $this->essayFile->getSize();

            $firstEssayId = $firstEssayQuestion['id'];
            $finalAnswer = $this->answers[$firstEssayId] ?? null;
            if (!empty($this->essayLink)) {
                $base = trim($finalAnswer ?? '');
                $finalAnswer = $base !== ''
                    ? $base . "\n\n[Tautan Lampiran/Video]: " . trim($this->essayLink)
                    : "[Tautan Lampiran/Video]: " . trim($this->essayLink);
            }

            TestAnswer::updateOrCreate(
                [
                    'attempt_id' => $this->attemptId,
                    'question_id' => $firstEssayId,
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
                'question_id' => $firstEssayId,
            ];

            $this->essayFile = null;
            session()->flash('message', 'File lampiran berhasil diunggah.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage());
        }
    }

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
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
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
            // Otomatis tambahkan https:// jika belum ada skema
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

    public function finishTest($isAuto = false)
    {
        if (!$this->attemptId) return;

        // Pastikan simpan jawaban essay yang sedang aktif
        foreach ($this->questions as $q) {
            if ($q['question_type'] === 'essay' && isset($this->answers[$q['id']])) {
                $this->saveAnswer($q['id'], null, $this->answers[$q['id']]);
            }
        }

        if (!$isAuto) {
            $unanswered = $this->getUnansweredQuestions();
            if (!empty($unanswered)) {
                $this->currentQuestionIndex = $unanswered[0]['index'];
                $this->currentPage = (int) floor($this->currentQuestionIndex / max(1, $this->questionsPerPage));

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
                // ignore
            }
        }

        if ($hasPapi) {
            try {
                $papiService = app(PapiKostickCalculatorService::class);
                $papiService->calculate($attempt);
            } catch (\Exception $e) {
                // ignore
            }
        }

        $passingScore = (float) $this->test->passing_score;

        if (($hasDisc || $hasPapi) && !$hasEssay && !$hasMultipleChoice) {
            $status = 'completed';
            $totalScore = 100;
        } elseif ($hasEssay) {
            $status = 'completed';
            $totalScore = $objectiveScore;
        } elseif ($passingScore <= 0) {
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

        return view('livewire.employee.employee-online-test', [
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
        ])->layout('layouts.app');
    }
}

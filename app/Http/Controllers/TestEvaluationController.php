<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestAttempt;
use App\Models\TestAnswer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ApplicationStatusUpdatedMail;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DiscTrait;
use App\Services\DiscChartService;
use Illuminate\Support\Str;

class TestEvaluationController extends Controller
{
    /**
     * Preview or download DISC test results as a PDF document.
     */
    public function downloadDiscPdf(Request $request, string $id)
    {
        $attempt = TestAttempt::with([
            'jobApplication.applicantProfile.user',
            'jobApplication.applicantProfile.educations',
            'jobApplication.job.company',
            'jobApplication.job.department',
            'user.employeeProfile.department.company',
            'user.employeeProfile.company',
            'test.category',
            'test.department',
            'discTestResult.discProfile',
        ])->findOrFail($id);

        $user = auth()->user();
        $isRecruiter = $user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter');
        $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));

        // Hanya Admin dan Recruiter yang diizinkan melihat / mengunduh laporan DISC
        if (!$isAdmin && !$isRecruiter) {
            abort(403, 'Akses ditolak. Hasil dan laporan analisis DISC hanya dapat diakses oleh Admin atau Tim HR.');
        }

        $discResult = $attempt->discTestResult;

        if (!$discResult) {
            $redirectRoute = $isAdmin ? 'admin.test_evaluation' : ($isRecruiter ? 'recruiter.test_evaluation' : 'profile');

            return redirect()->route($redirectRoute)
                ->with('error', 'Laporan DISC tidak dapat dibuat: Hasil tes DISC belum tersedia.');
        }

        $line1Raw = $discResult->line_1_scores['raw'] ?? ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0, '*' => 0];
        $line2Raw = $discResult->line_2_scores['raw'] ?? ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0, '*' => 0];
        $line3Raw = $discResult->line_3_scores['raw'] ?? ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];

        $line1Conv = $discResult->line_1_scores['converted'] ?? ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];
        $line2Conv = $discResult->line_2_scores['converted'] ?? ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];
        $line3Conv = $discResult->line_3_scores['converted'] ?? ['D' => 0, 'I' => 0, 'S' => 0, 'C' => 0];

        $profile = $discResult->discProfile;

        // Generate grafik garis dengan garis bantu koordinat
        $chartService = app(DiscChartService::class);
        $chartMost = $chartService->generateLineChart($line1Conv, 'most');
        $chartLeast = $chartService->generateLineChart($line2Conv, 'least');
        $chartChange = $chartService->generateLineChart($line3Conv, 'change');

        // Evaluasi standar DISC berdasarkan software resmi 2018 (Most, Least, Change)
        $evalService = app(\App\Services\DiscStandardEvaluationService::class);
        $discEval = $evalService->evaluateAll($line1Conv, $line2Conv, $line3Conv);

        if (!empty($discEval['change']['index'])) {
            $matchedProfile = \App\Models\DiscProfile::find($discEval['change']['index']);
            if ($matchedProfile) {
                $profile = $matchedProfile;
            }
        }

        // Ambil trait dominan
        $primaryCode = strtoupper(substr($profile?->pattern_code ?? 'D', 0, 1));
        if (!in_array($primaryCode, ['D', 'I', 'S', 'C'])) {
            $maxDim = 'D';
            $maxScore = -999;
            foreach (['D', 'I', 'S', 'C'] as $dim) {
                $sc = (float) ($line3Conv[$dim] ?? 0);
                if ($sc > $maxScore) {
                    $maxScore = $sc;
                    $maxDim = $dim;
                }
            }
            $primaryCode = $maxDim;
        }

        $dominantTrait = DiscTrait::where('dimension_code', $primaryCode)->first();

        $isEmployeeAttempt = ($attempt->attempt_type === 'employee') || empty($attempt->job_application_id);
        $applicantName = $isEmployeeAttempt 
            ? ($attempt->user?->employeeProfile?->full_name ?? ($attempt->user?->name ?? 'Karyawan'))
            : ($attempt->jobApplication?->applicantProfile?->full_name ?? ($attempt->jobApplication?->applicantProfile?->user?->name ?? 'Kandidat'));
        
        $cleanApplicantName = Str::slug($applicantName, '_');
        $patternCode = Str::slug($profile?->pattern_code ?? 'DISC', '_');
        $date = now()->format('Ymd');
        $fileName = "DISC_Report_{$cleanApplicantName}_{$patternCode}_{$date}.pdf";

        $data = compact(
            'attempt',
            'discResult',
            'profile',
            'dominantTrait',
            'primaryCode',
            'line1Raw',
            'line2Raw',
            'line3Raw',
            'line1Conv',
            'line2Conv',
            'line3Conv',
            'chartMost',
            'chartLeast',
            'chartChange',
            'discEval'
        );

        $pdf = Pdf::loadView('admin.test-evaluation.disc-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
                'dpi' => 120,
            ]);

        // Default ke inline preview di browser, jika ada query ?download=1 lakukan direct download
        if ($request->query('download') == '1') {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }

    /**
     * Preview or download PAPI Kostick test results as a PDF document.
     */
    public function downloadPapiPdf(Request $request, string $id)
    {
        $attempt = TestAttempt::with([
            'jobApplication.applicantProfile.user',
            'jobApplication.applicantProfile.educations',
            'jobApplication.job.company',
            'jobApplication.job.department',
            'user.employeeProfile.department.company',
            'user.employeeProfile.company',
            'test.category',
            'test.department',
            'papiTestResult',
        ])->findOrFail($id);

        $user = auth()->user();
        $isRecruiter = $user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter');
        $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));

        if (!$isAdmin && !$isRecruiter) {
            abort(403, 'Akses ditolak. Laporan evaluasi PAPI Kostick hanya dapat diakses oleh Admin atau Tim HR.');
        }

        $papiResult = $attempt->papiTestResult;

        if (!$papiResult) {
            $calculator = app(\App\Services\PapiKostickCalculatorService::class);
            $papiResult = $calculator->calculate($attempt);
        }

        if (!$papiResult) {
            $redirectRoute = $isAdmin ? 'admin.employee_test_evaluation' : ($isRecruiter ? 'recruiter.employee_test_evaluation' : 'profile');
            return redirect()->route($redirectRoute)
                ->with('error', 'Laporan PAPI Kostick tidak dapat dibuat: Hasil tes belum tersedia.');
        }

        $rawAnswers = $papiResult->raw_answers ?? [];
        $scores = $papiResult->scores ?? [];
        $interpretations = $papiResult->interpretations ?? [];
        $roleScore = $papiResult->role_score ?? 0;
        $needScore = $papiResult->need_score ?? 0;
        $isValid = $papiResult->is_valid ?? false;

        // Siapkan struktur 10 baris x 18 kolom untuk lembar jawaban (dari nomor 1-10 di kiri ke 81-90 di kanan)
        $sheetRows = [];
        $startCols = [1, 11, 21, 31, 41, 51, 61, 71, 81];
        for ($r = 0; $r < 10; $r++) {
            $rowCells = [];
            foreach ($startCols as $startNum) {
                $qNum = $startNum + $r;
                $ans = $rawAnswers[$qNum] ?? ($rawAnswers[(string)$qNum] ?? null);
                $choice = '-';
                if ($ans) {
                    if (!empty($ans['choice'])) {
                        $choice = strtolower($ans['choice']);
                    } elseif (!empty($ans['tag'])) {
                        $choice = strtolower($ans['tag']);
                    }
                }
                $rowCells[] = ['text' => $qNum, 'isNum' => true];
                $rowCells[] = ['text' => $choice, 'isNum' => false];
            }
            $sheetRows[] = $rowCells;
        }

        // Struktur 7 Aspek & 20 Faktor
        $papiAspects = [
            [
                'name' => 'Arah kerja',
                'factors' => [
                    ['code' => 'N', 'name' => 'Penyeleseian secara prestasi'],
                    ['code' => 'G', 'name' => 'Peranan sebagai pekerja keras'],
                    ['code' => 'A', 'name' => 'Hasrat untuk berprestasi'],
                ]
            ],
            [
                'name' => 'Kepemimpinan',
                'factors' => [
                    ['code' => 'L', 'name' => 'Peran sebagai pimpinan'],
                    ['code' => 'P', 'name' => 'Pengendalian orang lain'],
                    ['code' => 'I', 'name' => 'Mudah dalam mengambil keputusan'],
                ]
            ],
            [
                'name' => 'Aktivitas',
                'factors' => [
                    ['code' => 'T', 'name' => 'Tipe selalu sibuk'],
                    ['code' => 'V', 'name' => 'Tipe yang bersemangat'],
                ]
            ],
            [
                'name' => 'Pergaulan',
                'factors' => [
                    ['code' => 'X', 'name' => 'Kebutuhan untuk mendapatkan perhatian'],
                    ['code' => 'S', 'name' => 'Pergaulan luas'],
                    ['code' => 'B', 'name' => 'Kebutuhan berkelompok'],
                    ['code' => 'O', 'name' => 'Kebutuhan untuk dekat dan menyayangi'],
                ]
            ],
            [
                'name' => 'Gaya kerja',
                'factors' => [
                    ['code' => 'R', 'name' => 'Tipe teoritikal'],
                    ['code' => 'D', 'name' => 'Suka pekerjaan yang terperinci'],
                    ['code' => 'C', 'name' => 'Tipe teratur'],
                ]
            ],
            [
                'name' => 'Sifat',
                'factors' => [
                    ['code' => 'Z', 'name' => 'Hasrat untuk berubah'],
                    ['code' => 'E', 'name' => 'Pengendalian emosi'],
                    ['code' => 'K', 'name' => 'Agresi'],
                ]
            ],
            [
                'name' => 'Ketaatan',
                'factors' => [
                    ['code' => 'F', 'name' => 'Dukungan terhadap atasan'],
                    ['code' => 'W', 'name' => 'Kebutuhan taat pada aturan dan pengarahan'],
                ]
            ],
        ];

        $isEmployeeAttempt = ($attempt->attempt_type === 'employee') || empty($attempt->job_application_id);
        $participantName = $attempt->participant_name 
            ?: ($isEmployeeAttempt 
                ? ($attempt->user?->employeeProfile?->full_name ?? ($attempt->user?->name ?? 'Karyawan'))
                : ($attempt->jobApplication?->applicantProfile?->full_name ?? ($attempt->jobApplication?->applicantProfile?->user?->name ?? 'Kandidat')));

        $cleanName = Str::slug($participantName, '_');
        $date = now()->format('Ymd');
        $fileName = "PAPI_Kostick_Report_{$cleanName}_{$date}.pdf";

        $data = compact(
            'attempt',
            'papiResult',
            'participantName',
            'isEmployeeAttempt',
            'sheetRows',
            'papiAspects',
            'scores',
            'interpretations',
            'roleScore',
            'needScore',
            'isValid'
        );

        $pdf = Pdf::loadView('admin.test-evaluation.papi-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOption([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
                'dpi' => 120,
            ]);

        if ($request->query('download') == '1') {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }

    public function updateGrade(Request $request, string $id)
    {
        $attempt = TestAttempt::with([
            'answers.question', 
            'test', 
            'jobApplication.applicantProfile.user',
            'jobApplication.job.company',
            'jobApplication.job.department'
        ])->findOrFail($id);

        $request->validate([
            'essay_scores' => 'nullable|array',
            'essay_scores.*' => 'nullable|numeric|min:0',
            'scores' => 'nullable|array',
            'scores.*' => 'nullable|numeric|min:0',
            'application_status' => 'nullable|string|in:Submitted,Reviewed,Shortlisted,Interview,Accepted,Rejected',
            'application_notes' => 'nullable|string|max:1000',
            'send_email' => 'nullable',
        ]);

        $isEmployeeAttempt = ($attempt->attempt_type === 'employee') || empty($attempt->job_application_id);
        $user = auth()->user();
        $isRecruiter = $user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter');
        $redirectRoute = $isEmployeeAttempt
            ? ($isRecruiter ? 'recruiter.employee_test_evaluation' : 'admin.employee_test_evaluation')
            : ($isRecruiter ? 'recruiter.test_evaluation' : 'admin.test_evaluation');

        try {
            DB::beginTransaction();

            $reviewerId = auth()->id();
            $essayScores = $request->input('essay_scores') ?? $request->input('scores', []);

            foreach ($essayScores as $answerId => $scoreValue) {
                if ($scoreValue !== null && $scoreValue !== '') {
                    $answer = TestAnswer::where('attempt_id', $attempt->id)
                        ->where('id', $answerId)
                        ->first();

                    if ($answer) {
                        $maxPoints = $answer->question->points ?? 1;
                        $finalScore = min((float) $scoreValue, (float) $maxPoints);

                        $answer->update([
                            'score' => $finalScore,
                            'reviewed_by' => $reviewerId,
                        ]);
                    }
                }
            }

            // Recalculate essay score sum
            $essayScoreSum = TestAnswer::where('attempt_id', $attempt->id)
                ->whereHas('question', function ($q) {
                    $q->where('question_type', 'essay');
                })
                ->sum('score');

            // Recalculate objective score sum if not set
            $objectiveScoreSum = TestAnswer::where('attempt_id', $attempt->id)
                ->whereHas('question', function ($q) {
                    $q->where('question_type', 'multiple_choice');
                })
                ->sum('score');

            $totalScore = $objectiveScoreSum + $essayScoreSum;

            $passingScore = $attempt->test ? (float) $attempt->test->passing_score : 0;
            $hasDiscOrPapi = $attempt->discTestResult || $attempt->papiTestResult || ($attempt->test && (
                str_contains(strtolower($attempt->test->title ?? ''), 'disc') ||
                str_contains(strtolower($attempt->test->title ?? ''), 'papi') ||
                str_contains(strtolower($attempt->test->category?->name ?? ''), 'papi') ||
                str_contains(strtolower($attempt->test->title ?? ''), 'personality')
            ));

            if ($hasDiscOrPapi || $passingScore <= 0) {
                $status = 'completed';
            } else {
                $status = ($totalScore >= $passingScore) ? 'passed' : 'failed';
            }

            $attempt->update([
                'objective_score' => $objectiveScoreSum,
                'essay_score' => $essayScoreSum,
                'total_score' => $totalScore,
                'status' => $status,
            ]);

            $emailSent = false;
            $applicantEmail = null;

            // Update status lamaran (khusus pelamar): prioritaskan pilihan manual HR jika dipilih, jika tidak dan lulus KKM otomatis Shortlisted
            if ($attempt->jobApplication) {
                $app = $attempt->jobApplication;
                $newStatus = $request->input('application_status');
                $customNotes = $request->input('application_notes');
                $updatedStatus = null;
                $notes = null;

                if (!empty($newStatus)) {
                    $updatedStatus = $newStatus;
                    $notes = !empty($customNotes) 
                        ? $customNotes 
                        : 'Status lamaran diperbarui oleh HR melalui evaluasi ujian (Total Nilai: ' . number_format($totalScore, 1) . ').';

                    $app->update([
                        'status' => $updatedStatus,
                        'notes'  => $notes,
                    ]);

                    \App\Models\ApplicationStatusHistory::create([
                        'job_applications_id' => $app->id,
                        'status'              => $updatedStatus,
                        'notes'               => $notes,
                        'changed_by'          => $reviewerId ?? 1,
                        'changed_at'          => now(),
                    ]);
                } elseif ($status === 'passed' && in_array(strtolower($app->status), ['submitted', 'reviewed', 'pending'])) {
                    // Fallback otomatis jika lulus KKM
                    $updatedStatus = 'Shortlisted';
                    $notes = !empty($customNotes)
                        ? $customNotes
                        : 'Lolos Ujian Seleksi Online (Nilai: ' . number_format($totalScore, 1) . ' / KKM: ' . number_format($passingScore, 0) . '). Siap untuk dijadwalkan wawancara.';

                    $app->update([
                        'status' => $updatedStatus,
                        'notes'  => $notes,
                    ]);

                    \App\Models\ApplicationStatusHistory::create([
                        'job_applications_id' => $app->id,
                        'status'              => $updatedStatus,
                        'notes'               => $notes,
                        'changed_by'          => $reviewerId ?? 1,
                        'changed_at'          => now(),
                    ]);
                }

                // Kirim email notifikasi ke pelamar jika status berubah dan opsi aktif
                $shouldSendEmail = $request->has('send_email')
                    ? $request->boolean('send_email')
                    : true;

                $applicantEmail = $app->applicantProfile?->user?->email;

                if ($updatedStatus && $shouldSendEmail && $applicantEmail) {
                    try {
                        Mail::to($applicantEmail)->send(
                            new ApplicationStatusUpdatedMail($app, $updatedStatus, $notes)
                        );
                        $emailSent = true;
                    } catch (\Throwable $e) {
                        Log::error('Gagal mengirim email evaluasi pelamar: ' . $e->getMessage(), [
                            'attempt_id'     => $attempt->id,
                            'application_id' => $app->id,
                            'recipient'      => $applicantEmail,
                            'status'         => $updatedStatus,
                        ]);
                    }
                }
            }

            DB::commit();

            $successMsg = $isEmployeeAttempt
                ? 'Penilaian essay karyawan berhasil disimpan dan total skor telah diperbarui.'
                : 'Penilaian essay pelamar berhasil disimpan dan total skor telah diperbarui.';

            if ($emailSent && !empty($applicantEmail)) {
                $successMsg .= ' Notifikasi email status telah berhasil dikirim ke ' . $applicantEmail . '.';
            }

            return redirect()->route($redirectRoute)
                ->with('grade_success', $successMsg)
                ->with('update', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route($redirectRoute)
                ->with('error', 'Gagal menyimpan penilaian essay: ' . $e->getMessage());
        }
    }
}

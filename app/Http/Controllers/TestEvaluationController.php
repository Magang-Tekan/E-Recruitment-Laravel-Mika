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
        $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
        $isRecruiter = $request->is('recruiter/*') || $request->routeIs('recruiter.*') || ($user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter));

        // Hanya Admin dan Recruiter yang diizinkan melihat / mengunduh laporan DISC
        if (!$isAdmin && !$isRecruiter) {
            abort(403, 'Akses ditolak. Hasil dan laporan analisis DISC hanya dapat diakses oleh Admin atau Tim HR.');
        }

        $isDesignatedRecruiter = $user && ((bool) $user->is_recruiter && !$isAdmin && $user->role_id != 2);
        if (!$isAdmin && $isRecruiter) {
            $jobReviewerId  = $attempt->jobApplication?->job?->reviewer_id;
            $employeeDeptId = $user?->employeeProfile?->department_id;
            $employeePosId  = $user?->employeeProfile?->position_id;
            $jobDeptId      = $attempt->jobApplication?->job?->department_id ?? $attempt->test?->department_id;
            $jobPosId       = $attempt->jobApplication?->job?->position_id;

            if ($isDesignatedRecruiter) {
                $hasAccess = ($jobReviewerId && $jobReviewerId == $user->id)
                    || ($employeePosId && $jobPosId && $jobPosId == $employeePosId);
            } else {
                $hasAccess = ($jobReviewerId && $jobReviewerId == $user->id)
                    || ($employeeDeptId && $jobDeptId && $jobDeptId == $employeeDeptId)
                    || ($user->role_id == 2 && !$employeeDeptId);
            }

            if (!$hasAccess) {
                abort(403, 'Akses ditolak. Anda hanya dapat melihat laporan peserta untuk posisi Anda atau lowongan yang ditugaskan kepada Anda.');
            }
        }

        $discResult = $attempt->discTestResult;

        if (!$discResult) {
            $redirectRoute = ($request->is('recruiter/*') || ($isRecruiter && !$isAdmin)) ? 'recruiter.test_evaluation' : ($isAdmin ? 'admin.test_evaluation' : 'profile');

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
        $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
        $isRecruiter = $request->is('recruiter/*') || $request->routeIs('recruiter.*') || ($user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter));

        if (!$isAdmin && !$isRecruiter) {
            abort(403, 'Akses ditolak. Laporan evaluasi PAPI Kostick hanya dapat diakses oleh Admin atau Tim HR.');
        }

        $isDesignatedRecruiter = $user && ((bool) $user->is_recruiter && !$isAdmin && $user->role_id != 2);
        if (!$isAdmin && $isRecruiter) {
            $jobReviewerId  = $attempt->jobApplication?->job?->reviewer_id;
            $employeeDeptId = $user?->employeeProfile?->department_id;
            $employeePosId  = $user?->employeeProfile?->position_id;
            $jobDeptId      = $attempt->jobApplication?->job?->department_id ?? $attempt->test?->department_id;
            $jobPosId       = $attempt->jobApplication?->job?->position_id;

            if ($isDesignatedRecruiter) {
                $hasAccess = ($jobReviewerId && $jobReviewerId == $user->id)
                    || ($employeePosId && $jobPosId && $jobPosId == $employeePosId);
            } else {
                $hasAccess = ($jobReviewerId && $jobReviewerId == $user->id)
                    || ($employeeDeptId && $jobDeptId && $jobDeptId == $employeeDeptId)
                    || ($user->role_id == 2 && !$employeeDeptId);
            }

            if (!$hasAccess) {
                abort(403, 'Akses ditolak. Anda hanya dapat melihat laporan peserta untuk posisi Anda atau lowongan yang ditugaskan kepada Anda.');
            }
        }

        $papiResult = $attempt->papiTestResult;

        if (!$papiResult) {
            $calculator = app(\App\Services\PapiKostickCalculatorService::class);
            $papiResult = $calculator->calculate($attempt);
        }

        if (!$papiResult) {
            $redirectRoute = ($request->is('recruiter/*') || ($isRecruiter && !$isAdmin)) ? 'recruiter.dashboard' : ($isAdmin ? 'admin.employee_test_evaluation' : 'profile');
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
            'test_decision' => 'nullable|string|in:approved,rejected,override_shortlisted',
            'application_status' => 'nullable|string|in:Submitted,Reviewed,Shortlisted,Interview,Accepted,Rejected',
            'application_notes' => 'nullable|string|max:1000',
            'send_email' => 'nullable',
        ]);

        $isEmployeeAttempt = ($attempt->attempt_type === 'employee') || empty($attempt->job_application_id);
        $user = auth()->user();
        $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
        $isRecruiter = $request->is('recruiter/*') || $request->routeIs('recruiter.*') || ($user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter));
        $isRecruiterTarget = $request->is('recruiter/*') || $request->routeIs('recruiter.*') || ($isRecruiter && !$isAdmin);

        $redirectRoute = $isEmployeeAttempt
            ? ($isRecruiterTarget ? 'recruiter.dashboard' : 'admin.employee_test_evaluation')
            : ($isRecruiterTarget ? 'recruiter.test_evaluation' : 'admin.test_evaluation');

        $isDesignatedRecruiter = $user && ((bool) $user->is_recruiter && !$isAdmin && $user->role_id != 2);
        if (!$isAdmin && $isRecruiter) {
            $jobReviewerId  = $attempt->jobApplication?->job?->reviewer_id;
            $employeeDeptId = $user?->employeeProfile?->department_id;
            $employeePosId  = $user?->employeeProfile?->position_id;
            $jobDeptId      = $attempt->jobApplication?->job?->department_id ?? $attempt->test?->department_id;
            $jobPosId       = $attempt->jobApplication?->job?->position_id;

            if ($isDesignatedRecruiter) {
                $hasAccess = ($jobReviewerId && $jobReviewerId == $user->id)
                    || ($employeePosId && $jobPosId && $jobPosId == $employeePosId);
            } else {
                $hasAccess = ($jobReviewerId && $jobReviewerId == $user->id)
                    || ($employeeDeptId && $jobDeptId && $jobDeptId == $employeeDeptId)
                    || ($user->role_id == 2 && !$employeeDeptId);
            }

            if (!$hasAccess) {
                return redirect()->route($redirectRoute)
                    ->with('error', 'Anda hanya memiliki hak untuk menilai ujian yang sesuai dengan posisi Anda atau lowongan yang ditugaskan kepada Anda.');
            }
        }

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
            $updatedStatus = null;
            $finalNotes = null;

            // ========================================================
            // DOUBLE APPROVAL TAHAP 2: EVALUASI UJIAN ONLINE
            // ========================================================
            if ($attempt->jobApplication) {
                $app = $attempt->jobApplication;
                $customNotes = $request->input('application_notes');
                $testDecision = $request->input('test_decision');
                $newStatus = $request->input('application_status');
                $historyNotes = null;

                if (!$isAdmin) {
                    // ====================================================
                    // 1. RECRUITER (Pewawancara / Penilai Teknis - GATE 1)
                    // ====================================================
                    // Otomatis mencatat persetujuan Recruiter saat esai dinilai dan mencapai KKM:
                    if ($status === 'passed' || ($hasDiscOrPapi && $passingScore <= 0)) {
                        $app->test_recruiter_approval = 'approved';
                        $app->test_recruiter_approved_at = now();
                        $app->test_recruiter_id = $reviewerId;
                        $app->test_recruiter_notes = !empty($customNotes)
                            ? $customNotes
                            : 'Nilai ujian teknis mencapai KKM (' . number_format($totalScore, 1) . ' / KKM: ' . number_format($passingScore, 0) . '). Rekomendasi lolos tahap teknis.';

                        // Cek apakah HR sudah menyetujui sebelumnya (Double Approval Terpenuhi)
                        if ($app->test_admin_approval === 'approved') {
                            $updatedStatus = 'Shortlisted';
                            $finalNotes = 'Double Approved: Hasil tes disetujui oleh Recruiter Teknis & HR/Admin. Siap untuk dijadwalkan wawancara.';
                            $historyNotes = '[Double Approved: Lolos Ujian Online]. Kedua pihak (Recruiter & HR) telah menyetujui hasil ujian kandidat.';
                            $app->status = $updatedStatus;
                            $app->notes = $finalNotes;
                        } else {
                            $historyNotes = '[Persetujuan 1: Recruiter Teknis Disetujui] oleh ' . ($user->name ?? 'Recruiter') . ' (Total Nilai: ' . number_format($totalScore, 1) . '). Menunggu persetujuan psikotes & kuota dari HR/Admin.';
                        }
                    } else {
                        // Di bawah KKM
                        $app->test_recruiter_approval = 'rejected';
                        $app->test_recruiter_approved_at = now();
                        $app->test_recruiter_id = $reviewerId;
                        $app->test_recruiter_notes = !empty($customNotes)
                            ? $customNotes
                            : 'Nilai ujian teknis di bawah KKM (' . number_format($totalScore, 1) . ' / KKM: ' . number_format($passingScore, 0) . '). Rekomendasi tidak lolos teknis.';

                        $historyNotes = '[Evaluasi Teknis Recruiter: Di Bawah KKM] (Total Nilai: ' . number_format($totalScore, 1) . ' / KKM: ' . number_format($passingScore, 0) . '). Rekomendasi tidak lolos tahap teknis.';
                    }

                    $app->save();

                    \App\Models\ApplicationStatusHistory::create([
                        'job_applications_id' => $app->id,
                        'status'              => $app->status,
                        'notes'               => $historyNotes,
                        'changed_by'          => $reviewerId ?? 1,
                        'changed_at'          => now(),
                    ]);
                } else {
                    // ====================================================
                    // 2. HR / ADMIN (Verifikasi Psikotes & Keputusan - GATE 2)
                    // ====================================================
                    if ($testDecision === 'override_shortlisted') {
                        // Admin Override / Hak Veto
                        $app->test_admin_approval = 'approved';
                        $app->test_admin_notes = $customNotes ?: 'Disetujui melalui Admin Override (Hak Veto HR).';
                        $app->test_admin_approved_at = now();
                        $app->test_admin_id = $reviewerId;

                        $app->test_recruiter_approval = 'approved';
                        $app->test_recruiter_notes = $app->test_recruiter_notes ?: '[Bypassed by Admin Override]';
                        $app->test_recruiter_approved_at = $app->test_recruiter_approved_at ?: now();

                        $updatedStatus = 'Shortlisted';
                        $finalNotes = $customNotes ?: 'Selamat! Anda dinyatakan lolos tahap evaluasi ujian (Admin Override) dan masuk daftar kandidat terpilih (Shortlisted). Kami akan segera menginformasikan jadwal wawancara.';
                        $historyNotes = '[Admin Override: Lolos Ujian & Siap Wawancara (Hak Veto HR)]. ' . ($customNotes ? 'Catatan: ' . $customNotes : '');
                    } elseif ($testDecision === 'approved') {
                        // Persetujuan HR Normal
                        $app->test_admin_approval = 'approved';
                        $app->test_admin_notes = $customNotes ?: 'Persetujuan HR: Hasil psikotes (DISC & PAPI) serta evaluasi ujian disetujui.';
                        $app->test_admin_approved_at = now();
                        $app->test_admin_id = $reviewerId;

                        // Otomatis luluskan Recruiter jika tes ini tidak memiliki esai dan status lulus otomatis
                        $hasUnreviewedEssay = $attempt->answers->contains(function ($ans) {
                            return $ans->question && $ans->question->question_type === 'essay' && is_null($ans->reviewed_by);
                        });

                        if ($app->test_recruiter_approval === 'approved' || ($status === 'passed' && !$hasUnreviewedEssay)) {
                            if ($app->test_recruiter_approval !== 'approved' && $status === 'passed') {
                                $app->test_recruiter_approval = 'approved';
                                $app->test_recruiter_approved_at = now();
                                $app->test_recruiter_id = $reviewerId;
                                $app->test_recruiter_notes = 'Lolos nilai passing score otomatis.';
                            }

                            $updatedStatus = 'Shortlisted';
                            $finalNotes = $customNotes ?: 'Selamat! Anda dinyatakan lolos tahap evaluasi ujian (Double Approved) dan masuk ke dalam daftar kandidat terpilih (Shortlisted). Kami akan segera menginformasikan jadwal wawancara.';
                            $historyNotes = '[Double Approved: Lolos Ujian Online]. Kedua pihak (Recruiter Teknis & HR) telah menyetujui hasil ujian kandidat. Siap dijadwalkan wawancara.';
                        } else {
                            $finalNotes = $customNotes ?: 'Hasil psikotes disetujui oleh HR. Menunggu konfirmasi koreksi teknis Recruiter.';
                            $historyNotes = '[Persetujuan 2: HR Disetujui]. Verifikasi hasil psikotes & kepribadian disetujui. Menunggu konfirmasi nilai/koreksi teknis Recruiter.';
                        }
                    } elseif ($testDecision === 'rejected') {
                        // Penolakan oleh HR
                        $app->test_admin_approval = 'rejected';
                        $app->test_admin_notes = $customNotes ?: 'Hasil evaluasi ujian belum memenuhi kriteria kualifikasi lowongan.';
                        $app->test_admin_approved_at = now();
                        $app->test_admin_id = $reviewerId;

                        $updatedStatus = 'Rejected';
                        $finalNotes = $customNotes ?: 'Terima kasih atas partisipasi Anda. Saat ini hasil evaluasi ujian belum sesuai dengan kriteria yang kami butuhkan.';
                        $historyNotes = '[Tidak Lolos Ujian Online (Ditolak / Rejected)]. Catatan HR: ' . ($customNotes ?: 'Belum memenuhi kriteria.');
                    } elseif (!empty($newStatus)) {
                        // Pembaruan Status Manual oleh Admin
                        $updatedStatus = $newStatus;
                        $finalNotes = !empty($customNotes)
                            ? $customNotes
                            : 'Status lamaran diperbarui oleh HR melalui evaluasi ujian (Total Nilai: ' . number_format($totalScore, 1) . ').';

                        if ($newStatus === 'Shortlisted') {
                            $app->test_admin_approval = 'approved';
                            $app->test_admin_notes = $customNotes ?: 'Disetujui HR melalui evaluasi ujian.';
                            $app->test_admin_approved_at = now();
                            $app->test_admin_id = $reviewerId;
                        } elseif ($newStatus === 'Rejected') {
                            $app->test_admin_approval = 'rejected';
                            $app->test_admin_approved_at = now();
                            $app->test_admin_id = $reviewerId;
                        }

                        $historyNotes = '[Status Diperbarui oleh HR: ' . $updatedStatus . ']. ' . $finalNotes;
                    } elseif ($status === 'passed' && in_array(strtolower($app->status), ['submitted', 'reviewed', 'pending'])) {
                        // Fallback jika lolos KKM & belum ada keputusan spesifik
                        $app->test_admin_approval = 'approved';
                        $app->test_admin_approved_at = now();
                        $app->test_admin_id = $reviewerId;

                        if ($app->test_recruiter_approval === 'approved') {
                            $updatedStatus = 'Shortlisted';
                            $finalNotes = 'Lolos Ujian Seleksi Online (Double Approved). Siap untuk dijadwalkan wawancara.';
                            $historyNotes = '[Double Approved: Lolos Ujian Online]. Nilai teknis & evaluasi HR disetujui.';
                        } else {
                            $historyNotes = '[Evaluasi Ujian HR: Nilai Lolos KKM]. Menunggu konfirmasi recruiter.';
                        }
                    }

                    if ($updatedStatus) {
                        $app->status = $updatedStatus;
                    }
                    if ($finalNotes) {
                        $app->notes = $finalNotes;
                    }
                    $app->save();

                    if ($historyNotes) {
                        \App\Models\ApplicationStatusHistory::create([
                            'job_applications_id' => $app->id,
                            'status'              => $app->status,
                            'notes'               => $historyNotes,
                            'changed_by'          => $reviewerId ?? 1,
                            'changed_at'          => now(),
                        ]);
                    }

                    // Kirim notifikasi email jika diminta
                    $shouldSendEmail = $request->has('send_email')
                        ? $request->boolean('send_email')
                        : true;

                    $applicantEmail = $app->applicantProfile?->user?->email;

                    if ($updatedStatus && $shouldSendEmail && $applicantEmail) {
                        try {
                            Mail::to($applicantEmail)->send(
                                new ApplicationStatusUpdatedMail($app, $updatedStatus, $finalNotes)
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
            }

            DB::commit();

            if (!$isAdmin) {
                $successMsg = ($status === 'passed')
                    ? 'Koreksi esai berhasil disimpan (Total Nilai: ' . number_format($totalScore, 1) . ' ≥ KKM). Persetujuan Teknis Recruiter telah tercatat. Status kini menunggu verifikasi psikotes & kuota oleh HR/Admin.'
                    : 'Koreksi esai berhasil disimpan (Total Nilai: ' . number_format($totalScore, 1) . ' di bawah KKM). Catatan rekomendasi telah disimpan untuk peninjauan HR/Admin.';
            } else {
                $successMsg = $isEmployeeAttempt
                    ? 'Penilaian essay karyawan berhasil disimpan dan total skor telah diperbarui.'
                    : 'Penilaian essay pelamar berhasil disimpan dan total skor telah diperbarui.';

                if ($updatedStatus === 'Shortlisted') {
                    $successMsg = 'Evaluasi ujian berhasil disimpan! Pelamar resmi dinyatakan Lolos Ujian & Siap Wawancara (Shortlisted).';
                }

                if ($emailSent && !empty($applicantEmail)) {
                    $successMsg .= ' Notifikasi email status telah berhasil dikirim ke ' . $applicantEmail . '.';
                }
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Models\ApplicationStatusHistory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ApplicationStatusUpdatedMail;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of job applications (Read)
     */
    public function index()
    {
        $applications = JobApplication::with(['job.company', 'job.department', 'applicantProfile.user'])->get();
        return response()->json($applications);
    }

    /**
     * Handle applicant job application submission (Create/Store)
     */
    public function store(Request $request, string $id)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $applicantProfile = \App\Models\ApplicantProfile::where('user_id', $user->id)->first();
        if (!$applicantProfile) {
            return redirect()->route('profile')
                ->with('error', 'Profil pelamar tidak ditemukan. Silakan lengkapi profil Anda terlebih dahulu.');
        }

        // Cek kelengkapan data wajib (minimal 80%)
        if (!$applicantProfile->is_mandatory_complete || $applicantProfile->completion_percentage < 80) {
            return redirect()->route('jobs.show', $id)
                ->with('error', 'Mohon lengkapi seluruh data wajib profil Anda (minimal 80%) sebelum mengajukan lamaran.');
        }

        $job = \App\Models\Job::findOrFail($id);

        // Cek apakah lowongan aktif
        $isActive = $job->status === 'Open' && (!$job->deadline || $job->deadline >= now()->toDateString());
        if (!$isActive) {
            return redirect()->route('jobs.show', $id)
                ->with('error', 'Lowongan pekerjaan ini sudah ditutup atau tidak menerima lamaran baru.');
        }

        // Cek apakah sudah pernah melamar lowongan ini
        $alreadyApplied = JobApplication::where('job_id', $job->id)
            ->where('profile_id', $applicantProfile->id)
            ->exists();

        if ($alreadyApplied) {
            return redirect()->route('jobs.show', $id)
                ->with('error', 'Anda sudah pernah mengajukan lamaran untuk lowongan pekerjaan ini.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $application = JobApplication::create([
                'job_id'     => $job->id,
                'profile_id' => $applicantProfile->id,
                'status'     => 'Submitted',
                'applied_at' => now(),
                'notes'      => null,
            ]);

            // Catat history status awal
            ApplicationStatusHistory::create([
                'job_applications_id' => $application->id,
                'status'              => 'Submitted',
                'notes'               => 'Lamaran pekerjaan berhasil diajukan oleh pelamar.',
                'changed_by'          => $user->id,
                'changed_at'          => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('profile', ['tab' => 'riwayat'])
                ->with('create', 'Lamaran Anda berhasil dikirim! Silakan pantau perkembangan seleksi dan ikuti tes online jika tersedia.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->route('jobs.show', $id)
                ->with('error', 'Gagal mengirimkan lamaran: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified job application details (Read)
     */
    public function show(string $id)
    {
        $application = JobApplication::with([
            'job.company', 
            'job.department', 
            'applicantProfile.user',
            'applicantProfile.educations',
            'applicantProfile.workExperiences',
            'applicantProfile.skills',
            'statusHistories.changedBy'
        ])->findOrFail($id);

        return response()->json($application);
    }

    /**
     * Update the specified job application status (Update)
     */
    public function update(Request $request, string $id)
    {
        $application = JobApplication::with(['job.company', 'job.department', 'applicantProfile.user'])->findOrFail($id);

        $user = auth()->user();
        $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
        $isRecruiter = $user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter);
        $redirectRoute = ($isRecruiter && !$isAdmin) ? 'recruiter.application' : 'admin.application';

        $employeeDeptId = $user?->employeeProfile?->department_id;
        $employeePosId  = $user?->employeeProfile?->position_id;
        $jobReviewerId  = $application->job?->reviewer_id;
        $jobDeptId      = $application->job?->department_id;
        $jobPosId       = $application->job?->position_id;
        $isDesignatedRecruiter = $user && ((bool) $user->is_recruiter && !$isAdmin && $user->role_id != 2);

        // Cek mode approval:
        // - 'interview_recruiter_review': Review pasca wawancara oleh Recruiter
        // - 'interview_admin_review': Review pasca wawancara oleh HR / Admin
        // - 'recruiter_review': Review seleksi berkas oleh Recruiter
        // - 'admin_review': Review seleksi berkas oleh Admin
        // - 'direct': Direct status change oleh Admin
        $approvalType = $request->input('approval_type');
        if (!$isAdmin) {
            if ($approvalType === 'interview_recruiter_review' || $application->status === 'Interview') {
                $approvalType = 'interview_recruiter_review';
            } else {
                $approvalType = 'recruiter_review';
            }
        } else {
            if (!$approvalType) {
                if ($application->status === 'Interview') {
                    $approvalType = 'interview_admin_review';
                } else {
                    $approvalType = 'admin_review';
                }
            }
        }

        // Scoping hak akses Recruiter:
        // - Karyawan ditunjuk recruiter: HANYA lowongan sesuai posisi atau yang ditugaskan sebagai reviewer
        // - HR recruiter: lowongan pada departemennya atau lowongan umum
        if (!$isAdmin && $isRecruiter) {
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
                    ->with('error', 'Anda hanya memiliki hak untuk meninjau lamaran yang sesuai dengan posisi Anda atau lowongan yang ditugaskan kepada Anda.');
            }

            // Seleksi berkas hanya untuk lowongan yang aktif; tahap wawancara tetap dapat dievaluasi
            if ($approvalType !== 'interview_recruiter_review') {
                $job = $application->job;
                $isActive = $job && $job->status === 'Open' && (! $job->deadline || $job->deadline >= now()->toDateString());
                if (! $isActive) {
                    return redirect()->route($redirectRoute)
                        ->with('error', 'Recruiter hanya diperbolehkan menyeleksi CV pada lowongan yang aktif.');
                }
            }
        }

        $notes = $request->input('notes');
        $historyNotes = null;
        $customSuccessMsg = null;

        $defaultTemplates = [
            'Reviewed'         => 'Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.',
            'Partial Approved' => 'Lamaran Anda telah disetujui pada salah satu tahap verifikasi (Partial Approved) dan sedang dalam proses peninjauan akhir.',
            'Shortlisted'      => 'Selamat! Anda dinyatakan lolos seluruh tahap seleksi berkas dan masuk ke dalam daftar kandidat terpilih (Shortlisted). Kami akan segera menginformasikan jadwal wawancara.',
            'Interview'        => 'Anda diundang untuk mengikuti tahap wawancara. Silakan periksa jadwal dan informasi meeting yang tertera.',
            'Accepted'         => 'Selamat! Anda dinyatakan DITERIMA untuk bergabung bersama kami. Tim HR akan segera menghubungi Anda terkait proses offering dan onboarding.',
            'Rejected'         => 'Terima kasih atas partisipasi Anda. Saat ini kualifikasi Anda belum sesuai dengan kriteria yang kami butuhkan. Tetap semangat dan sukses untuk kesempatan berikutnya.',
            'Submitted'        => 'Lamaran Anda telah kami terima dan sedang dalam proses seleksi berkas oleh tim rekruter.',
        ];

        if ($approvalType === 'interview_recruiter_review') {
            $request->validate([
                'decision' => 'required|in:approved,rejected',
                'notes'    => 'nullable|string',
            ]);

            $decision = $request->input('decision');
            $application->interview_recruiter_approval = $decision;
            $application->interview_recruiter_notes = $notes;
            $application->interview_recruiter_approved_at = now();
            $application->interview_recruiter_id = $user->id;

            if ($decision === 'approved') {
                if ($application->interview_admin_approval === 'approved') {
                    $newStatus = 'Accepted';
                    $notes = $notes ?: $defaultTemplates['Accepted'];
                    $historyNotes = "[Double Approved: Lolos Wawancara]. Kedua pihak (Recruiter & HR) telah sepakat menyetujui kandidat." . ($notes ? " Catatan Recruiter: {$notes}" : "");
                    $customSuccessMsg = 'Persetujuan Recruiter berhasil dicatat. Kedua belah pihak telah sepakat, status pelamar resmi DITERIMA (Accepted).';
                } else {
                    $newStatus = 'Interview';
                    $historyNotes = "[Persetujuan Teknis Wawancara]. Disetujui oleh Recruiter ({$user->name}): 'Kompetensi layak untuk tim'. Menunggu keputusan HR/Admin." . ($notes ? " Catatan: {$notes}" : "");
                    $customSuccessMsg = 'Persetujuan teknis Recruiter berhasil disimpan. Status kandidat menunggu persetujuan HR/Admin (gaji & kontrak).';
                }
            } else {
                $newStatus = 'Interview';
                $historyNotes = "[Rekomendasi Tidak Lolos Wawancara oleh Recruiter: {$user->name}]." . ($notes ? " Catatan: {$notes}" : "");
                $customSuccessMsg = 'Catatan rekomendasi penolakan hasil wawancara berhasil disimpan untuk konfirmasi HR/Admin.';
            }
        } elseif ($approvalType === 'interview_admin_review') {
            if (!$isAdmin) {
                return redirect()->route($redirectRoute)
                    ->with('error', 'Hanya HR/Admin yang memiliki wewenang verifikasi HR pasca wawancara.');
            }

            $request->validate([
                'decision' => 'required|in:approved,rejected,override_accepted',
                'notes'    => 'nullable|string',
            ]);

            $decision = $request->input('decision');

            if ($decision === 'override_accepted') {
                // ADMIN OVERRIDE / EMERGENCY APPROVAL (Kuasa Penuh Admin)
                $application->interview_admin_approval = 'approved';
                $application->interview_admin_notes = $notes ?: 'Disetujui melalui Admin Override (Emergency Approval).';
                $application->interview_admin_approved_at = now();
                $application->interview_admin_id = $user->id;

                $application->interview_recruiter_approval = 'approved';
                $application->interview_recruiter_notes = $application->interview_recruiter_notes ?: '[Bypassed by Admin Override]';
                $application->interview_recruiter_approved_at = $application->interview_recruiter_approved_at ?: now();

                $newStatus = 'Accepted';
                $notes = $notes ?: $defaultTemplates['Accepted'];
                $historyNotes = "[Admin Override: Diterima Resmi (Emergency Approval)]." . ($notes ? " Catatan HR: {$notes}" : "");
                $customSuccessMsg = 'Admin Override berhasil diterapkan! Pelamar resmi dinyatakan DITERIMA (Accepted) tanpa menunggu persetujuan recruiter.';
            } elseif ($decision === 'approved') {
                $application->interview_admin_approval = 'approved';
                $application->interview_admin_notes = $notes ?: 'Persetujuan HR: Gaji, psikotes, kuota, dan kontrak kerja disetujui.';
                $application->interview_admin_approved_at = now();
                $application->interview_admin_id = $user->id;

                if ($application->interview_recruiter_approval === 'approved') {
                    $newStatus = 'Accepted';
                    $notes = $notes ?: $defaultTemplates['Accepted'];
                    $historyNotes = "[Double Approved: Lolos Wawancara]. Kedua pihak (Recruiter & HR) telah sepakat menyetujui kandidat." . ($notes ? " Catatan HR: {$notes}" : "");
                    $customSuccessMsg = 'Persetujuan HR berhasil dicatat. Kedua pihak telah sepakat dan status pelamar resmi DITERIMA (Accepted).';
                } else {
                    $newStatus = 'Interview';
                    $historyNotes = "[Persetujuan HR Wawancara]. Disetujui oleh HR/Admin (gaji & kontrak). Menunggu konfirmasi kelayakan teknis Recruiter." . ($notes ? " Catatan HR: {$notes}" : "");
                    $customSuccessMsg = 'Persetujuan HR berhasil disimpan. Status kandidat tetap Wawancara sampai Recruiter memberikan persetujuan kompetensi.';
                }
            } else { // rejected
                $application->interview_admin_approval = 'rejected';
                $application->interview_admin_notes = $notes ?: 'Lamaran ditolak pada tahap evaluasi wawancara HR/Admin.';
                $application->interview_admin_approved_at = now();
                $application->interview_admin_id = $user->id;

                $newStatus = 'Rejected';
                $notes = $notes ?: $defaultTemplates['Rejected'];
                $historyNotes = "[Tidak Lolos Wawancara (Ditolak / Rejected)]." . ($notes ? " Catatan HR: {$notes}" : "");
                $customSuccessMsg = 'Lamaran berhasil ditolak pada tahap evaluasi wawancara HR/Admin.';
            }
        } elseif ($approvalType === 'test_recruiter_review') {
            $request->validate([
                'decision' => 'required|in:approved,rejected',
                'notes'    => 'nullable|string',
            ]);

            $decision = $request->input('decision');
            $application->test_recruiter_approval = $decision;
            $application->test_recruiter_notes = $notes;
            $application->test_recruiter_approved_at = now();
            $application->test_recruiter_id = $user->id;

            if ($decision === 'approved') {
                if ($application->test_admin_approval === 'approved') {
                    $newStatus = 'Shortlisted';
                    $notes = $notes ?: $defaultTemplates['Shortlisted'];
                    $historyNotes = "[Double Approved: Lolos Ujian Online]. Kedua pihak (Recruiter & HR) telah menyetujui hasil ujian." . ($notes ? " Catatan Recruiter: {$notes}" : "");
                    $customSuccessMsg = 'Persetujuan teknis Recruiter berhasil disimpan. Kedua belah pihak telah setuju, status pelamar resmi SHORTLISTED (Siap Wawancara).';
                } else {
                    $newStatus = $application->status ?: 'Reviewed';
                    $historyNotes = "[Persetujuan 1: Recruiter Teknis Disetujui] oleh {$user->name}. Menunggu verifikasi psikotes & kuota oleh HR/Admin." . ($notes ? " Catatan: {$notes}" : "");
                    $customSuccessMsg = 'Persetujuan teknis Recruiter berhasil disimpan. Menunggu verifikasi psikotes & kuota oleh HR/Admin.';
                }
            } else {
                $newStatus = $application->status ?: 'Reviewed';
                $historyNotes = "[Rekomendasi Tidak Lolos Ujian Teknis oleh Recruiter: {$user->name}]." . ($notes ? " Catatan: {$notes}" : "");
                $customSuccessMsg = 'Catatan rekomendasi penolakan ujian teknis berhasil disimpan untuk pertimbangan HR/Admin.';
            }
        } elseif ($approvalType === 'test_admin_review') {
            if (!$isAdmin) {
                return redirect()->route($redirectRoute)
                    ->with('error', 'Hanya HR/Admin yang memiliki wewenang verifikasi HR tahap ujian.');
            }

            $request->validate([
                'decision' => 'required|in:approved,rejected,override_shortlisted',
                'notes'    => 'nullable|string',
            ]);

            $decision = $request->input('decision');

            if ($decision === 'override_shortlisted') {
                $application->test_admin_approval = 'approved';
                $application->test_admin_notes = $notes ?: 'Disetujui melalui Admin Override (Hak Veto HR Ujian).';
                $application->test_admin_approved_at = now();
                $application->test_admin_id = $user->id;

                $application->test_recruiter_approval = 'approved';
                $application->test_recruiter_notes = $application->test_recruiter_notes ?: '[Bypassed by Admin Override]';
                $application->test_recruiter_approved_at = $application->test_recruiter_approved_at ?: now();

                $newStatus = 'Shortlisted';
                $notes = $notes ?: $defaultTemplates['Shortlisted'];
                $historyNotes = "[Admin Override: Lolos Ujian & Siap Wawancara (Hak Veto HR)]." . ($notes ? " Catatan HR: {$notes}" : "");
                $customSuccessMsg = 'Admin Override berhasil diterapkan! Pelamar resmi dinyatakan Lolos Ujian & Siap Wawancara (Shortlisted).';
            } elseif ($decision === 'approved') {
                $application->test_admin_approval = 'approved';
                $application->test_admin_notes = $notes ?: 'Persetujuan HR: Hasil psikotes (DISC & PAPI) serta evaluasi ujian disetujui.';
                $application->test_admin_approved_at = now();
                $application->test_admin_id = $user->id;

                if ($application->test_recruiter_approval === 'approved') {
                    $newStatus = 'Shortlisted';
                    $notes = $notes ?: $defaultTemplates['Shortlisted'];
                    $historyNotes = "[Double Approved: Lolos Ujian Online]. Kedua pihak (Recruiter & HR) telah menyetujui hasil ujian." . ($notes ? " Catatan HR: {$notes}" : "");
                    $customSuccessMsg = 'Persetujuan HR berhasil dicatat. Kedua pihak telah sepakat, status pelamar resmi SHORTLISTED (Siap Wawancara).';
                } else {
                    $newStatus = $application->status ?: 'Reviewed';
                    $historyNotes = "[Persetujuan HR Ujian Online]. Disetujui oleh HR/Admin (psikotes & administrasi). Menunggu konfirmasi koreksi teknis Recruiter." . ($notes ? " Catatan HR: {$notes}" : "");
                    $customSuccessMsg = 'Persetujuan HR berhasil disimpan. Status menunggu konfirmasi nilai/koreksi teknis dari Recruiter.';
                }
            } else { // rejected
                $application->test_admin_approval = 'rejected';
                $application->test_admin_notes = $notes ?: 'Hasil evaluasi ujian ditolak oleh HR/Admin.';
                $application->test_admin_approved_at = now();
                $application->test_admin_id = $user->id;

                $newStatus = 'Rejected';
                $notes = $notes ?: $defaultTemplates['Rejected'];
                $historyNotes = "[Tidak Lolos Ujian Online (Ditolak / Rejected)]." . ($notes ? " Catatan HR: {$notes}" : "");
                $customSuccessMsg = 'Lamaran dinyatakan tidak lolos evaluasi ujian oleh HR/Admin.';
            }
        } elseif ($approvalType === 'recruiter_review' || (!$isAdmin && $isRecruiter)) {
            $request->validate([
                'decision' => 'required|in:approved,rejected',
                'notes'    => 'nullable|string',
            ]);

            $decision = $request->input('decision');
            $application->recruiter_approval = $decision;
            $application->recruiter_notes = $notes;
            $application->recruiter_approved_at = now();
            $application->recruiter_id = $user->id;

            // KETENTUAN KHUSUS RECRUITER (Tahap Berkas):
            if ($application->admin_approval === 'approved') {
                $newStatus = 'Reviewed';
                $notes = $notes ?: $defaultTemplates['Reviewed'];
            } else {
                if ($decision === 'approved') {
                    $newStatus = 'Partial Approved';
                    $notes = $notes ?: ($defaultTemplates['Partial Approved'] . ' (Direkomendasikan Lolos oleh Recruiter: ' . $user->name . ')');
                } else {
                    $newStatus = $application->status ?: 'Submitted';
                    $notes = $notes ?: ('Rekomendasi penolakan teknis oleh Recruiter: ' . $user->name);
                }
            }
        } elseif ($approvalType === 'admin_review') {
            $request->validate([
                'decision' => 'required|in:approved,rejected,override_reviewed',
                'notes'    => 'nullable|string',
            ]);

            $decision = $request->input('decision');

            if ($decision === 'override_reviewed') {
                $application->admin_approval = 'approved';
                $application->admin_notes = $notes ?: 'Disetujui melalui Admin Override (Hak Veto HR Seleksi Berkas).';
                $application->admin_approved_at = now();
                $application->admin_id = $user->id;

                $application->recruiter_approval = 'approved';
                $application->recruiter_notes = $application->recruiter_notes ?: '[Bypassed by Admin Override]';
                $application->recruiter_approved_at = $application->recruiter_approved_at ?: now();

                $newStatus = 'Reviewed';
                $notes = $notes ?: $defaultTemplates['Reviewed'];
                $historyNotes = "[Admin Override: Lolos Berkas & Siap Ujian (Hak Veto HR)]." . ($notes ? " Catatan HR: {$notes}" : "");
                $customSuccessMsg = 'Admin Override berhasil diterapkan! Pelamar resmi dinyatakan Lolos Seleksi Berkas (Reviewed).';
            } elseif ($decision === 'approved') {
                $application->admin_approval = 'approved';
                $application->admin_notes = $notes ?: 'Persetujuan HR: Berkas lamaran disetujui.';
                $application->admin_approved_at = now();
                $application->admin_id = $user->id;

                if ($application->recruiter_approval === 'approved') {
                    $newStatus = 'Reviewed';
                    $notes = $notes ?: $defaultTemplates['Reviewed'];
                    $historyNotes = "[Double Approved: Lolos Berkas]. Kedua pihak (Recruiter & HR) telah menyetujui berkas pelamar." . ($notes ? " Catatan HR: {$notes}" : "");
                    $customSuccessMsg = 'Persetujuan HR berhasil dicatat. Kedua pihak telah sepakat, status pelamar resmi LOLOS BERKAS (Reviewed).';
                } else {
                    $newStatus = 'Partial Approved';
                    $notes = $notes ?: ($defaultTemplates['Partial Approved'] . ' (Disetujui oleh HR/Admin, menunggu verifikasi seleksi CV Recruiter)');
                    $historyNotes = "[Persetujuan HR Seleksi Berkas]. Disetujui oleh HR/Admin. Menunggu verifikasi seleksi CV dari Recruiter." . ($notes ? " Catatan HR: {$notes}" : "");
                    $customSuccessMsg = 'Persetujuan HR berhasil disimpan. Status menjadi Partial Approved (menunggu review seleksi CV oleh Recruiter).';
                }
            } else { // rejected
                $application->admin_approval = 'rejected';
                $application->admin_notes = $notes ?: 'Lamaran ditolak pada tahap seleksi administratif HR/Admin.';
                $application->admin_approved_at = now();
                $application->admin_id = $user->id;

                $newStatus = 'Rejected';
                $notes = $notes ?: $defaultTemplates['Rejected'];
                $historyNotes = "[Tidak Lolos Seleksi Berkas (Ditolak / Rejected)]." . ($notes ? " Catatan HR: {$notes}" : "");
                $customSuccessMsg = 'Lamaran dinyatakan tidak lolos seleksi berkas oleh HR/Admin.';
            }
        } else {
            // Direct status change (HANYA DAPAT DILAKUKAN OLEH ADMIN)
            if (!$isAdmin) {
                return redirect()->route($redirectRoute)
                    ->with('error', 'Recruiter tidak berwenang mengubah status lamaran secara langsung.');
            }

            $request->validate([
                'status' => 'required|string|max:255',
                'notes'  => 'nullable|string',
            ]);

            $newStatus = $request->input('status', $application->status);

            // Auto-sync approval flags if admin directly sets Reviewed, Shortlisted, Accepted or Rejected
            if ($newStatus === 'Reviewed') {
                $application->admin_approval = 'approved';
                $application->admin_approved_at = now();
                $application->admin_id = $user->id;
            } elseif ($newStatus === 'Shortlisted') {
                $application->admin_approval = 'approved';
                $application->admin_approved_at = now();
                $application->admin_id = $user->id;
                $application->test_admin_approval = 'approved';
                $application->test_admin_approved_at = now();
                $application->test_admin_id = $user->id;
            } elseif ($newStatus === 'Accepted') {
                $application->admin_approval = 'approved';
                $application->admin_approved_at = now();
                $application->admin_id = $user->id;
                $application->test_admin_approval = 'approved';
                $application->test_admin_approved_at = now();
                $application->test_admin_id = $user->id;
                $application->interview_admin_approval = 'approved';
                $application->interview_admin_approved_at = now();
                $application->interview_admin_id = $user->id;
                if ($application->interview_recruiter_approval !== 'approved') {
                    $application->interview_recruiter_approval = 'approved';
                    $application->interview_recruiter_notes = $application->interview_recruiter_notes ?: '[Bypassed by Admin Override]';
                    $application->interview_recruiter_approved_at = $application->interview_recruiter_approved_at ?: now();
                }
            } elseif ($newStatus === 'Rejected') {
                $application->admin_approval = 'rejected';
                $application->admin_approved_at = now();
                $application->admin_id = $user->id;
                if ($application->status === 'Interview') {
                    $application->interview_admin_approval = 'rejected';
                    $application->interview_admin_approved_at = now();
                    $application->interview_admin_id = $user->id;
                } else {
                    $application->test_admin_approval = 'rejected';
                    $application->test_admin_approved_at = now();
                    $application->test_admin_id = $user->id;
                }
            }

            if (empty(trim($notes ?? ''))) {
                $notes = $defaultTemplates[$newStatus] ?? $application->notes;
            }
        }

        $application->status = $newStatus;
        $application->notes = $notes;
        $application->save();

        // Catat perubahan status secara otomatis ke application_status_history
        ApplicationStatusHistory::create([
            'job_applications_id' => $application->id,
            'status'              => $newStatus,
            'notes'               => $historyNotes ?: $notes,
            'changed_by'          => $user->id ?? 1,
            'changed_at'          => now(),
        ]);

        // KETENTUAN PENGIRIMAN EMAIL:
        // 1. Recruiter TIDAK PERNAH diizinkan mengirim email ke pelamar (selalu false).
        // 2. Pada status 'Partial Approved' atau 'Interview' saat menunggu pihak lain, email tidak dikirim jika belum final.
        // 3. Email HANYA dikirim jika dipicu oleh Admin dan opsi send_email aktif.
        $shouldSendEmail = false;

        if ($isAdmin) {
            $shouldSendEmail = $request->has('send_email')
                ? $request->boolean('send_email')
                : true;

            if ($newStatus === 'Partial Approved' || ($newStatus === 'Interview' && $decision === 'approved' && $application->interview_recruiter_approval !== 'approved')) {
                $shouldSendEmail = false;
            }
        }

        $emailSent = false;
        $applicantEmail = $application->applicantProfile?->user?->email;

        if ($shouldSendEmail && $applicantEmail) {
            try {
                Mail::to($applicantEmail)->send(
                    new ApplicationStatusUpdatedMail($application, $newStatus, $notes)
                );
                $emailSent = true;
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim email notifikasi status lamaran: ' . $e->getMessage(), [
                    'application_id' => $application->id,
                    'recipient'      => $applicantEmail,
                    'status'         => $newStatus,
                ]);
            }
        }

        if ($customSuccessMsg) {
            $successMsg = $customSuccessMsg;
        } elseif (!$isAdmin) {
            $successMsg = 'Rekomendasi verifikasi berhasil dicatat. Status resmi lamaran akan diputuskan secara final oleh HR/Admin.';
        } else {
            $successMsg = 'Status lamaran berhasil diperbarui dan dicatat ke riwayat status.';
        }

        if ($emailSent) {
            $successMsg .= ' Notifikasi email otomatis telah berhasil dikirim ke ' . $applicantEmail . '.';
        } elseif ($newStatus === 'Partial Approved') {
            $successMsg .= ' Status saat ini Partial Approved (email belum dikirim ke pelamar sampai peninjauan akhir selesai).';
        }

        return redirect()->route($redirectRoute)
            ->with('update', $successMsg);
    }
}

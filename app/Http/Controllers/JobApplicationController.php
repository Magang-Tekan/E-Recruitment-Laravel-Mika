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

        // Cek kelengkapan data wajib
        if (!$applicantProfile->is_mandatory_complete) {
            return redirect()->route('jobs.show', $id)
                ->with('error', 'Mohon lengkapi seluruh data wajib profil Anda sebelum mengajukan lamaran.');
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
        $isAdmin = $user && ($user->role_id == 1 || strtolower($user->role?->name ?? '') === 'admin');
        $isRecruiter = $user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter);
        $redirectRoute = ($isRecruiter && !$isAdmin) ? 'recruiter.application' : 'admin.application';

        $isDesignatedRecruiter = $user && ((bool) $user->is_recruiter && !$isAdmin && $user->role_id != 2);
        if ($isDesignatedRecruiter) {
            if ($application->job?->reviewer_id != $user->id) {
                return redirect()->route($redirectRoute)
                    ->with('error', 'Anda hanya memiliki hak untuk meninjau lamaran pada lowongan yang ditugaskan kepada Anda.');
            }
        }

        if ($isRecruiter && !$isAdmin) {
            $job = $application->job;
            $isActive = $job && $job->status === 'Open' && (! $job->deadline || $job->deadline >= now()->toDateString());
            if (! $isActive) {
                return redirect()->route($redirectRoute)
                    ->with('error', 'Recruiter hanya diperbolehkan menyeleksi CV pada lowongan yang aktif.');
            }
        }

        $approvalType = $request->input('approval_type'); // 'recruiter_review', 'admin_review', or null (direct status update)
        $notes = $request->input('notes');

        $defaultTemplates = [
            'Reviewed'         => 'Selamat! Anda lolos seleksi berkas administrasi. Silakan lanjut kerjakan ujian online yang tersedia pada menu Riwayat Lamaran.',
            'Partial Approved' => 'Lamaran Anda telah disetujui pada salah satu tahap verifikasi (Partial Approved) dan sedang dalam proses peninjauan akhir.',
            'Shortlisted'      => 'Selamat! Anda dinyatakan lolos seluruh tahap seleksi berkas (Double Approved) dan masuk ke dalam daftar kandidat terpilih (Shortlisted). Kami akan segera menginformasikan jadwal wawancara.',
            'Interview'        => 'Anda diundang untuk mengikuti tahap wawancara. Silakan periksa jadwal dan informasi meeting yang tertera.',
            'Accepted'         => 'Selamat! Anda dinyatakan DITERIMA untuk bergabung bersama kami. Tim HR akan segera menghubungi Anda terkait proses offering dan onboarding.',
            'Rejected'         => 'Terima kasih atas partisipasi Anda. Saat ini kualifikasi Anda belum sesuai dengan kriteria yang kami butuhkan. Tetap semangat dan sukses untuk kesempatan berikutnya.',
            'Submitted'        => 'Lamaran Anda telah kami terima dan sedang dalam proses seleksi berkas oleh tim rekruter.',
        ];

        if ($approvalType === 'recruiter_review') {
            $request->validate([
                'decision' => 'required|in:approved,rejected',
                'notes'    => 'nullable|string',
            ]);

            $decision = $request->input('decision');
            $application->recruiter_approval = $decision;
            $application->recruiter_notes = $notes;
            $application->recruiter_approved_at = now();
            $application->recruiter_id = $user->id;

            if ($decision === 'rejected') {
                $newStatus = 'Rejected';
                $notes = $notes ?: 'Kualifikasi CV/portofolio belum memenuhi kriteria teknis oleh penilai/recruiter.';
            } else { // approved
                if ($application->admin_approval === 'approved') {
                    $newStatus = 'Reviewed';
                    $notes = $notes ?: $defaultTemplates['Reviewed'];
                } else {
                    $newStatus = 'Partial Approved';
                    $notes = $notes ?: ($defaultTemplates['Partial Approved'] . ' (Disetujui oleh Tim Reviewer: ' . $user->name . ')');
                }
            }
        } elseif ($approvalType === 'admin_review') {
            $request->validate([
                'decision' => 'required|in:approved,rejected',
                'notes'    => 'nullable|string',
            ]);

            $decision = $request->input('decision');
            $application->admin_approval = $decision;
            $application->admin_notes = $notes;
            $application->admin_approved_at = now();
            $application->admin_id = $user->id;

            if ($decision === 'rejected') {
                $newStatus = 'Rejected';
                $notes = $notes ?: 'Lamaran ditolak pada tahap seleksi administratif HR/Admin.';
            } else { // approved
                if ($application->recruiter_approval === 'approved') {
                    $newStatus = 'Reviewed';
                    $notes = $notes ?: $defaultTemplates['Reviewed'];
                } else {
                    $newStatus = 'Partial Approved';
                    $notes = $notes ?: ($defaultTemplates['Partial Approved'] . ' (Disetujui oleh HR/Admin)');
                }
            }
        } else {
            // Direct status change (Admin standard workflow)
            $request->validate([
                'status' => 'required|string|max:255',
                'notes'  => 'nullable|string',
            ]);

            $newStatus = $request->input('status', $application->status);

            // Auto-sync approval flags if admin directly sets Reviewed, Shortlisted or Rejected
            if ($newStatus === 'Reviewed' || $newStatus === 'Shortlisted' || $newStatus === 'Accepted') {
                $application->admin_approval = 'approved';
                $application->admin_approved_at = now();
                $application->admin_id = $user->id;
            } elseif ($newStatus === 'Rejected') {
                $application->admin_approval = 'rejected';
                $application->admin_approved_at = now();
                $application->admin_id = $user->id;
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
            'notes'               => $notes,
            'changed_by'          => $user->id ?? 1,
            'changed_at'          => now(),
        ]);

        // Kirim email notifikasi ke pelamar jika diaktifkan (default: true)
        // KETENTUAN KHUSUS: Jangan kirim email saat status masih 'Partial Approved' (karena baru 1 pihak yang menyetujui).
        // Email ke pelamar HANYA dikirim setelah kedua pihak telah setuju (status 'Reviewed' / Lolos Berkas & lanjut tes),
        // atau saat ditolak/perubahan status final lainnya.
        $shouldSendEmail = $request->has('send_email')
            ? $request->boolean('send_email')
            : true;

        if ($newStatus === 'Partial Approved') {
            $shouldSendEmail = false;
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

        $successMsg = 'Status lamaran berhasil diperbarui dan dicatat ke riwayat status.';
        if ($emailSent) {
            $successMsg .= ' Notifikasi email otomatis telah berhasil dikirim ke ' . $applicantEmail . '.';
        } elseif ($newStatus === 'Partial Approved') {
            $successMsg .= ' Status saat ini Partial Approved (email belum dikirim ke pelamar sampai kedua pihak selesai menyetujui).';
        }

        return redirect()->route($redirectRoute)
            ->with('update', $successMsg);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\InterviewSchedule;
use App\Models\JobApplication;
use App\Models\ApplicationStatusHistory;
use App\Mail\ApplicationStatusUpdatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InterviewScheduleController extends Controller
{
    /**
     * Helper to get redirect route name based on current user role
     */
    private function getRedirectRoute(): string
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
        $isRecruiter = request()->is('recruiter/*') || request()->routeIs('recruiter.*') || ($user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter));
        return (request()->is('recruiter/*') || ($isRecruiter && !$isAdmin)) ? 'recruiter.interview_schedule' : 'admin.interview_schedule';
    }

    /**
     * Store a newly created interview schedule (Create)
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
        if (!$isAdmin) {
            abort(403, 'Akses ditolak. Hanya HR/Admin yang memiliki wewenang untuk membuat jadwal wawancara.');
        }

        $request->validate([
            'job_applications_id' => 'required|exists:job_applications,id',
            'users_id'            => 'required|exists:users,id',
            'interview_date'      => 'required|date',
            'interview_type'      => 'required|in:online,offline',
            'location'            => 'required|string|max:255',
            'meeting_link'        => 'nullable|string|max:500',
            'status'              => 'nullable|string|in:Scheduled,Completed,Rescheduled,Cancelled,No Show',
            'notes'               => 'nullable|string|max:1000',
            'send_email'          => 'nullable|in:0,1',
        ]);

        $type = $request->input('interview_type');
        $location = $request->input('location');
        $meetingLink = $request->input('meeting_link');

        // Cegah duplikasi: jika kandidat sudah memiliki jadwal aktif yang belum dibatalkan
        $existingActiveSchedule = InterviewSchedule::where('job_applications_id', $request->job_applications_id)
            ->whereNotIn('status', ['Cancelled'])
            ->first();
        if ($existingActiveSchedule) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Kandidat ini sudah memiliki jadwal wawancara aktif. Silakan ubah jadwal yang sudah ada pada tabel.');
        }

        // Jika online dan meeting_link diisi namun lokasi kosong / default
        if ($type === 'online') {
            if (empty($location)) {
                $location = 'Online (Video Conference)';
            }
        } else {
            // Jika offline, kosongkan link meeting
            $meetingLink = null;
        }

        try {
            DB::beginTransaction();

            InterviewSchedule::create([
                'job_applications_id' => $request->job_applications_id,
                'users_id'            => $request->users_id,
                'interview_date'      => $request->interview_date,
                'location'            => $location,
                'meeting_link'        => $meetingLink,
                'status'              => $request->input('status', 'Scheduled'),
            ]);

            // Status lamaran otomatis → Interview
            $application   = JobApplication::find($request->job_applications_id);
            $user          = auth()->user();
            $isAdmin       = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
            // Recruiter tidak diizinkan mengirim email langsung ke pelamar
            $sendEmail     = $isAdmin && ($request->input('send_email', '0') === '1');
            $targetStatus  = 'Interview';

            if ($application) {
                $prevStatus = $application->status;

                // Update status ke Interview jika belum
                if (!in_array($prevStatus, ['Interview', 'Accepted'])) {
                    $application->update([
                        'status' => $targetStatus,
                        'notes'  => $request->notes ?? $application->notes,
                    ]);

                    ApplicationStatusHistory::create([
                        'job_applications_id' => $application->id,
                        'status'              => $targetStatus,
                        'notes'               => 'Dijadwalkan wawancara (' . ucfirst($type) . ') pada ' . \Carbon\Carbon::parse($request->interview_date)->translatedFormat('d M Y H:i') . ' WIB. ' . ($request->notes ? 'Catatan: ' . $request->notes : ''),
                        'changed_by'          => auth()->id() ?? $request->users_id,
                        'changed_at'          => now(),
                    ]);
                }

                // Kirim email notifikasi jika diminta
                if ($sendEmail) {
                    try {
                        $application->load(['job.company', 'applicantProfile.user']);
                        $email = $application->applicantProfile?->user?->email;
                        if ($email) {
                            \Illuminate\Support\Facades\Mail::to($email)
                                ->send(new \App\Mail\ApplicationStatusUpdatedMail(
                                    $application,
                                    $targetStatus,
                                    $request->notes
                                ));
                        }
                    } catch (\Exception $mailEx) {
                        \Illuminate\Support\Facades\Log::warning('Gagal kirim email notifikasi wawancara: ' . $mailEx->getMessage());
                    }
                }
            }

            DB::commit();

            $emailNote = $sendEmail ? ' Email notifikasi telah dikirim ke kandidat.' : '';
            return redirect()->route($this->getRedirectRoute())
                ->with('create', 'Jadwal wawancara (' . ucfirst($type) . ') berhasil dibuat dan dicatat ke sistem.' . $emailNote);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat jadwal wawancara: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified interview schedule (Update)
     */
    public function update(Request $request, string $id)
    {
        $schedule = InterviewSchedule::findOrFail($id);

        $request->validate([
            'users_id'       => 'required|exists:users,id',
            'interview_date' => 'required|date',
            'interview_type' => 'required|in:online,offline',
            'location'       => 'required|string|max:255',
            'meeting_link'   => 'nullable|string|max:500',
            'status'         => 'required|string|in:Scheduled,Completed,Rescheduled,Cancelled,No Show',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $type = $request->input('interview_type');
        $location = $request->input('location');
        $meetingLink = $request->input('meeting_link');

        if ($type === 'offline') {
            $meetingLink = null;
        }

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
            $isRecruiter = request()->is('recruiter/*') || request()->routeIs('recruiter.*') || ($user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter));

            if ($isAdmin) {
                $schedule->update([
                    'users_id'       => $request->users_id,
                    'interview_date' => $request->interview_date,
                    'location'       => $location,
                    'meeting_link'   => $meetingLink,
                    'status'         => $request->status,
                ]);
            } else {
                // Recruiter hanya berwenang memberikan evaluasi/approval wawancara, tidak dapat mengubah data jadwal
                $schedule->update([
                    'status' => 'Completed',
                ]);
            }

            // Tangani keputusan hasil wawancara dan update status lamaran jika ada
            $decision = $request->input('application_decision', 'keep');
            $score = $request->input('interview_score');
            $notes = $request->input('notes');

            if ($schedule->jobApplication) {
                $application = $schedule->jobApplication;
                $targetStatus = $application->status;
                $historyNotes = "";
                $scoreText = (!is_null($score) && $score !== '') ? " Nilai: {$score}/100." : "";

                if ($isAdmin) {
                    if ($decision === 'admin_override_accepted') {
                        // ⚡ ADMIN OVERRIDE / EMERGENCY APPROVAL (Kuasa Penuh Admin)
                        $application->interview_admin_approval = 'approved';
                        $application->interview_admin_notes = $notes ?: 'Disetujui melalui Admin Override (Emergency Approval).';
                        $application->interview_admin_id = $user->id;
                        $application->interview_admin_approved_at = now();

                        $application->interview_recruiter_approval = 'approved';
                        $application->interview_recruiter_notes = $application->interview_recruiter_notes ?: '[Bypassed by Admin Override]';
                        $application->interview_recruiter_approved_at = $application->interview_recruiter_approved_at ?: now();

                        $targetStatus = 'Accepted';
                        $historyNotes = "[⚡ Admin Override: Diterima Resmi (Emergency Approval)].{$scoreText}" . ($notes ? " Catatan HR: {$notes}" : "");
                    } elseif ($decision === 'admin_approved') {
                        // Persetujuan HR/Admin (Normal Flow)
                        $application->interview_admin_approval = 'approved';
                        $application->interview_admin_notes = $notes ?: 'Persetujuan HR: Gaji, psikotes, kuota, dan kontrak kerja disetujui.';
                        $application->interview_admin_id = $user->id;
                        $application->interview_admin_approved_at = now();

                        if ($application->interview_recruiter_approval === 'approved') {
                            $targetStatus = 'Accepted';
                            $historyNotes = "[Double Approved: Lolos Wawancara].{$scoreText} Kedua pihak (Recruiter & HR) telah sepakat menyetujui kandidat." . ($notes ? " Catatan HR: {$notes}" : "");
                        } else {
                            $targetStatus = 'Interview';
                            $historyNotes = "[Persetujuan HR Wawancara].{$scoreText} Disetujui oleh HR/Admin. Menunggu konfirmasi Recruiter teknis." . ($notes ? " Catatan HR: {$notes}" : "");
                        }
                    } elseif ($decision === 'Accepted') {
                        // Jika Admin langsung klik Accepted
                        $application->interview_admin_approval = 'approved';
                        $application->interview_admin_notes = $notes;
                        $application->interview_admin_id = $user->id;
                        $application->interview_admin_approved_at = now();

                        $targetStatus = 'Accepted';
                        $historyNotes = "[Lolos Wawancara (Diterima / Accepted)].{$scoreText}" . ($notes ? " Catatan: {$notes}" : "");
                    } elseif ($decision === 'Rejected') {
                        $application->interview_admin_approval = 'rejected';
                        $application->interview_admin_notes = $notes;
                        $application->interview_admin_id = $user->id;
                        $application->interview_admin_approved_at = now();

                        $targetStatus = 'Rejected';
                        $historyNotes = "[Tidak Lolos Wawancara (Ditolak / Rejected)]." . ($notes ? " Catatan: {$notes}" : "");
                    } elseif ($decision === 'Interview') {
                        $targetStatus = 'Interview';
                        $historyNotes = "[Lolos ke Tahap Wawancara Berikutnya]." . ($notes ? " Catatan: {$notes}" : "");
                    } else {
                        // keep
                        $historyNotes = "[Sesi Wawancara Selesai (Completed)].{$scoreText}" . ($notes ? " Catatan: {$notes}" : "");
                    }
                } else {
                    // Recruiter (Pewawancara)
                    if ($decision === 'recruiter_approved' || $decision === 'Accepted' || $decision === 'approved') {
                        $application->interview_recruiter_approval = 'approved';
                        $application->interview_recruiter_notes = $notes ?: 'Persetujuan Teknis: Recruiter menyetujui kompetensi kandidat layak masuk tim.';
                        $application->interview_recruiter_id = $user->id;
                        $application->interview_recruiter_approved_at = now();

                        if ($application->interview_admin_approval === 'approved') {
                            $targetStatus = 'Accepted';
                            $historyNotes = "[Double Approved: Lolos Wawancara].{$scoreText} Kedua pihak (Recruiter & HR) telah sepakat menyetujui kandidat." . ($notes ? " Catatan Recruiter: {$notes}" : "");
                        } else {
                            $targetStatus = 'Interview';
                            $historyNotes = "[Persetujuan Teknis Wawancara].{$scoreText} Disetujui oleh Recruiter ({$user->name}). Menunggu keputusan HR/Admin." . ($notes ? " Catatan Recruiter: {$notes}" : "");
                        }
                    } elseif ($decision === 'recruiter_rejected' || $decision === 'Rejected' || $decision === 'rejected') {
                        $application->interview_recruiter_approval = 'rejected';
                        $application->interview_recruiter_notes = $notes ?: 'Rekomendasi penolakan hasil wawancara teknis.';
                        $application->interview_recruiter_id = $user->id;
                        $application->interview_recruiter_approved_at = now();

                        $targetStatus = 'Interview';
                        $historyNotes = "[Rekomendasi Tidak Lolos Wawancara oleh Recruiter: {$user->name}].{$scoreText}" . ($notes ? " Catatan: {$notes}" : "");
                    } else {
                        $historyNotes = "[Evaluasi Wawancara Disimpan oleh Recruiter].{$scoreText}" . ($notes ? " Catatan: {$notes}" : "");
                    }
                }

                $application->update([
                    'status' => $targetStatus,
                    'notes'  => $notes ? $notes : $application->notes,
                ]);

                ApplicationStatusHistory::create([
                    'job_applications_id' => $application->id,
                    'status'              => $targetStatus,
                    'notes'               => $historyNotes,
                    'changed_by'          => auth()->id() ?? 1,
                    'changed_at'          => now(),
                ]);

                // Notifikasi Email Otomatis:
                // 1. Jika HR/Admin: kirim jika send_email bernilai true (atau default true untuk status final Accepted / Rejected).
                // 2. Jika Recruiter: kirim jika persetujuan melengkapi Double Approval sehingga resmi Accepted.
                $defaultStatusNotes = [
                    'Accepted' => 'Selamat! Anda dinyatakan DITERIMA untuk bergabung bersama kami. Tim HR akan segera menghubungi Anda terkait proses offering dan onboarding.',
                    'Rejected' => 'Terima kasih atas partisipasi Anda dalam tahap wawancara. Saat ini kualifikasi Anda belum sesuai dengan kriteria yang kami butuhkan.',
                ];

                $shouldSendEmail = false;
                if ($isAdmin) {
                    $shouldSendEmail = $request->has('send_email')
                        ? $request->boolean('send_email')
                        : in_array($targetStatus, ['Accepted', 'Rejected']);
                } else {
                    if ($targetStatus === 'Accepted') {
                        $shouldSendEmail = true;
                    }
                }

                $emailSent = false;
                if ($shouldSendEmail) {
                    try {
                        $application->load(['job.company', 'applicantProfile.user']);
                        $applicantEmail = $application->applicantProfile?->user?->email;
                        if ($applicantEmail) {
                            $mailNotes = $notes ?: ($defaultStatusNotes[$targetStatus] ?? null);
                            Mail::to($applicantEmail)->send(
                                new ApplicationStatusUpdatedMail($application, $targetStatus, $mailNotes)
                            );
                            $emailSent = true;
                        }
                    } catch (\Throwable $mailEx) {
                        Log::warning('Gagal kirim email notifikasi hasil wawancara: ' . $mailEx->getMessage(), [
                            'application_id' => $application->id,
                            'status'         => $targetStatus,
                        ]);
                    }
                }
            }

            DB::commit();

            $emailNote = ($emailSent ?? false) ? ' Notifikasi email resmi telah berhasil dikirim ke kandidat.' : '';
            return redirect()->route($this->getRedirectRoute())
                ->with('update', 'Hasil wawancara dan status pelamar berhasil diperbarui.' . $emailNote);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui jadwal wawancara: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified interview schedule (Delete)
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
        if (!$isAdmin) {
            abort(403, 'Akses ditolak. Hanya HR/Admin yang memiliki wewenang untuk menghapus jadwal wawancara.');
        }

        try {
            $schedule = InterviewSchedule::findOrFail($id);
            $schedule->delete();

            return redirect()->route($this->getRedirectRoute())
                ->with('delete', 'Jadwal wawancara berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus jadwal wawancara: ' . $e->getMessage());
        }
    }
}

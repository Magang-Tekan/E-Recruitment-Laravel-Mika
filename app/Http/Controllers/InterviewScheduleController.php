<?php

namespace App\Http\Controllers;

use App\Models\InterviewSchedule;
use App\Models\JobApplication;
use App\Models\ApplicationStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InterviewScheduleController extends Controller
{
    /**
     * Helper to get redirect route name based on current user role
     */
    private function getRedirectRoute(): string
    {
        $user = auth()->user();
        $isRecruiter = $user && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter');
        return $isRecruiter ? 'recruiter.interview_schedule' : 'admin.interview_schedule';
    }

    /**
     * Store a newly created interview schedule (Create)
     */
    public function store(Request $request)
    {
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
            $sendEmail      = $request->input('send_email', '0') === '1';
            $targetStatus   = 'Interview';

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

            $oldStatus = $schedule->status;
            $newStatus = $request->status;

            $schedule->update([
                'users_id'       => $request->users_id,
                'interview_date' => $request->interview_date,
                'location'       => $location,
                'meeting_link'   => $meetingLink,
                'status'         => $newStatus,
            ]);

            // Tangani keputusan hasil wawancara dan update status lamaran jika ada
            $decision = $request->input('application_decision', 'keep');
            $score = $request->input('interview_score');
            $notes = $request->input('notes');

            if ($schedule->jobApplication) {
                $application = $schedule->jobApplication;
                $targetStatus = in_array($decision, ['Accepted', 'Rejected', 'Interview']) ? $decision : $application->status;

                $scoreText = (!is_null($score) && $score !== '') ? " Nilai: {$score}/100." : "";
                $decisionLabel = match($decision) {
                    'Accepted'  => 'Lolos Wawancara (Diterima / Accepted)',
                    'Rejected'  => 'Tidak Lolos Wawancara (Ditolak / Rejected)',
                    'Interview' => 'Lolos ke Tahap Wawancara Berikutnya',
                    default     => 'Sesi Wawancara Selesai (Completed)',
                };

                $historyNotes = "[{$decisionLabel}].{$scoreText}" . ($notes ? " Catatan Evaluasi: {$notes}" : "");

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
            }

            DB::commit();

            return redirect()->route($this->getRedirectRoute())
                ->with('update', 'Hasil wawancara dan status pelamar berhasil diperbarui.');
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

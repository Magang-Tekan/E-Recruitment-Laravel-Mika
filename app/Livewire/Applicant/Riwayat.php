<?php

namespace App\Livewire\Applicant;

use App\Models\JobApplication;
use App\Services\TestSequenceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Riwayat extends Component
{
    public $search = '';
    public $statusFilter = 'all';
    public $selectedApplicationId = null;
    public $showDetailModal = false;
    public $showWithdrawModal = false;
    public $withdrawId = null;
    public $withdrawJobTitle = '';
    public $withdrawReason = '';

    #[On('application-updated')]
    public function refreshList()
    {
        // Refresh component
    }

    public function openWithdrawModal($id)
    {
        $user = Auth::user();
        if (!$user || !$user->applicantProfile) return;

        $application = JobApplication::with('job')
            ->where('profile_id', $user->applicantProfile->id)
            ->find($id);

        if ($application) {
            $this->withdrawId = $application->id;
            $this->withdrawJobTitle = $application->job?->title ?? '';
            $this->withdrawReason = '';
            $this->showWithdrawModal = true;
        }
    }

    public function closeWithdrawModal()
    {
        $this->showWithdrawModal = false;
        $this->withdrawId = null;
        $this->withdrawJobTitle = '';
        $this->withdrawReason = '';
    }

    public function confirmWithdraw()
    {
        $user = Auth::user();
        if (!$user || !$user->applicantProfile) return;

        $application = JobApplication::with('job')
            ->where('profile_id', $user->applicantProfile->id)
            ->where('id', $this->withdrawId)
            ->first();

        if (!$application) {
            session()->flash('error', __('Data lamaran tidak ditemukan.'));
            $this->closeWithdrawModal();
            return;
        }

        // Hanya boleh dibatalkan jika masih Submitted / pending / applied
        if (!in_array(strtolower($application->status), ['submitted', 'pending', 'applied'])) {
            session()->flash('error', __('Lamaran ini tidak dapat dibatalkan karena sudah masuk ke tahapan seleksi oleh tim rekrutmen.'));
            $this->closeWithdrawModal();
            return;
        }

        $reason = trim($this->withdrawReason) ?: __('Salah memilih posisi lowongan pekerjaan');

        \Illuminate\Support\Facades\DB::transaction(function () use ($application, $user, $reason) {
            $application->update([
                'status' => 'Withdrawn',
                'notes'  => 'Dibatalkan oleh Pelamar. Alasan: ' . $reason,
            ]);

            \App\Models\ApplicationStatusHistory::create([
                'job_applications_id' => $application->id,
                'status'              => 'Withdrawn',
                'notes'               => 'Lamaran dibatalkan oleh pelamar sendiri. Alasan: ' . $reason,
                'changed_by'          => $user->id,
                'changed_at'          => now(),
            ]);
        });

        session()->flash('withdraw_success', __('Lamaran untuk posisi :title berhasil dibatalkan.', ['title' => $application->job?->title ?? '']));
        $this->closeWithdrawModal();
    }

    public function openDetail($id)
    {
        $user = Auth::user();
        if (!$user || !$user->applicantProfile) return;

        $application = JobApplication::with([
            'job.company',
            'job.department',
            'statusHistories.changedBy',
            'interviewSchedules',
            'testAttempts.test'
        ])
        ->where('profile_id', $user->applicantProfile->id)
        ->find($id);

        if ($application) {
            $this->selectedApplicationId = $application->id;
            $this->showDetailModal = true;
        }
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedApplicationId = null;
    }

    public function render()
    {
        $user = Auth::user();
        $applicantProfile = $user ? $user->applicantProfile : null;

        $stats = [
            'total' => 0,
            'process' => 0,
            'accepted' => 0,
            'rejected' => 0,
        ];

        $applications = collect();
        $selectedApplication = null;

        if ($applicantProfile) {
            $baseQuery = JobApplication::where('profile_id', $applicantProfile->id);

            $stats['total'] = (clone $baseQuery)->count();
            $stats['process'] = (clone $baseQuery)->whereIn('status', ['Submitted', 'Reviewed', 'Shortlisted', 'Interview', 'applied', 'pending', 'screening'])->count();
            $stats['accepted'] = (clone $baseQuery)->whereIn('status', ['Accepted', 'accepted'])->count();
            $stats['rejected'] = (clone $baseQuery)->whereIn('status', ['Rejected', 'rejected', 'Withdrawn', 'withdrawn', 'dibatalkan'])->count();

            $query = JobApplication::with([
                'job.company', 
                'job.department', 
                'job.tests',
                'testAttempts.test',
                'statusHistories', 
                'interviewSchedules.user'
            ])->where('profile_id', $applicantProfile->id);

            if (!empty($this->search)) {
                $search = '%' . $this->search . '%';
                $query->whereHas('job', function ($q) use ($search) {
                    $q->where('title', 'like', $search)
                        ->orWhereHas('company', function ($c) use ($search) {
                            $c->where('name', 'like', $search);
                        })
                        ->orWhereHas('department', function ($d) use ($search) {
                            $d->where('name', 'like', $search);
                        });
                });
            }

            if ($this->statusFilter !== 'all') {
                $statusVal = $this->statusFilter;
                $indoMap = [
                    'diajukan'     => 'Submitted',
                    'lolos berkas' => 'Reviewed',
                    'lolos_berkas' => 'Reviewed',
                    'lolos ujian'  => 'Shortlisted',
                    'lolos_ujian'  => 'Shortlisted',
                    'wawancara'    => 'Interview',
                    'diterima'     => 'Accepted',
                    'ditolak'      => 'Rejected',
                    'dibatalkan'   => 'Withdrawn',
                    'withdrawn'    => 'Withdrawn',
                ];
                if (isset($indoMap[strtolower($statusVal)])) {
                    $statusVal = $indoMap[strtolower($statusVal)];
                }
                if ($statusVal === 'process') {
                    $query->whereIn('status', ['Submitted', 'Reviewed', 'Shortlisted', 'Interview', 'applied', 'pending', 'screening']);
                } else {
                    $query->whereRaw('LOWER(status) = ?', [strtolower($statusVal)]);
                }
            }

            $applications = $query->orderBy('applied_at', 'desc')->get();

            // Bangun test progress map: application_id => array of test progress
            $testProgressMap = [];
            foreach ($applications as $appItem) {
                if ($appItem->job && $appItem->job->tests && $appItem->job->tests->count() > 1) {
                    // Hanya hitung jika ada lebih dari 1 test (untuk efisiensi)
                    $testProgressMap[$appItem->id] = TestSequenceService::buildTestProgress($appItem);
                }
            }

            // Ambil jadwal wawancara aktif / mendatang untuk notifikasi pelamar
            $upcomingInterviews = \App\Models\InterviewSchedule::with([
                'jobApplication.job.company',
                'jobApplication.job.department',
                'user'
            ])
            ->whereHas('jobApplication', function ($q) use ($applicantProfile) {
                $q->where('profile_id', $applicantProfile->id);
            })
            ->whereIn('status', ['Scheduled', 'Rescheduled'])
            ->where('interview_date', '>=', now()->subHours(4))
            ->orderBy('interview_date', 'asc')
            ->get();

            if ($this->selectedApplicationId) {
                $selectedApplication = JobApplication::with([
                    'job.company',
                    'job.department',
                    'statusHistories.changedBy',
                    'interviewSchedules.user',
                    'testAttempts.test'
                ])
                ->where('profile_id', $applicantProfile->id)
                ->find($this->selectedApplicationId);
            }
        }

        return view('livewire.applicant.riwayat', [
            'applications'      => $applications,
            'selectedApplication' => $selectedApplication,
            'upcomingInterviews' => $upcomingInterviews ?? collect(),
            'stats'             => $stats,
            'testProgressMap'   => $testProgressMap ?? [],
        ]);
    }
}

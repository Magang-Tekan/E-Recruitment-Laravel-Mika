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

    #[On('application-updated')]
    public function refreshList()
    {
        // Refresh component
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
            $stats['rejected'] = (clone $baseQuery)->whereIn('status', ['Rejected', 'rejected'])->count();

            $query = JobApplication::with([
                'job.company', 
                'job.department', 
                'job.tests',
                'testAttempts.test',
                'statusHistories', 
                'interviewSchedules'
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
                if ($this->statusFilter === 'process') {
                    $query->whereIn('status', ['Submitted', 'Reviewed', 'Shortlisted', 'Interview', 'applied', 'pending', 'screening']);
                } else {
                    $query->where('status', $this->statusFilter);
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

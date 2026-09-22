<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InterviewSchedule;
use App\Models\JobApplication;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Livewire\Traits\WithTableSkeleton;

class InterviewScheduleTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $statusFilter = '';
    public $timeFilter = 'all'; // all, today, upcoming, past
    public $typeFilter = 'all'; // all, online, offline
    public $companyFilter = '';
    public $jobFilter = '';
    public $interviewerFilter = '';
    public $perPage = 10;

    public $sortField = 'interview_date';
    public $sortDirection = 'asc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTimeFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingCompanyFilter()
    {
        $this->jobFilter = '';
        $this->resetPage();
    }

    public function updatingJobFilter()
    {
        $this->resetPage();
    }

    public function updatingInterviewerFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->companyFilter = '';
        $this->jobFilter = '';
        $this->statusFilter = '';
        $this->timeFilter = 'all';
        $this->typeFilter = 'all';
        $this->interviewerFilter = '';
        $this->sortField = 'interview_date';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $now = Carbon::now();
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        // Calculate Stats
        $stats = [
            'total'     => InterviewSchedule::count(),
            'today'     => InterviewSchedule::whereBetween('interview_date', [$todayStart, $todayEnd])->count(),
            'upcoming'  => InterviewSchedule::where('interview_date', '>', $now)->where('status', 'Scheduled')->count(),
            'completed' => InterviewSchedule::where('status', 'Completed')->count(),
            'accepted'  => InterviewSchedule::whereHas('jobApplication', function($q) {
                $q->whereRaw('LOWER(status) = ?', ['accepted']);
            })->count(),
            'rejected'  => InterviewSchedule::whereHas('jobApplication', function($q) {
                $q->whereRaw('LOWER(status) = ?', ['rejected']);
            })->count(),
            'online'    => InterviewSchedule::where(function ($q) {
                $q->whereNotNull('meeting_link')
                  ->where('meeting_link', '!=', '')
                  ->orWhereRaw('LOWER(location) LIKE ?', ['%online%']);
            })->count(),
            'offline'   => InterviewSchedule::where(function ($q) {
                $q->whereNull('meeting_link')
                  ->orWhere('meeting_link', '=', '');
            })->whereRaw('LOWER(location) NOT LIKE ?', ['%online%'])->count(),
        ];

        // Base Query
        $query = InterviewSchedule::with([
            'jobApplication.applicantProfile.user',
            'jobApplication.job.company',
            'jobApplication.job.department',
            'user.employeeProfile', // interviewer profile & photo
        ]);

        // Search Filter
        if (!empty($this->search)) {
            $search = strtolower(trim($this->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(location) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(meeting_link) LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('LOWER(status) LIKE ?', ['%' . $search . '%'])
                  ->orWhereHas('jobApplication', function ($jq) use ($search) {
                      $jq->whereRaw('LOWER(status) LIKE ?', ['%' . $search . '%']);
                  })
                  ->orWhereHas('jobApplication.applicantProfile', function ($apq) use ($search) {
                      $apq->whereRaw('LOWER(full_name) LIKE ?', ['%' . $search . '%'])
                          ->orWhereRaw('LOWER(city) LIKE ?', ['%' . $search . '%'])
                          ->orWhereHas('user', function ($uq) use ($search) {
                              $uq->whereRaw('LOWER(email) LIKE ?', ['%' . $search . '%']);
                          });
                  })
                  ->orWhereHas('jobApplication.job', function ($jq) use ($search) {
                      $jq->whereRaw('LOWER(title) LIKE ?', ['%' . $search . '%'])
                        ->orWhereHas('company', function ($cq) use ($search) {
                            $cq->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                        });
                  })
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
                        ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $search . '%']);
                  });
            });
        }

        // Status Filter (Support both schedule status and application decision status)
        if (!empty($this->statusFilter)) {
            if (in_array(strtolower($this->statusFilter), ['accepted', 'rejected'])) {
                $query->whereHas('jobApplication', function ($q) {
                    $q->whereRaw('LOWER(status) = ?', [strtolower($this->statusFilter)]);
                });
            } else {
                $query->where('status', $this->statusFilter);
            }
        }

        // Time Filter
        if ($this->timeFilter === 'today') {
            $query->whereBetween('interview_date', [$todayStart, $todayEnd]);
        } elseif ($this->timeFilter === 'upcoming') {
            $query->where('interview_date', '>=', $now);
        } elseif ($this->timeFilter === 'past') {
            $query->where('interview_date', '<', $now);
        }

        // Type Filter (Online / Offline)
        if ($this->typeFilter === 'online') {
            $query->where(function ($q) {
                $q->whereNotNull('meeting_link')
                  ->where('meeting_link', '!=', '')
                  ->orWhereRaw('LOWER(location) LIKE ?', ['%online%']);
            });
        } elseif ($this->typeFilter === 'offline') {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('meeting_link')
                        ->orWhere('meeting_link', '=', '');
                })->whereRaw('LOWER(location) NOT LIKE ?', ['%online%']);
            });
        }

        // Company Filter
        if (!empty($this->companyFilter)) {
            $query->whereHas('jobApplication.job', function ($jq) {
                $jq->where('company_id', $this->companyFilter);
            });
        }

        // Job Filter
        if (!empty($this->jobFilter)) {
            $query->whereHas('jobApplication', function ($jaq) {
                $jaq->where('job_id', $this->jobFilter);
            });
        }

        // Interviewer Filter
        if (!empty($this->interviewerFilter)) {
            $query->where('users_id', $this->interviewerFilter);
        }

        // Sorting: Jadwal mendatang (upcoming) paling atas secara default
        if ($this->sortField === 'interview_date') {
            if ($this->sortDirection === 'asc') {
                // Mendatang paling atas (diurutkan dari waktu terdekat), lalu jadwal yang sudah lewat (terbaru dulu)
                $schedules = $query->orderByRaw('CASE WHEN interview_date >= ? THEN 0 ELSE 1 END ASC', [$now])
                                   ->orderByRaw('CASE WHEN interview_date >= ? THEN interview_date END ASC', [$now])
                                   ->orderBy('interview_date', 'desc')
                                   ->paginate($this->perPage);
            } else {
                $schedules = $query->orderBy('interview_date', 'desc')->paginate($this->perPage);
            }
        } else {
            $schedules = $query->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);
        }

        // Data for Modal Form Selection (Hanya status Interview & Shortlisted)
        $activeApplications = JobApplication::with(['applicantProfile.user', 'job.company'])
            ->whereIn(DB::raw('LOWER(status)'), ['interview', 'shortlisted'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($app) {
                $photo = $app->applicantProfile?->photo ?? $app->applicantProfile?->user?->avatar ?? null;
                $photoUrl = null;
                if ($photo) {
                    $photoUrl = str_starts_with($photo, 'http') ? $photo : asset('storage/' . $photo);
                }
                return [
                    'id'        => $app->id,
                    'name'      => $app->applicantProfile->full_name ?? ('Pelamar #' . $app->id),
                    'email'     => $app->applicantProfile->user->email ?? '',
                    'job_title' => $app->job->title ?? 'Posisi',
                    'company'   => $app->job->company->name ?? '',
                    'status'    => $app->status,
                    'photo'     => $photoUrl,
                ];
            });

        $interviewers = User::whereHas('role', function ($rq) {
            $rq->whereIn(DB::raw('LOWER(name)'), ['admin', 'recruiter', 'superadmin']);
        })->orWhereIn('role_id', [1, 2])
        ->orderBy('name', 'asc')
        ->get();

        $companies = Company::orderBy('name', 'asc')->get();

        $jobsQuery = Job::with('company')->orderBy('title', 'asc');
        if (!empty($this->companyFilter)) {
            $jobsQuery->where('company_id', $this->companyFilter);
        }
        $jobs = $jobsQuery->get();

        return view('livewire.admin.interview-schedule.table', [
            'schedules'          => $schedules,
            'stats'              => $stats,
            'activeApplications' => $activeApplications,
            'interviewers'       => $interviewers,
            'companies'          => $companies,
            'jobs'               => $jobs,
        ]);
    }
}

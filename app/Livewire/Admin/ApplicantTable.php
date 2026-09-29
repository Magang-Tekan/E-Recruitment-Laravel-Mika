<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\JobApplication;
use App\Models\Company;
use App\Models\Job;
use Illuminate\Support\Facades\DB;
use App\Livewire\Traits\WithTableSkeleton;

class ApplicantTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';

    #[Url(as: 'status', except: '')]
    public $statusFilter = '';

    public $jobFilter = '';
    public $companyFilter = '';
    public array $selectedStatuses = [];
    public array $selectedColumns = ['applicant', 'job', 'applied_at', 'status', 'actions'];
    public $perPage = 10;

    public $sortField = 'id';
    public $sortDirection = 'desc';

    public function mount()
    {
        $status = request()->query('status') ?? request()->query('statusFilter');
        if (!empty($status)) {
            $this->statusFilter = $status;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingJobFilter()
    {
        $this->resetPage();
    }

    public function updatingCompanyFilter()
    {
        $this->jobFilter = '';
        $this->resetPage();
    }

    public function updatingSelectedStatuses()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function setStatusFilter($status = '')
    {
        $this->statusFilter = $status;
        $this->selectedStatuses = [];
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

    public function resetColumnSelection()
    {
        $this->selectedColumns = ['applicant', 'job', 'applied_at', 'status', 'actions'];
    }

    public function selectAllColumns()
    {
        $this->selectedColumns = ['applicant', 'contact', 'job', 'applied_at', 'status', 'actions'];
    }

    public function resetAllFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->jobFilter = '';
        $this->companyFilter = '';
        $this->selectedStatuses = [];
        $this->sortField = 'id';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->role_id == 1 || strtolower($user->role?->name ?? '') === 'admin');
        $isRecruiterOnly = $user && !$isAdmin && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter);
        $isDesignatedRecruiter = $user && ((bool) $user->is_recruiter && !$isAdmin && $user->role_id != 2);

        $applications = JobApplication::with([
            'job.company',
            'job.department',
            'job.reviewer',
            'recruiter',
            'admin',
            'applicantProfile.user',
            'applicantProfile.educations',
            'applicantProfile.workExperiences',
            'applicantProfile.organizations',
            'applicantProfile.achievements',
            'applicantProfile.certifications',
            'applicantProfile.trainings',
            'applicantProfile.skills',
            'applicantProfile.socialMedias',
            'applicantProfile.languages',
            'statusHistories.changedBy',
            'interviewSchedules.user'
        ])
        ->when($isDesignatedRecruiter, function ($query) use ($user) {
            $query->whereHas('job', function ($jq) use ($user) {
                $jq->where('reviewer_id', $user->id);
            });
        })
        ->when($isRecruiterOnly && !$isDesignatedRecruiter, function ($query) {
            $query->whereHas('job', function ($jq) {
                $jq->whereIn(DB::raw('LOWER(status)'), ['open', 'published', 'active', 'draft'])
                  ->where(function($q) {
                      $q->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
                  });
            });
        })
        ->when($this->search, function ($query) {
            $search = strtolower(trim($this->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(status) LIKE ?', ['%' . $search . '%'])
                  ->orWhereHas('applicantProfile', function ($apq) use ($search) {
                      $apq->whereRaw('LOWER(full_name) LIKE ?', ['%' . $search . '%'])
                          ->orWhereRaw('LOWER(city) LIKE ?', ['%' . $search . '%'])
                          ->orWhereRaw('LOWER(nik) LIKE ?', ['%' . $search . '%'])
                          ->orWhereHas('user', function ($uq) use ($search) {
                              $uq->whereRaw('LOWER(email) LIKE ?', ['%' . $search . '%']);
                          });
                  })
                  ->orWhereHas('job', function ($jq) use ($search) {
                      $jq->whereRaw('LOWER(title) LIKE ?', ['%' . $search . '%'])
                        ->orWhereHas('company', function ($cq) use ($search) {
                            $cq->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                        });
                  });
            });
        })
        ->when($this->companyFilter, function ($query) {
            $query->whereHas('job', function ($jq) {
                $jq->where('company_id', $this->companyFilter);
            });
        })
        ->when($this->jobFilter, function ($query) {
            $query->where('job_id', $this->jobFilter);
        })
        ->when(!empty($this->selectedStatuses), function ($query) {
            $query->whereIn(DB::raw('LOWER(status)'), array_map('strtolower', $this->selectedStatuses));
        })
        ->when($this->statusFilter, function ($query) {
            $query->whereRaw('LOWER(status) = ?', [strtolower($this->statusFilter)]);
        })
        ->when($this->sortField === 'position', function ($query) {
            $query->join('jobs', 'job_applications.job_id', '=', 'jobs.id')
                  ->orderBy('jobs.title', $this->sortDirection)
                  ->select('job_applications.*');
        })
        ->when($this->sortField === 'company', function ($query) {
            $query->join('jobs', 'job_applications.job_id', '=', 'jobs.id')
                  ->join('companies', 'jobs.company_id', '=', 'companies.id')
                  ->orderBy('companies.name', $this->sortDirection)
                  ->select('job_applications.*');
        })
        ->when($this->sortField === 'applicant', function ($query) {
            $query->join('applicant_profile', 'job_applications.profile_id', '=', 'applicant_profile.id')
                  ->orderBy('applicant_profile.full_name', $this->sortDirection)
                  ->select('job_applications.*');
        })
        ->when($this->sortField === 'applied_at', function ($query) {
            $query->orderBy('job_applications.applied_at', $this->sortDirection);
        })
        ->when($this->sortField === 'status', function ($query) {
            $query->orderBy('job_applications.status', $this->sortDirection);
        })
        ->when($this->sortField === 'id' || !in_array($this->sortField, ['position', 'company', 'applicant', 'applied_at', 'status']), function ($query) {
            $query->orderBy('job_applications.id', $this->sortDirection);
        })
        ->paginate($this->perPage);

        // Calculate quick stats counters
        $baseStatsQuery = JobApplication::query()
            ->when($isDesignatedRecruiter, function ($query) use ($user) {
                $query->whereHas('job', function ($jq) use ($user) {
                    $jq->where('reviewer_id', $user->id);
                });
            })
            ->when($isRecruiterOnly && !$isDesignatedRecruiter, function ($query) {
                $query->whereHas('job', function ($jq) {
                    $jq->whereIn(DB::raw('LOWER(status)'), ['open', 'published', 'active', 'draft'])
                      ->where(function($q) {
                          $q->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
                      });
                });
            })
            ->when($this->companyFilter, function ($query) {
                $query->whereHas('job', fn($q) => $q->where('company_id', $this->companyFilter));
            })
            ->when($this->jobFilter, function ($query) {
                $query->where('job_id', $this->jobFilter);
            });

        $rawStatusCounts = (clone $baseStatsQuery)
            ->select(DB::raw('LOWER(status) as lower_status'), DB::raw('count(*) as count_val'))
            ->groupBy(DB::raw('LOWER(status)'))
            ->pluck('count_val', 'lower_status')
            ->toArray();

        $stats = [
            'total'            => (clone $baseStatsQuery)->count(),
            'submitted'        => $rawStatusCounts['submitted'] ?? 0,
            'partial_approved' => $rawStatusCounts['partial approved'] ?? 0,
            'reviewed'         => $rawStatusCounts['reviewed'] ?? 0,
            'shortlisted'      => $rawStatusCounts['shortlisted'] ?? 0,
            'interview'        => $rawStatusCounts['interview'] ?? 0,
            'accepted'         => $rawStatusCounts['accepted'] ?? 0,
            'rejected'         => $rawStatusCounts['rejected'] ?? 0,
        ];

        $companies = Company::select('id', 'name')->orderBy('name')->get();

        $jobsQuery = Job::select('id', 'title', 'company_id');
        if ($this->companyFilter) {
            $jobsQuery->where('company_id', $this->companyFilter);
        }
        $jobs = $jobsQuery->orderBy('title')->get();

        $assignedJobsCount = ($isDesignatedRecruiter && $user) ? Job::where('reviewer_id', $user->id)->count() : 0;

        return view('livewire.admin.applicants.table', [
            'applications'          => $applications,
            'isRecruiter'           => $isRecruiterOnly,
            'isAdmin'               => $isAdmin,
            'isDesignatedRecruiter' => $isDesignatedRecruiter,
            'assignedJobsCount'     => $assignedJobsCount,
            'search'                => $this->search,
            'statusFilter'          => $this->statusFilter,
            'companyFilter'         => $this->companyFilter,
            'jobFilter'        => $this->jobFilter,
            'companies'        => $companies,
            'jobs'             => $jobs,
            'stats'            => $stats,
            'selectedStatuses' => $this->selectedStatuses,
            'selectedColumns'  => $this->selectedColumns,
            'sortField'        => $this->sortField,
            'sortDirection'    => $this->sortDirection,
        ]);
    }
}

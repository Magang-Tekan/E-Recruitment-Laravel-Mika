<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\JobApplication;
use App\Models\Company;
use App\Models\Job;
use App\Models\Degree;
use App\Models\ApplicantProfile;
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
    public $cityFilter = '';
    public $degreeFilter = '';
    public $universityFilter = '';
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

    public function updatingCityFilter()
    {
        $this->resetPage();
    }

    public function updatingDegreeFilter()
    {
        $this->resetPage();
    }

    public function updatingUniversityFilter()
    {
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
        $this->cityFilter = '';
        $this->degreeFilter = '';
        $this->universityFilter = '';
        $this->selectedStatuses = [];
        $this->sortField = 'id';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function resetProfileFilter()
    {
        $this->cityFilter = '';
        $this->degreeFilter = '';
        $this->universityFilter = '';
        $this->resetPage();
    }

    public function clearDegreeFilter()
    {
        $this->degreeFilter = '';
        $this->resetPage();
    }

    public function clearCityFilter()
    {
        $this->cityFilter = '';
        $this->resetPage();
    }

    public function clearUniversityFilter()
    {
        $this->universityFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
        $isRecruiterOnly = $user && !$isAdmin && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter);
        $userDeptId = $user?->employeeProfile?->department_id;
        $userPosId  = $user?->employeeProfile?->position_id;
        $isDesignatedRecruiter = $user && ((bool) $user->is_recruiter && !$isAdmin && $user->role_id != 2);

        // Filter lowongan:
        // - Karyawan ditunjuk recruiter: HANYA lowongan sesuai posisi atau yang ditugaskan sebagai reviewer
        // - HR recruiter dengan departemen: lowongan departemennya
        // - HR recruiter umum: lowongan aktif
        $jobScopeCallback = function ($query) use ($user, $userPosId, $userDeptId, $isDesignatedRecruiter, $isRecruiterOnly) {
            if ($isDesignatedRecruiter && $user) {
                $query->where(function ($sq) use ($user, $userPosId) {
                    $sq->where('reviewer_id', $user->id);
                    if ($userPosId) {
                        $sq->orWhere('position_id', $userPosId);
                    }
                });
            } elseif ($userDeptId) {
                $query->where('department_id', $userDeptId);
            } elseif ($isRecruiterOnly) {
                $query->whereIn(DB::raw('LOWER(status)'), ['open', 'published', 'active', 'draft'])
                      ->where(function ($q) {
                          $q->whereNull('deadline')->orWhere('deadline', '>=', now()->toDateString());
                      });
            }
        };

        $applications = JobApplication::with([
            'job.company',
            'job.department',
            'job.reviewer',
            'recruiter',
            'admin',
            'testRecruiter',
            'testAdmin',
            'interviewRecruiter',
            'interviewAdmin',
            'testAttempts.test.category',
            'testAttempts.discTestResult',
            'testAttempts.papiTestResult',
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
            'applicantProfile.family',
            'applicantProfile.jobPreference',
            'statusHistories.changedBy',
            'interviewSchedules.user'
        ])
        ->when($isRecruiterOnly, function ($query) use ($jobScopeCallback) {
            $query->whereHas('job', $jobScopeCallback);
        })
        ->when($this->search, function ($query) {
            $search = strtolower(trim($this->search));
            $indoStatusMap = [
                'diajukan'     => 'submitted',
                'lolos berkas' => 'reviewed',
                'lolos ujian'  => 'shortlisted',
                'wawancara'    => 'interview',
                'diterima'     => 'accepted',
                'ditolak'      => 'rejected',
            ];
            $matchedStatuses = [];
            foreach ($indoStatusMap as $indo => $eng) {
                if (str_contains($indo, $search) || str_contains($search, $indo)) {
                    $matchedStatuses[] = $eng;
                }
            }

            $query->where(function ($q) use ($search, $matchedStatuses) {
                $q->whereRaw('LOWER(status) LIKE ?', ['%' . $search . '%']);
                if (!empty($matchedStatuses)) {
                    $q->orWhereIn(DB::raw('LOWER(status)'), $matchedStatuses);
                }
                $q->orWhereHas('applicantProfile', function ($apq) use ($search) {
                      $apq->whereRaw('LOWER(full_name) LIKE ?', ['%' . $search . '%'])
                          ->orWhereRaw('LOWER(city) LIKE ?', ['%' . $search . '%'])
                          ->orWhereRaw('LOWER(nik) LIKE ?', ['%' . $search . '%'])
                          ->orWhereHas('user', function ($uq) use ($search) {
                              $uq->whereRaw('LOWER(email) LIKE ?', ['%' . $search . '%']);
                          })
                          ->orWhereHas('educations', function ($eq) use ($search) {
                              $eq->whereRaw('LOWER(school_name) LIKE ?', ['%' . $search . '%'])
                                 ->orWhereRaw('LOWER(degree) LIKE ?', ['%' . $search . '%'])
                                 ->orWhereRaw('LOWER(major) LIKE ?', ['%' . $search . '%']);
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
        ->when($this->cityFilter, function ($query) {
            $query->whereHas('applicantProfile', function ($apq) {
                $apq->whereRaw('LOWER(city) LIKE ?', ['%' . strtolower(trim($this->cityFilter)) . '%']);
            });
        })
        ->when($this->degreeFilter, function ($query) {
            $val = trim($this->degreeFilter);
            $query->whereHas('applicantProfile.educations', function ($eq) use ($val) {
                $eq->where(function ($sub) use ($val) {
                    if (is_numeric($val)) {
                        $sub->where('degree_id', (int) $val);
                    } else {
                        $matchingDegreeIds = Degree::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($val) . '%'])
                            ->pluck('id')
                            ->toArray();

                        if (!empty($matchingDegreeIds)) {
                            $sub->whereIn('degree_id', $matchingDegreeIds)
                                ->orWhereRaw('LOWER(degree) LIKE ?', ['%' . strtolower($val) . '%']);
                        } else {
                            $sub->whereRaw('LOWER(degree) LIKE ?', ['%' . strtolower($val) . '%']);
                        }
                    }
                });
            });
        })
        ->when($this->universityFilter, function ($query) {
            $query->whereHas('applicantProfile.educations', function ($eq) {
                $eq->whereRaw('LOWER(school_name) LIKE ?', ['%' . strtolower(trim($this->universityFilter)) . '%']);
            });
        })
        ->when(!empty($this->selectedStatuses), function ($query) {
            $query->whereIn(DB::raw('LOWER(status)'), array_map('strtolower', $this->selectedStatuses));
        })
        ->when($this->statusFilter, function ($query) {
            $val = strtolower($this->statusFilter);
            $indoParamMap = [
                'diajukan'     => 'submitted',
                'lolos_berkas' => 'reviewed',
                'lolos berkas' => 'reviewed',
                'lolos_ujian'  => 'shortlisted',
                'lolos ujian'  => 'shortlisted',
                'wawancara'    => 'interview',
                'diterima'     => 'accepted',
                'ditolak'      => 'rejected',
            ];
            if (isset($indoParamMap[$val])) {
                $val = $indoParamMap[$val];
            }
            if (in_array($val, ['need_review', 'pending_review', 'perlu_review'])) {
                $query->whereIn(DB::raw('LOWER(status)'), ['applied', 'pending', 'screening', 'submitted', 'partial approved']);
            } else {
                $query->whereRaw('LOWER(status) = ?', [$val]);
            }
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
            ->when($isRecruiterOnly, function ($query) use ($jobScopeCallback) {
                $query->whereHas('job', $jobScopeCallback);
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

        $companiesQuery = Company::select('id', 'name')->orderBy('name');
        if ($isRecruiterOnly) {
            $companiesQuery->whereHas('jobs', $jobScopeCallback);
        }
        $companies = $companiesQuery->get();

        $jobsQuery = Job::select('id', 'title', 'company_id');
        if ($isRecruiterOnly) {
            $jobsQuery->where($jobScopeCallback);
        }
        if ($this->companyFilter) {
            $jobsQuery->where('company_id', $this->companyFilter);
        }
        $jobs = $jobsQuery->orderBy('title')->get();

        $assignedJobsCount = ($isDesignatedRecruiter && $user)
            ? Job::where($jobScopeCallback)->count()
            : 0;

        $degrees = Degree::orderBy('rank')->get();
        $availableCities = ApplicantProfile::whereNotNull('city')
            ->where('city', '!=', '')
            ->pluck('city')
            ->map(fn($c) => trim(ucwords(strtolower($c))))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('livewire.admin.applicants.table', [
            'applications'          => $applications,
            'isRecruiter'           => $isRecruiterOnly,
            'isAdmin'               => $isAdmin,
            'isDesignatedRecruiter' => $isDesignatedRecruiter,
            'assignedJobsCount'     => $assignedJobsCount,
            'search'                => $this->search,
            'statusFilter'          => $this->statusFilter,
            'companyFilter'         => $this->companyFilter,
            'jobFilter'             => $this->jobFilter,
            'cityFilter'            => $this->cityFilter,
            'degreeFilter'          => $this->degreeFilter,
            'universityFilter'      => $this->universityFilter,
            'degrees'               => $degrees,
            'availableCities'       => $availableCities,
            'companies'             => $companies,
            'jobs'                  => $jobs,
            'stats'                 => $stats,
            'selectedStatuses'      => $this->selectedStatuses,
            'selectedColumns'       => $this->selectedColumns,
            'sortField'             => $this->sortField,
            'sortDirection'         => $this->sortDirection,
        ]);
    }
}

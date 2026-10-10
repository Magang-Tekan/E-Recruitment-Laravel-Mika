<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TestAttempt;
use App\Models\Company;
use App\Models\Job;
use App\Models\Test;
use Illuminate\Support\Facades\DB;
use App\Livewire\Traits\WithTableSkeleton;

class TestEvaluationTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $companyId = '';
    public $jobId = '';
    public $testId = '';
    public $status = '';
    public $perPage = 10;

    public $sortField = 'id';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCompanyId()
    {
        $this->jobId = '';
        $this->resetPage();
    }

    public function updatingJobId()
    {
        $this->resetPage();
    }

    public function updatingTestId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
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

    public function resetFilters()
    {
        $this->search = '';
        $this->companyId = '';
        $this->jobId = '';
        $this->testId = '';
        $this->status = '';
        $this->sortField = 'id';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->role_id == 1 || in_array(strtolower($user->role?->name ?? ''), ['admin', 'superadmin']));
        $isRecruiter = !$isAdmin && ($user->role_id == 2 || strtolower($user->role?->name ?? '') === 'recruiter' || (bool) $user->is_recruiter);
        $userDeptId = $user?->employeeProfile?->department_id;
        $userPosId  = $user?->employeeProfile?->position_id;
        $isDesignatedRecruiter = $user && ((bool) $user->is_recruiter && !$isAdmin && $user->role_id != 2);

        $companiesQuery = Company::orderBy('name', 'asc');
        if ($isRecruiter && $user) {
            $companiesQuery->whereHas('jobs', function ($jq) use ($user, $userPosId, $userDeptId, $isDesignatedRecruiter) {
                if ($isDesignatedRecruiter) {
                    $jq->where(function ($sq) use ($user, $userPosId) {
                        $sq->where('reviewer_id', $user->id);
                        if ($userPosId) {
                            $sq->orWhere('position_id', $userPosId);
                        }
                    });
                } elseif ($userDeptId) {
                    $jq->where('department_id', $userDeptId);
                }
            });
        }
        $companies = $companiesQuery->get();

        $jobsQuery = Job::with('company')->orderBy('title', 'asc');
        if ($isRecruiter && $user) {
            $jobsQuery->where(function ($sq) use ($user, $userPosId, $userDeptId, $isDesignatedRecruiter) {
                if ($isDesignatedRecruiter) {
                    $sq->where('reviewer_id', $user->id);
                    if ($userPosId) {
                        $sq->orWhere('position_id', $userPosId);
                    }
                } elseif ($userDeptId) {
                    $sq->where('department_id', $userDeptId);
                }
            });
        }
        if (!empty($this->companyId)) {
            $jobsQuery->where('company_id', $this->companyId);
        }
        $jobs = $jobsQuery->get();

        $tests = Test::where(function ($q) {
            $q->where('test_type', 'recruitment')
              ->orWhereNull('test_type');
        })->orderBy('title', 'asc')->get();

        $query = TestAttempt::with([
            'jobApplication.applicantProfile.user',
            'jobApplication.job.company',
            'jobApplication.testRecruiter',
            'jobApplication.testAdmin',
            'test.category',
            'answers.question.options',
            'answers.option',
            'answers.reviewer',
            'discTestResult.discProfile',
            'papiTestResult',
        ])
        ->where('attempt_type', 'applicant')
        ->when($isRecruiter && $user, function ($q) use ($user, $userPosId, $userDeptId, $isDesignatedRecruiter) {
            $q->where(function ($sq) use ($user, $userPosId, $userDeptId, $isDesignatedRecruiter) {
                $sq->whereHas('jobApplication.job', function ($jq) use ($user, $userPosId, $userDeptId, $isDesignatedRecruiter) {
                    if ($isDesignatedRecruiter) {
                        $jq->where(function ($ssq) use ($user, $userPosId) {
                            $ssq->where('reviewer_id', $user->id);
                            if ($userPosId) {
                                $ssq->orWhere('position_id', $userPosId);
                            }
                        });
                    } elseif ($userDeptId) {
                        $jq->where('department_id', $userDeptId);
                    }
                });
                if (!$isDesignatedRecruiter && $userDeptId) {
                    $sq->orWhereHas('test', function ($tq) use ($userDeptId) {
                        $tq->where('department_id', $userDeptId);
                    });
                }
            });
        });

        // Search Filter
        if (!empty($this->search)) {
            $search = strtolower(trim($this->search));
            $query->where(function ($q) use ($search) {
                $q->whereHas('jobApplication.applicantProfile', function ($p) use ($search) {
                    $p->whereRaw('LOWER(full_name) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(nik) LIKE ?', ['%' . $search . '%'])
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->whereRaw('LOWER(email) LIKE ?', ['%' . $search . '%']);
                      });
                })
                ->orWhereHas('test', function ($t) use ($search) {
                    $t->whereRaw('LOWER(title) LIKE ?', ['%' . $search . '%']);
                })
                ->orWhereHas('jobApplication.job', function ($j) use ($search) {
                    $j->whereRaw('LOWER(title) LIKE ?', ['%' . $search . '%']);
                });
            });
        }

        // Filter Perusahaan
        if (!empty($this->companyId)) {
            $query->whereHas('jobApplication.job', function ($j) {
                $j->where('company_id', $this->companyId);
            });
        }

        // Filter Lowongan
        if (!empty($this->jobId)) {
            $query->whereHas('jobApplication', function ($j) {
                $j->where('job_id', $this->jobId);
            });
        }

        // Filter Paket Ujian
        if (!empty($this->testId)) {
            $query->where('test_id', $this->testId);
        }

        // Filter Status Ujian / Hasil Evaluasi
        if (!empty($this->status)) {
            if ($this->status === 'needs_grading') {
                $query->whereHas('answers.question', function ($q) {
                    $q->where('question_type', 'essay');
                })->whereHas('answers', function ($a) {
                    $a->whereNull('reviewed_by');
                });
            } elseif ($this->status === 'waiting_hr') {
                // Menunggu persetujuan HR (Teknis sudah disetujui / esai >= KKM)
                $query->whereHas('jobApplication', function ($jq) {
                    $jq->where('test_recruiter_approval', 'approved')
                       ->where('test_admin_approval', '!=', 'approved');
                });
            } elseif ($this->status === 'double_approved') {
                // Double Approved (Kedua pihak setuju atau status Shortlisted / Interview / Accepted)
                $query->whereHas('jobApplication', function ($jq) {
                    $jq->where(function ($sub) {
                        $sub->where(function ($s) {
                            $s->where('test_recruiter_approval', 'approved')
                              ->where('test_admin_approval', 'approved');
                        })->orWhereIn(DB::raw('LOWER(status)'), ['shortlisted', 'interview', 'accepted']);
                    });
                });
            } elseif ($this->status === 'passed') {
                $query->where('status', 'passed');
            } elseif ($this->status === 'failed') {
                // Gagal Tes / Ditolak (tanpa mencampurkan yang Shortlisted/Interview/Accepted)
                $query->where(function ($q) {
                    $q->where('status', 'failed')
                      ->orWhereHas('jobApplication', function ($jq) {
                          $jq->where('status', 'Rejected');
                      });
                })
                ->whereDoesntHave('discTestResult')
                ->where(function ($q) {
                    $q->whereDoesntHave('jobApplication')
                      ->orWhereHas('jobApplication', function ($jq) {
                          $jq->whereNotIn(DB::raw('LOWER(status)'), ['shortlisted', 'interview', 'accepted']);
                      });
                });
            } elseif ($this->status === 'disc') {
                // Khusus Tes Kepribadian DISC
                $query->where(function ($q) {
                    $q->whereHas('discTestResult')
                      ->orWhereHas('test', function ($t) {
                          $t->whereRaw('LOWER(title) LIKE ?', ['%disc%'])
                            ->orWhereRaw('LOWER(title) LIKE ?', ['%personality%']);
                      });
                });
            } elseif ($this->status === 'papi') {
                // Khusus Tes Kepribadian PAPI Kostick
                $query->where(function ($q) {
                    $q->whereHas('papiTestResult')
                      ->orWhereHas('test', function ($t) {
                          $t->whereRaw('LOWER(title) LIKE ?', ['%papi%'])
                            ->orWhereRaw('LOWER(title) LIKE ?', ['%kostick%']);
                      });
                });
            } elseif ($this->status === 'psychometric') {
                // Semua Psikotes (DISC & PAPI)
                $query->where(function ($q) {
                    $q->whereHas('discTestResult')
                      ->orWhereHas('papiTestResult')
                      ->orWhereHas('test', function ($t) {
                          $t->whereRaw('LOWER(title) LIKE ?', ['%disc%'])
                            ->orWhereRaw('LOWER(title) LIKE ?', ['%papi%'])
                            ->orWhereRaw('LOWER(title) LIKE ?', ['%kostick%'])
                            ->orWhereRaw('LOWER(title) LIKE ?', ['%personality%'])
                            ->orWhereRaw('LOWER(title) LIKE ?', ['%kepribadian%']);
                      });
                });
            } elseif ($this->status === 'in_progress') {
                $query->where('status', 'in_progress');
            } else {
                $query->where('status', $this->status);
            }
        }

        // Filter out orphaned in-progress attempts with no answers when another attempt exists for the same application and test
        $query->where(function ($q) {
            $q->where('test_attempts.status', '!=', 'in_progress')
              ->orWhereHas('answers')
              ->orWhereNotExists(function ($sub) {
                  $sub->select(DB::raw(1))
                      ->from('test_attempts as ta2')
                      ->whereColumn('ta2.job_application_id', 'test_attempts.job_application_id')
                      ->whereColumn('ta2.test_id', 'test_attempts.test_id')
                      ->where('ta2.id', '!=', DB::raw('test_attempts.id'));
              });
        });

        // Sorting
        if ($this->sortField === 'applicant') {
            $query->join('job_applications', 'test_attempts.job_application_id', '=', 'job_applications.id')
                  ->join('applicant_profile', 'job_applications.profile_id', '=', 'applicant_profile.id')
                  ->orderBy('applicant_profile.full_name', $this->sortDirection)
                  ->select('test_attempts.*');
        } elseif ($this->sortField === 'score') {
            $query->orderBy('total_score', $this->sortDirection);
        } elseif ($this->sortField === 'started_at') {
            $query->orderBy('started_at', $this->sortDirection);
        } elseif ($this->sortField === 'status') {
            $query->orderBy('status', $this->sortDirection);
        } else {
            $query->orderBy('id', $this->sortDirection);
        }

        $attempts = $query->paginate($this->perPage);

        return view('livewire.admin.test-evaluation.table', [
            'attempts'              => $attempts,
            'companies'             => $companies,
            'jobs'                  => $jobs,
            'tests'                 => $tests,
            'search'                => $this->search,
            'companyId'             => $this->companyId,
            'jobId'                 => $this->jobId,
            'testId'                => $this->testId,
            'status'                => $this->status,
            'sortField'             => $this->sortField,
            'sortDirection'         => $this->sortDirection,
            'isDesignatedRecruiter' => $isDesignatedRecruiter,
            'assignedJobsCount'     => ($isDesignatedRecruiter && $user) ? Job::where(function ($sq) use ($user, $userPosId) {
                $sq->where('reviewer_id', $user->id);
                if ($userPosId) {
                    $sq->orWhere('position_id', $userPosId);
                }
            })->count() : 0,
        ]);
    }
}

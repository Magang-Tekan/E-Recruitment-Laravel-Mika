<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TestAttempt;
use App\Models\Company;
use App\Models\Department;
use App\Models\Test;
use Illuminate\Support\Facades\DB;
use App\Livewire\Traits\WithTableSkeleton;

class EmployeeTestEvaluationTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $companyId = '';
    public $departmentId = '';
    public $employeeType = '';
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
        $this->departmentId = '';
        $this->testId = '';
        $this->resetPage();
    }

    public function updatingDepartmentId()
    {
        $this->resetPage();
    }

    public function updatingEmployeeType()
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
        $this->departmentId = '';
        $this->employeeType = '';
        $this->testId = '';
        $this->status = '';
        $this->sortField = 'id';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function render()
    {
        $companies = Company::orderBy('name', 'asc')->get();

        $departments = Department::with('company')
            ->when($this->companyId, function ($q) {
                $q->where('company_id', $this->companyId);
            })
            ->orderBy('name', 'asc')
            ->get();

        $tests = Test::where('test_type', 'employee')
            ->when($this->companyId, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('departments', function ($d) {
                        $d->where('company_id', $this->companyId);
                    })->orWhereHas('department', function ($d) {
                        $d->where('company_id', $this->companyId);
                    });
                });
            })
            ->orderBy('title', 'asc')
            ->get();

        $query = TestAttempt::with([
            'user.employeeProfile.company',
            'user.employeeProfile.department.company',
            'user.employeeProfile.position',
            'test.category',
            'test.department',
            'answers.question.options',
            'answers.option',
            'answers.reviewer',
            'discTestResult.discProfile',
        ])
        ->where('attempt_type', 'employee');

        // Search Filter
        if (!empty($this->search)) {
            $search = strtolower(trim($this->search));
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(nik) LIKE ?', ['%' . $search . '%']);
                })
                ->orWhereHas('user.employeeProfile', function ($ep) use ($search) {
                    $ep->whereRaw('LOWER(full_name) LIKE ?', ['%' . $search . '%'])
                       ->orWhereRaw('LOWER(position_title) LIKE ?', ['%' . $search . '%'])
                       ->orWhereHas('position', function ($pq) use ($search) {
                           $pq->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                       });
                })
                ->orWhereHas('test', function ($t) use ($search) {
                    $t->whereRaw('LOWER(title) LIKE ?', ['%' . $search . '%']);
                });
            });
        }

        // Filter Perusahaan
        if (!empty($this->companyId)) {
            $query->where(function ($q) {
                $q->whereHas('user.employeeProfile', function ($ep) {
                    $ep->where('company_id', $this->companyId)
                       ->orWhereHas('department', function ($d) {
                           $d->where('company_id', $this->companyId);
                       });
                })->orWhereHas('test', function ($t) {
                    $t->whereHas('departments', function ($d) {
                        $d->where('company_id', $this->companyId);
                    })->orWhereHas('department', function ($d) {
                        $d->where('company_id', $this->companyId);
                    });
                });
            });
        }

        // Filter Departemen
        if (!empty($this->departmentId)) {
            $query->where(function ($q) {
                $q->whereHas('user.employeeProfile', function ($ep) {
                    $ep->where('department_id', $this->departmentId);
                })->orWhereHas('test', function ($t) {
                    $t->whereHas('departments', function ($d) {
                        $d->where('departments.id', $this->departmentId);
                    })->orWhere('department_id', $this->departmentId);
                });
            });
        }

        // Filter Tipe Pegawai (Karyawan vs Magang)
        if (!empty($this->employeeType)) {
            $query->whereHas('user.employeeProfile', function ($ep) {
                $ep->where('employee_type', $this->employeeType);
            });
        }

        // Filter Paket Asesmen
        if (!empty($this->testId)) {
            $query->where('test_id', $this->testId);
        }

        // Filter Status
        if (!empty($this->status)) {
            if ($this->status === 'needs_grading') {
                $query->whereHas('answers.question', function ($q) {
                    $q->where('question_type', 'essay');
                })->whereHas('answers', function ($a) {
                    $a->whereNull('reviewed_by');
                });
            } elseif ($this->status === 'disc') {
                $query->where(function ($q) {
                    $q->whereHas('discTestResult')
                      ->orWhereHas('test', function ($t) {
                          $t->whereRaw('LOWER(title) LIKE ?', ['%disc%']);
                      });
                });
            } else {
                $query->where('status', $this->status);
            }
        }

        // Sorting
        if ($this->sortField === 'employee') {
            $query->join('users', 'test_attempts.user_id', '=', 'users.id')
                  ->orderBy('users.name', $this->sortDirection)
                  ->select('test_attempts.*');
        } elseif ($this->sortField === 'score') {
            $query->orderBy('total_score', $this->sortDirection);
        } elseif ($this->sortField === 'started_at') {
            $query->orderBy('started_at', $this->sortDirection);
        } else {
            $query->orderBy('id', $this->sortDirection);
        }

        $attempts = $query->paginate($this->perPage);

        return view('livewire.admin.employee-test-evaluation.table', [
            'attempts' => $attempts,
            'companies' => $companies,
            'departments' => $departments,
            'tests' => $tests,
        ]);
    }
}


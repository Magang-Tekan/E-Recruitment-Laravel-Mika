<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Test;
use App\Models\Company;
use App\Models\Department;
use App\Models\TestCategory;
use App\Models\QuestionBank;
use App\Livewire\Traits\WithTableSkeleton;

class EmployeeTestTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $companyId = '';
    public $departmentId = '';
    public $categoryId = '';
    public $targetEmployeeType = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCompanyId()
    {
        $this->departmentId = '';
        $this->resetPage();
    }

    public function updatingDepartmentId()
    {
        $this->resetPage();
    }

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function updatingTargetEmployeeType()
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
        $this->companyId = '';
        $this->departmentId = '';
        $this->categoryId = '';
        $this->targetEmployeeType = '';
        $this->resetPage();
    }

    public function render()
    {
        $companies = Company::orderBy('name', 'asc')->get();
        $departments = Department::with('company')->orderBy('name', 'asc')->get();
        $filterDepartments = Department::when($this->companyId, function ($q) {
            $q->where('company_id', $this->companyId);
        })->orderBy('name', 'asc')->get();

        $categories = TestCategory::orderBy('name', 'asc')->get();

        $allQuestions = QuestionBank::with(['category', 'options'])
            ->orderBy('category_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $tests = Test::with(['departments.company', 'department.company', 'category', 'questions'])
            ->withCount(['questions', 'attempts', 'departments'])
            ->where('test_type', 'employee')
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->whereRaw('LOWER(title) LIKE ?', ['%' . $search . '%']);
            })
            ->when($this->companyId, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('departments', function ($d) {
                        $d->where('company_id', $this->companyId);
                    })->orWhereHas('department', function ($d) {
                        $d->where('company_id', $this->companyId);
                    });
                });
            })
            ->when($this->departmentId, function ($query) {
                if ($this->departmentId === 'all') {
                    $query->whereDoesntHave('departments')
                          ->whereNull('department_id');
                } else {
                    $query->where(function ($q) {
                        $q->whereHas('departments', function ($d) {
                            $d->where('departments.id', $this->departmentId);
                        })->orWhere('department_id', $this->departmentId);
                    });
                }
            })
            ->when($this->categoryId, function ($query) {
                $query->where('category_id', $this->categoryId);
            })
            ->when($this->targetEmployeeType, function ($query) {
                $query->where('target_employee_type', $this->targetEmployeeType);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.employee-test.table', [
            'tests' => $tests,
            'companies' => $companies,
            'departments' => $departments,
            'filterDepartments' => $filterDepartments,
            'categories' => $categories,
            'allQuestions' => $allQuestions,
        ]);
    }
}


<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Job;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Livewire\Traits\WithTableSkeleton;

class JobTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $companyId = '';
    public $departmentId = '';
    public $statusFilter = '';
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

    public function updatingStatusFilter()
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
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $companies = Company::orderBy('name')->get();

        $departments = Department::with('company')->orderBy('name')->get();

        $filterDepartments = Department::when($this->companyId, function ($q) {
            $q->where('company_id', $this->companyId);
        })->orderBy('name')->get();

        $positions = Position::orderBy('name')->get();

        $jobs = Job::with(['company', 'department', 'position'])
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(title) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(location) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(employment_type) LIKE ?', ['%' . $search . '%'])
                      ->orWhereHas('company', function ($cq) use ($search) {
                          $cq->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                      })
                      ->orWhereHas('department', function ($dq) use ($search) {
                          $dq->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                      })
                      ->orWhereHas('position', function ($pq) use ($search) {
                          $pq->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                      });
                });
            })
            ->when($this->companyId, function ($query) {
                $query->where('company_id', $this->companyId);
            })
            ->when($this->departmentId, function ($query) {
                $query->where('department_id', $this->departmentId);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.jobs.table', [
            'jobs' => $jobs,
            'companies' => $companies,
            'departments' => $departments,
            'filterDepartments' => $filterDepartments,
            'positions' => $positions,
        ]);
    }
}

<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Department;
use App\Models\Company;
use App\Livewire\Traits\WithTableSkeleton;

class DepartmentTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $companyFilter = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCompanyFilter()
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
        $this->resetPage();
    }

    public function render()
    {
        $departments = Department::with('company')
            ->when($this->companyFilter, function ($query) {
                $query->where('company_id', $this->companyFilter);
            })
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $search . '%'])
                      ->orWhereHas('company', function ($cq) use ($search) {
                          $cq->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                      });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        $companies = Company::orderBy('name')->get();

        return view('livewire.admin.department.table', [
            'departments' => $departments,
            'companies' => $companies,
        ]);
    }
}

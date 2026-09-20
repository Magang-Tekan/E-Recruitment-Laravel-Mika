<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Company;
use App\Models\Role;
use App\Livewire\Traits\WithTableSkeleton;

class CompanyTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $companies = Company::query()
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(city) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(province) LIKE ?', ['%' . $search . '%']);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        $roles = Role::all();

        return view('livewire.admin.company.table', [
            'companies' => $companies,
            'roles' => $roles,
        ]);
    }
}

<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Role;
use App\Livewire\Traits\WithTableSkeleton;

class RoleTable extends Component
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
        $roles = Role::withCount('users')
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
            })
            ->orderBy('id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.role.table', [
            'roles' => $roles,
        ]);
    }
}

<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use App\Livewire\Traits\WithTableSkeleton;

class UserTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $roleFilter = '';
    public $sortBy = 'latest';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
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
        $this->roleFilter = '';
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function toggleRecruiterStatus($userId)
    {
        $user = User::findOrFail($userId);
        
        // Admin & Recruiter by default already have access
        if ($user->role_id == 1 || $user->role_id == 2) {
            return;
        }

        // Hanya karyawan (employee) yang dapat ditunjuk sebagai recruiter
        $roleName = strtolower($user->role?->name ?? '');
        if ($user->role_id != 4 && $roleName !== 'employee') {
            session()->flash('error', "Hanya akun dengan role Karyawan (Employee) yang dapat ditunjuk sebagai Recruiter.");
            return;
        }

        $user->is_recruiter = !((bool) $user->is_recruiter);
        $user->save();

        $statusText = $user->is_recruiter ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('create', "Hak akses Recruiter untuk karyawan {$user->name} berhasil {$statusText}.");
    }

    public function render()
    {
        $roles = Role::orderBy('id', 'asc')->get();

        $users = User::with(['role', 'applicantProfile', 'employeeProfile'])
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(nik) LIKE ?', ['%' . $search . '%']);
                });
            })
            ->when($this->roleFilter, function ($query) {
                if ($this->roleFilter === 'staff') {
                    $query->whereIn('role_id', [1, 2]);
                } else {
                    $query->where('role_id', $this->roleFilter);
                }
            })
            ->when($this->sortBy === 'oldest', function ($query) {
                $query->orderBy('id', 'asc');
            })
            ->when($this->sortBy === 'name_asc', function ($query) {
                $query->orderBy('name', 'asc');
            })
            ->when($this->sortBy === 'name_desc', function ($query) {
                $query->orderBy('name', 'desc');
            })
            ->when($this->sortBy === 'role_asc', function ($query) {
                $query->orderBy('role_id', 'asc')->orderBy('name', 'asc');
            })
            ->when(!in_array($this->sortBy, ['oldest', 'name_asc', 'name_desc', 'role_asc']), function ($query) {
                $query->orderBy('id', 'desc');
            })
            ->paginate($this->perPage);

        return view('livewire.admin.user.table', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}

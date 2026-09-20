<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmployeeProfile;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Validation\Rule;
use App\Livewire\Traits\WithTableSkeleton;

class EmployeeTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $companyId = '';
    public $departmentId = '';
    public $employeeType = '';
    public $perPage = 10;

    // Modal Edit State
    public $showEditModal = false;
    public $editId = null;
    public $editFullName = '';
    public $editNik = '';
    public $editEmployeeType = 'permanent';
    public $editDepartmentId = '';
    public $editPositionId = '';
    public $editPositionTitle = '';

    // Modal Delete State
    public $showDeleteModal = false;
    public $deleteId = null;
    public $deleteName = '';

    // Modal Double Permission Promosi State
    public $showPromoteModal = false;
    public $promoteId = null;
    public $promoteFullName = '';
    public $promoteCurrentPosition = '';
    public $promotePositionId = '';
    public $promoteNewPosition = '';
    public $promoteTargetType = 'permanent';
    public $promoteDepartmentId = '';
    public $promoteDepartmentName = '';

    public function updatedEditDepartmentId()
    {
        if ($this->editPositionId) {
            $pos = Position::find($this->editPositionId);
            if ($pos && (string) $pos->department_id !== (string) $this->editDepartmentId) {
                $this->editPositionId = '';
            }
        }
    }

    public function updatedEditPositionId()
    {
        if ($this->editPositionId) {
            $pos = Position::find($this->editPositionId);
            if ($pos) {
                $this->editPositionTitle = $pos->name;
            }
        }
    }

    public function updatedPromoteDepartmentId()
    {
        if ($this->promotePositionId) {
            $pos = Position::find($this->promotePositionId);
            if ($pos && (string) $pos->department_id !== (string) $this->promoteDepartmentId) {
                $this->promotePositionId = '';
            }
        }
    }

    public function updatedPromotePositionId()
    {
        if ($this->promotePositionId) {
            $pos = Position::find($this->promotePositionId);
            if ($pos) {
                $this->promoteNewPosition = $pos->name;
            }
        }
    }

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

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingEmployeeType()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->companyId = '';
        $this->departmentId = '';
        $this->employeeType = '';
        $this->resetPage();
    }

    public function openEdit($id)
    {
        $employee = EmployeeProfile::with('user')->findOrFail($id);
        $this->editId = $employee->id;
        $this->editFullName = $employee->full_name ?? ($employee->user?->name ?? '');
        $this->editNik = $employee->nik ?? ($employee->user?->nik ?? '');
        $this->editEmployeeType = $employee->employee_type ?? 'permanent';
        $this->editDepartmentId = $employee->department_id;
        $this->editPositionId = $employee->position_id ? (string) $employee->position_id : '';
        $this->editPositionTitle = $employee->position_title ?? ($employee->position?->name ?? '');
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editId', 'editFullName', 'editNik', 'editEmployeeType', 'editDepartmentId', 'editPositionId', 'editPositionTitle']);
    }

    public function saveEmployee()
    {
        $employee = EmployeeProfile::findOrFail($this->editId);

        $this->validate([
            'editFullName' => 'required|string|max:255',
            'editNik' => [
                'required',
                'digits:16',
                Rule::unique('employee_profiles', 'nik')->ignore($this->editId),
                Rule::unique('users', 'nik')->ignore($employee->user_id),
            ],
            'editEmployeeType' => 'required|in:permanent,contract,internship,probation',
            'editDepartmentId' => 'nullable|exists:departments,id',
            'editPositionId' => 'nullable|exists:positions,id',
            'editPositionTitle' => 'nullable|string|max:255',
        ], [
            'editFullName.required' => 'Nama lengkap wajib diisi.',
            'editNik.required' => 'NIK wajib diisi.',
            'editNik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'editNik.unique' => 'NIK sudah digunakan oleh pegawai/pengguna lain.',
            'editEmployeeType.required' => 'Tipe status pegawai wajib dipilih.',
        ]);

        $dept = $this->editDepartmentId ? Department::find($this->editDepartmentId) : null;
        $pos = $this->editPositionId ? Position::find($this->editPositionId) : null;
        $finalPositionTitle = $this->editPositionTitle ?: ($pos?->name ?? null);

        $employee->update([
            'full_name' => $this->editFullName,
            'nik' => $this->editNik,
            'employee_type' => $this->editEmployeeType,
            'department_id' => $this->editDepartmentId ?: null,
            'company_id' => $dept?->company_id ?? $employee->company_id,
            'position_id' => $this->editPositionId ?: null,
            'position_title' => $finalPositionTitle,
        ]);

        if ($employee->user) {
            $userUpdates = [];
            if ($employee->user->name !== $this->editFullName) {
                $userUpdates['name'] = $this->editFullName;
            }
            if ($employee->user->nik !== $this->editNik) {
                $userUpdates['nik'] = $this->editNik;
            }
            if (!empty($userUpdates)) {
                $employee->user->update($userUpdates);
            }
        }

        $this->closeEditModal();
        session()->flash('message', 'Data dan status pegawai berhasil diperbarui.');
    }

    public function openDeleteModal($id)
    {
        $employee = EmployeeProfile::with('user')->findOrFail($id);
        $this->deleteId = $employee->id;
        $this->deleteName = $employee->full_name ?? ($employee->user?->name ?? 'Karyawan');
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->reset(['deleteId', 'deleteName']);
    }

    public function deleteEmployee()
    {
        if ($this->deleteId) {
            $employee = EmployeeProfile::findOrFail($this->deleteId);
            $name = $employee->full_name;
            $user = $employee->user;

            $employee->delete();

            if ($user && $user->role_id === 4) {
                $user->delete();
            }

            $this->closeDeleteModal();
            session()->flash('message', "Data pegawai '{$name}' berhasil dihapus.");
        }
    }

    public function openPromoteModal($id)
    {
        $employee = EmployeeProfile::with(['user', 'department'])->findOrFail($id);
        $this->promoteId = $employee->id;
        $this->promoteFullName = $employee->full_name ?? ($employee->user?->name ?? 'Peserta Magang');
        $this->promoteCurrentPosition = $employee->position?->name ?? ($employee->position_title ?? 'Intern / Magang');
        $this->promotePositionId = '';
        $this->promoteNewPosition = $employee->position_title ? str_ireplace(['intern', 'magang', 'internship'], 'Staff', $employee->position_title) : 'Staff';
        $this->promoteDepartmentId = $employee->department_id;
        $this->promoteDepartmentName = $employee->department?->name ?? 'Belum Diatur';
        $this->promoteTargetType = 'permanent';
        $this->showPromoteModal = true;
    }

    public function closePromoteModal()
    {
        $this->showPromoteModal = false;
        $this->reset(['promoteId', 'promoteFullName', 'promoteCurrentPosition', 'promotePositionId', 'promoteNewPosition', 'promoteTargetType', 'promoteDepartmentId', 'promoteDepartmentName']);
    }

    public function confirmPromotion()
    {
        $this->validate([
            'promoteTargetType' => 'required|in:permanent,contract',
            'promotePositionId' => 'nullable|exists:positions,id',
            'promoteNewPosition' => 'required|string|max:255',
            'promoteDepartmentId' => 'nullable|exists:departments,id',
        ]);

        $employee = EmployeeProfile::findOrFail($this->promoteId);
        $dept = $this->promoteDepartmentId ? Department::find($this->promoteDepartmentId) : null;
        $pos = $this->promotePositionId ? Position::find($this->promotePositionId) : null;
        $finalTitle = $this->promoteNewPosition ?: ($pos?->name ?? 'Staff');

        $employee->update([
            'employee_type' => $this->promoteTargetType,
            'position_id' => $this->promotePositionId ?: null,
            'position_title' => $finalTitle,
            'department_id' => $this->promoteDepartmentId ?: $employee->department_id,
            'company_id' => $dept?->company_id ?? $employee->company_id,
        ]);

        $statusText = $this->promoteTargetType === 'permanent' ? 'Karyawan Tetap' : 'Karyawan Kontrak';
        $this->closePromoteModal();
        session()->flash('message', 'Selamat! ' . $employee->full_name . ' berhasil diangkat menjadi ' . $statusText . ' dengan jabatan "' . $this->promoteNewPosition . '".');
    }

    public function render()
    {
        $companies = Company::orderBy('name', 'asc')->get();
        $departments = Department::with('company')->orderBy('name', 'asc')->get();
        $filterDepartments = Department::with('company')->when($this->companyId, function ($q) {
            $q->where('company_id', $this->companyId);
        })->orderBy('name', 'asc')->get();

        $positions = Position::with('department')->orderBy('name', 'asc')->get();

        $employees = EmployeeProfile::with(['user', 'company', 'department.company', 'position'])
            ->when($this->search, function ($query) {
                $search = strtolower(trim($this->search));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(full_name) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(nik) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(position_title) LIKE ?', ['%' . $search . '%'])
                      ->orWhereHas('position', function ($pq) use ($search) {
                          $pq->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%']);
                      })
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->whereRaw('LOWER(email) LIKE ?', ['%' . $search . '%']);
                      });
                });
            })
            ->when($this->companyId, function ($query) {
                $query->where(function ($q) {
                    $q->where('company_id', $this->companyId)
                      ->orWhereHas('department', function ($d) {
                          $d->where('company_id', $this->companyId);
                      });
                });
            })
            ->when($this->departmentId, function ($query) {
                $query->where('department_id', $this->departmentId);
            })
            ->when($this->employeeType, function ($query) {
                $query->where('employee_type', $this->employeeType);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.employee.table', [
            'employees' => $employees,
            'companies' => $companies,
            'departments' => $departments,
            'filterDepartments' => $filterDepartments,
            'positions' => $positions,
        ]);
    }
}

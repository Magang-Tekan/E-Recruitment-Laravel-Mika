<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmployeeProfile;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\Setting;
use Illuminate\Validation\Rule;
use App\Livewire\Traits\WithTableSkeleton;

class EmployeeTable extends Component
{
    use WithPagination, WithTableSkeleton;

    public $search = '';
    public $companyId = '';
    public $departmentId = '';
    public $employeeType = '';
    public $employmentStatus = '';
    public $expiringSoon = false;
    public $perPage = 10;

    // Modal Edit State
    public $showEditModal = false;
    public $editId = null;
    public $editFullName = '';
    public $editNik = '';
    public $editEmployeeType = 'permanent';
    public $editContractStartDate = '';
    public $editContractEndDate = '';
    public $editCompanyId = '';
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
    public $promoteOriginLabel = 'Magang';
    public $promoteCurrentPosition = '';
    public $promoteCurrentCompany = '';
    public $promoteCompanyId = '';
    public $promotePositionId = '';
    public $promoteNewPosition = '';
    public $promoteTargetType = 'permanent';
    public $promoteContractStartDate = '';
    public $promoteContractEndDate = '';
    public $promoteDepartmentId = '';
    public $promoteDepartmentName = '';

    // Modal Perpanjangan Kontrak State
    public $showExtendModal = false;
    public $extendId = null;
    public $extendName = '';
    public $extendTypeLabel = '';
    public $extendStartDate = null;
    public $extendCurrentEnd = null;
    public $extendNewEnd = '';

    // Modal Konfirmasi Status Kepegawaian State
    public $showStatusModal = false;
    public $statusId = null;
    public $statusName = '';
    public $statusAction = '';

    // Modal Master Passkey State
    public $showPasskeyModal = false;
    public $masterPasskey = '';
    public $showPasskeyText = true;

    public function updatedEditCompanyId()
    {
        if ($this->editDepartmentId) {
            $dept = Department::find($this->editDepartmentId);
            if ($dept && (string) $dept->company_id !== (string) $this->editCompanyId) {
                $this->editDepartmentId = '';
                $this->editPositionId = '';
            }
        }
        if ($this->editPositionId) {
            $pos = Position::with('department')->find($this->editPositionId);
            if ($pos && (string) ($pos->department?->company_id) !== (string) $this->editCompanyId) {
                $this->editPositionId = '';
            }
        }
    }

    public function updatedEditDepartmentId()
    {
        if ($this->editDepartmentId) {
            $dept = Department::find($this->editDepartmentId);
            if ($dept && $dept->company_id) {
                $this->editCompanyId = (string) $dept->company_id;
            }
        }

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
            $pos = Position::with('department')->find($this->editPositionId);
            if ($pos) {
                $this->editPositionTitle = $pos->name;
                if (!$this->editDepartmentId && $pos->department_id) {
                    $this->editDepartmentId = (string) $pos->department_id;
                }
                if (!$this->editCompanyId && $pos->department?->company_id) {
                    $this->editCompanyId = (string) $pos->department->company_id;
                }
            }
        }
    }

    public function updatedPromoteCompanyId()
    {
        if ($this->promoteDepartmentId) {
            $dept = Department::find($this->promoteDepartmentId);
            if ($dept && (string) $dept->company_id !== (string) $this->promoteCompanyId) {
                $this->promoteDepartmentId = '';
                $this->promotePositionId = '';
            }
        }
        if ($this->promotePositionId) {
            $pos = Position::with('department')->find($this->promotePositionId);
            if ($pos && (string) ($pos->department?->company_id) !== (string) $this->promoteCompanyId) {
                $this->promotePositionId = '';
            }
        }
    }

    public function updatedPromoteDepartmentId()
    {
        if ($this->promoteDepartmentId) {
            $dept = Department::find($this->promoteDepartmentId);
            if ($dept && $dept->company_id) {
                $this->promoteCompanyId = (string) $dept->company_id;
            }
        }

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
            $pos = Position::with('department')->find($this->promotePositionId);
            if ($pos) {
                $this->promoteNewPosition = $pos->name;
                if (!$this->promoteDepartmentId && $pos->department_id) {
                    $this->promoteDepartmentId = (string) $pos->department_id;
                }
                if (!$this->promoteCompanyId && $pos->department?->company_id) {
                    $this->promoteCompanyId = (string) $pos->department->company_id;
                }
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

    public function updatingEmploymentStatus()
    {
        $this->expiringSoon = false;
        $this->resetPage();
    }

    public function setEmploymentStatus(string $status = '')
    {
        $this->employmentStatus = $status;
        $this->expiringSoon = false;
        $this->resetPage();
    }

    public function updatingExpiringSoon()
    {
        $this->resetPage();
    }

    public function toggleExpiringSoon()
    {
        $this->expiringSoon = !$this->expiringSoon;
        if ($this->expiringSoon) {
            $this->employmentStatus = '';
            $this->employeeType = '';
        }
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->companyId = '';
        $this->departmentId = '';
        $this->employeeType = '';
        $this->employmentStatus = '';
        $this->expiringSoon = false;
        $this->resetPage();
    }

    public function openEdit($id)
    {
        $employee = EmployeeProfile::with(['user', 'department.company'])->findOrFail($id);
        $this->editId = $employee->id;
        $this->editFullName = $employee->full_name ?? ($employee->user?->name ?? '');
        $this->editNik = $employee->nik ?? ($employee->user?->nik ?? '');
        $this->editEmployeeType = $employee->employee_type ?? 'permanent';
        $this->editContractStartDate = $employee->contract_start_date?->format('Y-m-d') ?? '';
        $this->editContractEndDate = $employee->contract_end_date?->format('Y-m-d') ?? '';
        $this->editCompanyId = (string) ($employee->company_id ?: ($employee->department?->company_id ?: ''));
        $this->editDepartmentId = $employee->department_id ? (string) $employee->department_id : '';
        $this->editPositionId = $employee->position_id ? (string) $employee->position_id : '';
        $this->editPositionTitle = $employee->position_title ?? ($employee->position?->name ?? '');
        $this->resetErrorBag();
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editId', 'editFullName', 'editNik', 'editEmployeeType', 'editContractStartDate', 'editContractEndDate', 'editCompanyId', 'editDepartmentId', 'editPositionId', 'editPositionTitle']);
    }

    public function saveEmployee()
    {
        $employee = EmployeeProfile::findOrFail($this->editId);

        $rules = [
            'editFullName' => 'required|string|max:255',
            'editNik' => [
                'required',
                'digits:16',
                Rule::unique('employee_profiles', 'nik')->ignore($this->editId),
                Rule::unique('users', 'nik')->ignore($employee->user_id),
            ],
            'editEmployeeType' => 'required|in:permanent,contract,internship,probation',
            'editContractStartDate' => 'nullable|date',
            'editContractEndDate' => array_filter([
                'nullable',
                'date',
                $this->editContractStartDate ? 'after:editContractStartDate' : null,
            ]),
            'editCompanyId' => 'nullable|exists:companies,id',
            'editDepartmentId' => 'nullable|exists:departments,id',
            'editPositionId' => 'nullable|exists:positions,id',
            'editPositionTitle' => 'nullable|string|max:255',
        ];

        $this->validate($rules, [
            'editFullName.required' => 'Nama lengkap wajib diisi.',
            'editNik.required' => 'NIK wajib diisi.',
            'editNik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'editNik.unique' => 'NIK sudah digunakan oleh pegawai/pengguna lain.',
            'editEmployeeType.required' => 'Tipe status pegawai wajib dipilih.',
            'editContractEndDate.after' => 'Tanggal berakhir harus setelah tanggal mulai bekerja.',
        ]);

        $dept = $this->editDepartmentId ? Department::find($this->editDepartmentId) : null;
        $pos = $this->editPositionId ? Position::find($this->editPositionId) : null;
        $finalPositionTitle = $this->editPositionTitle ?: ($pos?->name ?? null);
        $finalCompanyId = $this->editCompanyId ?: ($dept?->company_id ?? $employee->company_id);

        $startDate = $this->editEmployeeType === 'permanent' ? null : ($this->editContractStartDate ?: null);
        $endDate = $this->editEmployeeType === 'permanent' ? null : ($this->editContractEndDate ?: null);

        // Status kepegawaian mengikuti tipe dan tanggal berakhir yang dipilih admin.
        $employmentStatus = $employee->employment_status ?: EmployeeProfile::STATUS_ACTIVE;
        if ($this->editEmployeeType === 'permanent') {
            $employmentStatus = EmployeeProfile::STATUS_ACTIVE;
        } elseif ($employmentStatus !== EmployeeProfile::STATUS_INACTIVE) {
            $isExpired = $endDate && \Carbon\Carbon::parse($endDate)->lt(now()->startOfDay());
            $employmentStatus = $isExpired
                ? EmployeeProfile::STATUS_CONTRACT_ENDED
                : EmployeeProfile::STATUS_ACTIVE;
        }

        $employee->update([
            'full_name' => $this->editFullName,
            'nik' => $this->editNik,
            'employee_type' => $this->editEmployeeType,
            'employment_status' => $employmentStatus,
            'contract_start_date' => $startDate,
            'contract_end_date' => $endDate,
            'company_id' => $finalCompanyId ?: null,
            'department_id' => $this->editDepartmentId ?: null,
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
        $employee = EmployeeProfile::with(['user', 'department.company', 'company', 'position'])->findOrFail($id);
        $isProbation = $employee->employee_type === 'probation';

        $this->promoteId = $employee->id;
        $this->promoteFullName = $employee->full_name ?? ($employee->user?->name ?? ($isProbation ? 'Karyawan Probation' : 'Peserta Magang'));
        $this->promoteOriginLabel = $isProbation ? 'Masa Percobaan' : 'Magang';
        $this->promoteCurrentPosition = $employee->position?->name ?? ($employee->position_title ?? ($isProbation ? 'Staff Probation' : 'Intern / Magang'));
        $this->promoteCurrentCompany = $employee->company?->name ?? ($employee->department?->company?->name ?? '');
        $this->promoteCompanyId = (string) ($employee->company_id ?: ($employee->department?->company_id ?: ''));
        $this->promoteDepartmentId = $employee->department_id ? (string) $employee->department_id : '';
        $this->promoteDepartmentName = $employee->department?->name ?? 'Belum Diatur';
        $this->promotePositionId = $employee->position_id ? (string) $employee->position_id : '';
        $this->promoteNewPosition = $employee->position_title
            ? trim(str_ireplace(['intern', 'magang', 'internship', 'probation'], $isProbation ? '' : 'Staff', $employee->position_title)) ?: 'Staff'
            : 'Staff';
        $this->promoteTargetType = 'permanent';
        $this->promoteContractStartDate = now()->toDateString();
        $this->promoteContractEndDate = '';
        $this->resetErrorBag();
        $this->showPromoteModal = true;
    }

    public function closePromoteModal()
    {
        $this->showPromoteModal = false;
        $this->reset(['promoteId', 'promoteFullName', 'promoteOriginLabel', 'promoteCurrentPosition', 'promoteCurrentCompany', 'promoteCompanyId', 'promotePositionId', 'promoteNewPosition', 'promoteTargetType', 'promoteContractStartDate', 'promoteContractEndDate', 'promoteDepartmentId', 'promoteDepartmentName']);
    }

    public function confirmPromotion()
    {
        $rules = [
            'promoteTargetType' => 'required|in:permanent,contract',
            'promoteCompanyId' => 'nullable|exists:companies,id',
            'promoteDepartmentId' => 'nullable|exists:departments,id',
            'promotePositionId' => 'nullable|exists:positions,id',
            'promoteNewPosition' => 'required|string|max:255',
        ];

        if ($this->promoteTargetType === 'contract') {
            $rules['promoteContractStartDate'] = 'required|date';
            $rules['promoteContractEndDate'] = 'required|date|after:promoteContractStartDate';
        }

        $this->validate($rules, [
            'promoteContractStartDate.required' => 'Tanggal mulai kontrak wajib diisi.',
            'promoteContractEndDate.required' => 'Tanggal berakhir kontrak wajib diisi.',
            'promoteContractEndDate.after' => 'Tanggal berakhir kontrak harus setelah tanggal mulai.',
        ]);

        $employee = EmployeeProfile::findOrFail($this->promoteId);
        $dept = $this->promoteDepartmentId ? Department::find($this->promoteDepartmentId) : null;
        $pos = $this->promotePositionId ? Position::find($this->promotePositionId) : null;
        $finalTitle = $this->promoteNewPosition ?: ($pos?->name ?? 'Staff');
        $finalCompanyId = $this->promoteCompanyId ?: ($dept?->company_id ?? $employee->company_id);

        $employee->update([
            'employee_type' => $this->promoteTargetType,
            'employment_status' => EmployeeProfile::STATUS_ACTIVE,
            'contract_start_date' => $this->promoteTargetType === 'permanent' ? null : $this->promoteContractStartDate,
            'contract_end_date' => $this->promoteTargetType === 'permanent' ? null : $this->promoteContractEndDate,
            'company_id' => $finalCompanyId ?: null,
            'department_id' => $this->promoteDepartmentId ?: null,
            'position_id' => $this->promotePositionId ?: null,
            'position_title' => $finalTitle,
        ]);

        $statusText = $this->promoteTargetType === 'permanent' ? 'Karyawan Tetap' : 'Karyawan Kontrak';
        $this->closePromoteModal();
        session()->flash('message', 'Selamat! ' . $employee->full_name . ' berhasil diangkat menjadi ' . $statusText . ' dengan jabatan "' . $finalTitle . '".');
    }

    public function openExtendModal($id)
    {
        $employee = EmployeeProfile::with('user')->findOrFail($id);

        $this->extendId = $employee->id;
        $this->extendName = $employee->full_name ?? ($employee->user?->name ?? 'Karyawan');
        $this->extendTypeLabel = match ($employee->employee_type) {
            'contract'   => 'Karyawan Kontrak',
            'internship' => 'Magang',
            'probation'  => 'Masa Percobaan',
            default      => 'Karyawan Tetap',
        };
        $this->extendStartDate = $employee->contract_start_date?->translatedFormat('d F Y');
        $this->extendCurrentEnd = $employee->contract_end_date?->translatedFormat('d F Y');

        // Default mengikuti tanggal berakhir yang berlaku agar tidak membingungkan admin.
        // Bila kontraknya sudah lewat/tidak ada, pakai besok (batas minimum perpanjangan).
        $this->extendNewEnd = $employee->contract_end_date && $employee->contract_end_date->isFuture()
            ? $employee->contract_end_date->format('Y-m-d')
            : now()->addDay()->format('Y-m-d');

        $this->resetErrorBag('extendNewEnd');
        $this->showExtendModal = true;
    }

    public function closeExtendModal()
    {
        $this->showExtendModal = false;
        $this->reset(['extendId', 'extendName', 'extendTypeLabel', 'extendStartDate', 'extendCurrentEnd', 'extendNewEnd']);
    }

    public function extendContract()
    {
        $this->validate([
            'extendNewEnd' => 'required|date|after:today',
        ], [
            'extendNewEnd.required' => 'Tanggal berakhir baru wajib diisi.',
            'extendNewEnd.date'     => 'Format tanggal berakhir tidak valid.',
            'extendNewEnd.after'    => 'Tanggal berakhir baru harus setelah hari ini.',
        ]);

        $employee = EmployeeProfile::findOrFail($this->extendId);

        if ($employee->employee_type === 'permanent') {
            $this->closeExtendModal();
            session()->flash('message', 'Karyawan tetap tidak memiliki tanggal kontrak berakhir, jadi kontraknya tidak perlu diperpanjang.');

            return;
        }

        $employee->update([
            'contract_end_date' => $this->extendNewEnd,
            'employment_status' => EmployeeProfile::STATUS_ACTIVE,
        ]);

        $name = $employee->full_name ?? ($employee->user?->name ?? 'Karyawan');
        $newEnd = $employee->contract_end_date->translatedFormat('d F Y');

        $this->closeExtendModal();
        session()->flash('message', "Kontrak {$name} berhasil diperpanjang sampai {$newEnd}. Tipe dan role pegawai tidak berubah.");
    }

    public function openStatusModal($id, $action)
    {
        $employee = EmployeeProfile::with('user')->findOrFail($id);

        $this->statusId = $employee->id;
        $this->statusName = $employee->full_name ?? ($employee->user?->name ?? 'Karyawan');
        $this->statusAction = $action;
        $this->resetErrorBag('statusAction');
        $this->showStatusModal = true;
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->reset(['statusId', 'statusName', 'statusAction']);
    }

    public function confirmStatusChange()
    {
        $this->validate([
            'statusAction' => 'required|in:permanent,inactive,reactivate',
        ]);

        $employee = EmployeeProfile::with('user')->findOrFail($this->statusId);
        $name = $employee->full_name ?? ($employee->user?->name ?? 'Karyawan');

        switch ($this->statusAction) {
            case 'permanent':
                $employee->update([
                    'employee_type' => 'permanent',
                    'employment_status' => EmployeeProfile::STATUS_ACTIVE,
                    'contract_start_date' => null,
                    'contract_end_date' => null,
                ]);
                $message = "{$name} berhasil diangkat menjadi karyawan tetap. Tanggal kontrak dikosongkan.";
                break;

            case 'inactive':
                $employee->update(['employment_status' => EmployeeProfile::STATUS_INACTIVE]);
                $message = "Akun {$name} dinonaktifkan. Role tetap karyawan, tetapi login diblokir sampai diaktifkan kembali.";
                break;

            default:
                $stillExpired = $employee->employee_type !== 'permanent'
                    && $employee->contract_end_date
                    && $employee->contract_end_date->isPast();

                $employee->update([
                    'employment_status' => $stillExpired
                        ? EmployeeProfile::STATUS_CONTRACT_ENDED
                        : EmployeeProfile::STATUS_ACTIVE,
                ]);
                $message = $stillExpired
                    ? "Akun {$name} diaktifkan kembali, tetapi kontraknya sudah lewat sehingga statusnya tetap 'Kontrak Berakhir'."
                    : "Akun {$name} diaktifkan kembali.";
                break;
        }

        $this->closeStatusModal();
        session()->flash('message', $message);
    }

    public function openPasskeyModal()
    {
        $this->masterPasskey = Setting::get('employee_registration_passkey', env('EMPLOYEE_REGISTRATION_PASSKEY', 'MIKA2026'));
        $this->showPasskeyText = true;
        $this->resetErrorBag('masterPasskey');
        $this->showPasskeyModal = true;
    }

    public function closePasskeyModal()
    {
        $this->showPasskeyModal = false;
        $this->resetErrorBag('masterPasskey');
    }

    public function togglePasskeyVisibility()
    {
        $this->showPasskeyText = !$this->showPasskeyText;
    }

    public function generateRandomPasskey()
    {
        $this->masterPasskey = 'MIKA' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
    }

    public function savePasskey()
    {
        $this->validate([
            'masterPasskey' => 'required|string|min:4|max:50',
        ], [
            'masterPasskey.required' => 'Master passkey pendaftaran tidak boleh kosong.',
            'masterPasskey.min' => 'Master passkey minimal 4 karakter.',
            'masterPasskey.max' => 'Master passkey maksimal 50 karakter.',
        ]);

        Setting::set('employee_registration_passkey', trim($this->masterPasskey), 'Master Passkey default registrasi akun karyawan');

        $this->showPasskeyModal = false;
        session()->flash('message', 'Master Passkey pendaftaran karyawan berhasil diperbarui menjadi: ' . trim($this->masterPasskey));
    }

    public function render()
    {
        $companies = Company::orderBy('name', 'asc')->get();
        $departments = Department::with('company')->orderBy('name', 'asc')->get();
        $filterDepartments = Department::with('company')->when($this->companyId, function ($q) {
            $q->where('company_id', $this->companyId);
        })->orderBy('name', 'asc')->get();

        $positions = Position::with('department.company')->orderBy('name', 'asc')->get();

        // Departments & Positions for Edit Modal
        $editDepartments = Department::with('company')
            ->when($this->editCompanyId, fn($q) => $q->where('company_id', $this->editCompanyId))
            ->orderBy('name', 'asc')
            ->get();

        $editPositions = Position::with('department.company')
            ->when($this->editDepartmentId, function ($q) {
                $q->where('department_id', $this->editDepartmentId);
            }, function ($q) {
                if ($this->editCompanyId) {
                    $q->whereHas('department', fn($dq) => $dq->where('company_id', $this->editCompanyId));
                }
            })
            ->orderBy('name', 'asc')
            ->get();

        // Departments & Positions for Promote Modal
        $promoteDepartments = Department::with('company')
            ->when($this->promoteCompanyId, fn($q) => $q->where('company_id', $this->promoteCompanyId))
            ->orderBy('name', 'asc')
            ->get();

        $promotePositions = Position::with('department.company')
            ->when($this->promoteDepartmentId, function ($q) {
                $q->where('department_id', $this->promoteDepartmentId);
            }, function ($q) {
                if ($this->promoteCompanyId) {
                    $q->whereHas('department', fn($dq) => $dq->where('company_id', $this->promoteCompanyId));
                }
            })
            ->orderBy('name', 'asc')
            ->get();

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
            ->when($this->employmentStatus, function ($query) {
                $query->where('employment_status', $this->employmentStatus);
            })
            ->when($this->expiringSoon, function ($query) {
                $query->where('employment_status', EmployeeProfile::STATUS_ACTIVE)
                    ->whereIn('employee_type', ['contract', 'internship', 'probation'])
                    ->whereNotNull('contract_end_date')
                    ->whereDate('contract_end_date', '<=', now()->addDays(30)->toDateString());
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        $expiringSoonQuery = EmployeeProfile::query()
            ->where('employment_status', EmployeeProfile::STATUS_ACTIVE)
            ->whereIn('employee_type', ['contract', 'internship', 'probation'])
            ->whereNotNull('contract_end_date')
            ->whereDate('contract_end_date', '<=', now()->addDays(30)->toDateString());

        $expiringSoonCount = (clone $expiringSoonQuery)->count();

        $statusCounts = EmployeeProfile::query()
            ->selectRaw('employment_status, COUNT(*) as total')
            ->groupBy('employment_status')
            ->pluck('total', 'employment_status');

        return view('livewire.admin.employee.table', [
            'employees' => $employees,
            'companies' => $companies,
            'departments' => $departments,
            'filterDepartments' => $filterDepartments,
            'positions' => $positions,
            'editDepartments' => $editDepartments,
            'editPositions' => $editPositions,
            'promoteDepartments' => $promoteDepartments,
            'promotePositions' => $promotePositions,
            'expiringSoonCount' => $expiringSoonCount,
            'statusCounts' => [
                'active' => (int) ($statusCounts[EmployeeProfile::STATUS_ACTIVE] ?? 0),
                'contract_ended' => (int) ($statusCounts[EmployeeProfile::STATUS_CONTRACT_ENDED] ?? 0),
                'inactive' => (int) ($statusCounts[EmployeeProfile::STATUS_INACTIVE] ?? 0),
            ],
        ]);
    }
}

<?php

namespace App\Livewire\Employee;

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\EmployeeProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmployeeProfileSettings extends Component
{
    use WithFileUploads;

    public $nik;

    public $full_name;

    public $company_id;

    public $department_id;

    public $position_id;

    public $position_title;

    public $employee_type = 'permanent';

    public $employeeTypeLabel = 'Karyawan Tetap';

    public $employment_status = 'active';

    public $employmentStatusLabel = 'Aktif';

    public $contract_start_date = null;

    public $contract_end_date = null;

    public $isNikLocked = false;

    public $isPlacementLocked = false;

    public $photo;

    public $cropped_photo_base64;

    public $current_photo_url;

    public function mount(): void
    {
        $user = Auth::user();
        $profile = EmployeeProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $user->name,
                'nik' => $user->nik,
            ]
        );
        $profile->loadMissing(['company', 'department.company', 'position']);

        $this->nik = $profile->nik ?? $user->nik;
        $this->full_name = $profile->full_name ?? $user->name;
        $this->company_id = $profile->company_id;
        $this->department_id = $profile->department_id;
        $this->position_id = $profile->position_id ? (string) $profile->position_id : null;
        $this->position_title = $profile->position_title ?? ($profile->position?->name ?? '');
        $this->employee_type = $profile->employee_type ?? 'permanent';
        $this->employment_status = $profile->employment_status ?? 'active';

        $this->isNikLocked = ! empty($profile->nik);
        $this->isPlacementLocked = ! empty($profile->company_id)
            || ! empty($profile->department_id)
            || ! empty($profile->position_id)
            || ! empty($profile->position_title);

        // Tipe pegawai & tanggal kontrak hanya boleh diubah Admin/HR.
        $this->employeeTypeLabel = match ($this->employee_type) {
            'contract' => 'Karyawan Kontrak',
            'internship' => 'Magang / Internship',
            'probation' => 'Masa Percobaan (Probation)',
            default => 'Karyawan Tetap',
        };
        $this->employmentStatusLabel = $profile->employmentStatusLabel();
        $this->contract_start_date = $profile->contract_start_date?->translatedFormat('d F Y');
        $this->contract_end_date = $profile->contract_end_date?->translatedFormat('d F Y');

        $this->current_photo_url = $profile->photo
            ? asset('storage/' . $profile->photo) . '?v=' . time()
            : null;
    }

    public function updatedCompanyId(): void
    {
        if ($this->department_id) {
            $department = Department::find($this->department_id);
            if (! $department || (string) $department->company_id !== (string) $this->company_id) {
                $this->department_id = null;
                $this->position_id = null;
            }
        }
    }

    public function updatedDepartmentId(): void
    {
        if ($this->department_id) {
            $department = Department::find($this->department_id);
            if ($department?->company_id) {
                $this->company_id = $department->company_id;
            }
            if ($this->position_id) {
                $pos = Position::find($this->position_id);
                if ($pos && (string) $pos->department_id !== (string) $this->department_id) {
                    $this->position_id = null;
                }
            }
        }
    }

    public function updatedPositionId(): void
    {
        if ($this->position_id) {
            $pos = Position::find($this->position_id);
            if ($pos) {
                $this->position_title = $pos->name;
            }
        }
    }

    protected function rules(): array
    {
        $user = Auth::user();
        $profile = EmployeeProfile::where('user_id', $user->id)->first();

        return [
            'nik' => [
                'nullable',
                'digits:16',
                Rule::unique('employee_profiles', 'nik')->ignore($profile?->id),
                Rule::unique('users', 'nik')->ignore($user->id),
            ],
            'full_name' => 'required|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'position_title' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:5120',
        ];
    }

    protected function messages(): array
    {
        return [
            'nik.digits' => 'NIK harus berupa 16 digit angka.',
            'nik.unique' => 'NIK sudah digunakan oleh pengguna lain.',
        ];
    }

    public function save(): void
    {
        $user = Auth::user();
        $profile = EmployeeProfile::firstOrCreate(['user_id' => $user->id]);

        $nikLocked = ! empty($profile->nik);
        $placementLocked = ! empty($profile->company_id)
            || ! empty($profile->department_id)
            || ! empty($profile->position_id)
            || ! empty($profile->position_title);

        if ($nikLocked) {
            $this->nik = $profile->nik;
        }
        if ($placementLocked) {
            $this->company_id = $profile->company_id;
            $this->department_id = $profile->department_id;
            $this->position_id = $profile->position_id ? (string) $profile->position_id : null;
            $this->position_title = $profile->position_title;
        }

        $this->company_id = $this->company_id ?: null;
        $this->department_id = $this->department_id ?: null;
        $this->position_id = $this->position_id ?: null;

        $validatedData = $this->validate();

        if ($nikLocked) {
            unset($validatedData['nik']);
        }
        if ($placementLocked) {
            unset($validatedData['company_id'], $validatedData['department_id'], $validatedData['position_id'], $validatedData['position_title']);
        }

        if ($this->cropped_photo_base64) {
            if ($profile->photo && Storage::disk('public')->exists($profile->photo)) {
                Storage::disk('public')->delete($profile->photo);
            }

            $imageParts = explode(';base64,', $this->cropped_photo_base64);
            $imageTypeAux = explode('image/', $imageParts[0]);
            $imageType = isset($imageTypeAux[1]) ? strtolower($imageTypeAux[1]) : 'jpeg';
            if ($imageType === 'jpeg') {
                $imageType = 'jpg';
            }
            $imageBase64 = base64_decode($imageParts[1]);

            $fileName = 'employee-photos/' . uniqid() . '.' . $imageType;
            Storage::disk('public')->put($fileName, $imageBase64);

            $validatedData['photo'] = $fileName;
            $this->current_photo_url = asset('storage/' . $fileName) . '?v=' . time();
            $this->photo = null;
            $this->cropped_photo_base64 = null;
        } elseif ($this->photo) {
            if ($profile->photo && Storage::disk('public')->exists($profile->photo)) {
                Storage::disk('public')->delete($profile->photo);
            }
            $photoPath = $this->photo->store('employee-photos', 'public');
            $validatedData['photo'] = $photoPath;
            $this->current_photo_url = asset('storage/' . $photoPath) . '?v=' . time();
            $this->photo = null;
        } else {
            unset($validatedData['photo']);
        }

        if (! $placementLocked && $this->department_id && empty($this->company_id)) {
            $department = Department::find($this->department_id);
            if ($department?->company_id) {
                $validatedData['company_id'] = $department->company_id;
            }
        }

        $profile->update($validatedData);

        $this->isNikLocked = ! empty($profile->nik);
        $this->isPlacementLocked = ! empty($profile->company_id)
            || ! empty($profile->department_id)
            || ! empty($profile->position_id)
            || ! empty($profile->position_title);

        $userUpdates = [];
        if ($user->name !== $this->full_name) {
            $userUpdates['name'] = $this->full_name;
        }
        if ($user->nik !== $this->nik) {
            $userUpdates['nik'] = $this->nik;
        }
        if (! empty($validatedData['photo'])) {
            $userUpdates['avatar'] = $validatedData['photo'];
        }
        if (! empty($userUpdates)) {
            $user->update($userUpdates);
        }

        session()->flash('employee_profile_message', 'Profil karyawan berhasil diperbarui.');
        $this->dispatch('profile-updated', name: $this->full_name, photo: $this->current_photo_url);
    }

    public function render()
    {
        $companies = Company::orderBy('name')->get();
        $departments = Department::with('company')
            ->when($this->company_id, fn ($query) => $query->where('company_id', $this->company_id))
            ->orderBy('name')
            ->get();
        $positions = Position::with('department')->orderBy('name')->get();

        return view('livewire.employee.employee-profile-settings', [
            'companies' => $companies,
            'departments' => $departments,
            'positions' => $positions,
        ]);
    }
}

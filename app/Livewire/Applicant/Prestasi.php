<?php

namespace App\Livewire\Applicant;

use App\Models\Achievement;
use App\Models\ApplicantProfile;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Prestasi extends Component
{
    use WithFileUploads;

    public $showDeleteModal = false;
    public $deleteId = null;
    public $deleteType = null; // 'organization' or 'achievement'

    // --- ORGANISASI STATE ---
    public $showOrganizationModal = false;
    public $isEditOrganization = false;
    public $organization_id;
    public $org_name;
    public $org_position;
    public $org_description;
    public $org_is_active = false;
    public $org_start_month;
    public $org_start_year;
    public $org_end_month;
    public $org_end_year;

    // --- ACHIEVEMENT STATE ---
    public $showAchievementModal = false;
    public $isEditAchievement = false;
    public $achievement_id;
    public $achievement_name;
    public $achievement_scale;
    public $achievement_month;
    public $achievement_year;
    public $achievement_description;
    public $achievement_certificate;
    public $existing_achievement_certificate;

    public function updatedOrgIsActive($value)
    {
        if ($value) {
            $this->org_end_month = null;
            $this->org_end_year = null;
        }
    }

    // --- DELETE CONFIRMATION ---
    public function confirmDeleteOrganization($id)
    {
        $this->deleteId = $id;
        $this->deleteType = 'organization';
        $this->showDeleteModal = true;
    }

    public function confirmDeleteAchievement($id)
    {
        $this->deleteId = $id;
        $this->deleteType = 'achievement';
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->deleteType = null;
    }

    public function executeDelete()
    {
        if ($this->deleteType === 'organization' && $this->deleteId) {
            return $this->deleteOrganization($this->deleteId);
        } elseif ($this->deleteType === 'achievement' && $this->deleteId) {
            return $this->deleteAchievement($this->deleteId);
        }

        $this->cancelDelete();
    }

    // --- ORGANISASI METHODS ---
    public function openOrganizationModal()
    {
        $this->resetOrganizationForm();
        $this->isEditOrganization = false;
        $this->showOrganizationModal = true;
    }

    public function closeOrganizationModal()
    {
        $this->showOrganizationModal = false;
        $this->resetOrganizationForm();
    }

    public function resetOrganizationForm()
    {
        $this->reset([
            'organization_id',
            'org_name',
            'org_position',
            'org_description',
            'org_is_active',
            'org_start_month',
            'org_start_year',
            'org_end_month',
            'org_end_year',
        ]);
        $this->resetErrorBag();
    }

    public function editOrganization($id)
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        if (!$profile) return;

        $org = Organization::where('profile_id', $profile->id)->where('id', $id)->firstOrFail();

        $this->organization_id = $org->id;
        $this->org_name = $org->name;
        $this->org_position = $org->position;
        $this->org_description = $org->description;
        $this->org_is_active = (bool) $org->is_active;
        $this->org_start_month = $org->start_month;
        $this->org_start_year = $org->start_year;
        $this->org_end_month = $org->end_month;
        $this->org_end_year = $org->end_year;

        $this->isEditOrganization = true;
        $this->showOrganizationModal = true;
    }

    public function saveOrganization()
    {
        $currentYear = (int) date('Y');

        $this->validate([
            'org_name' => 'required|string|max:255',
            'org_position' => 'required|string|max:255',
            'org_start_month' => 'required|string',
            'org_start_year' => 'required|integer|digits:4|min:1950|max:' . ($currentYear + 5),
            'org_is_active' => 'boolean',
            'org_end_month' => $this->org_is_active ? 'nullable' : 'nullable|string',
            'org_end_year' => $this->org_is_active ? 'nullable' : 'nullable|integer|digits:4|gte:org_start_year|max:' . ($currentYear + 10),
            'org_description' => 'nullable|string|max:2000',
        ], [
            'org_name.required' => __('Nama organisasi wajib diisi.'),
            'org_position.required' => __('Jabatan / posisi wajib diisi.'),
            'org_start_month.required' => __('Bulan mulai wajib dipilih.'),
            'org_start_year.required' => __('Tahun mulai wajib diisi.'),
            'org_start_year.digits' => __('Tahun mulai harus 4 digit angka.'),
            'org_end_year.gte' => __('Tahun selesai harus lebih besar atau sama dengan tahun mulai.'),
        ]);

        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        $data = [
            'name' => $this->org_name,
            'position' => $this->org_position,
            'description' => $this->org_description,
            'is_active' => $this->org_is_active,
            'start_month' => $this->org_start_month,
            'start_year' => $this->org_start_year,
            'end_month' => $this->org_is_active ? null : $this->org_end_month,
            'end_year' => $this->org_is_active ? null : $this->org_end_year,
        ];

        if ($this->isEditOrganization && $this->organization_id) {
            $org = Organization::where('profile_id', $profile->id)->where('id', $this->organization_id)->firstOrFail();
            $org->update($data);
            $msg = __('Riwayat organisasi berhasil diperbarui.');
            $action = 'update';
        } else {
            $data['profile_id'] = $profile->id;
            Organization::create($data);
            $msg = __('Riwayat organisasi berhasil ditambahkan.');
            $action = 'create';
        }

        $this->closeOrganizationModal();
        $this->dispatch('profile-updated', message: $msg, action: $action);
    }

    public function deleteOrganization($id)
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        if ($profile) {
            Organization::where('profile_id', $profile->id)->where('id', $id)->delete();
        }

        $this->cancelDelete();
        $this->dispatch('profile-updated', message: __('Riwayat organisasi berhasil dihapus.'), action: 'delete');
    }

    // --- ACHIEVEMENT METHODS ---
    public function openAchievementModal()
    {
        $this->resetAchievementForm();
        $this->isEditAchievement = false;
        $this->showAchievementModal = true;
    }

    public function closeAchievementModal()
    {
        $this->showAchievementModal = false;
        $this->resetAchievementForm();
    }

    public function resetAchievementForm()
    {
        $this->reset([
            'achievement_id',
            'achievement_name',
            'achievement_scale',
            'achievement_month',
            'achievement_year',
            'achievement_description',
            'achievement_certificate',
            'existing_achievement_certificate',
        ]);
        $this->resetErrorBag();
    }

    public function editAchievement($id)
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        if (!$profile) return;

        $ach = Achievement::where('profile_id', $profile->id)->where('id', $id)->firstOrFail();

        $this->achievement_id = $ach->id;
        $this->achievement_name = $ach->name;
        $this->achievement_scale = $ach->scale;
        $this->achievement_month = $ach->month;
        $this->achievement_year = $ach->year;
        $this->achievement_description = $ach->description;
        $this->existing_achievement_certificate = $ach->certificate_path;

        $this->isEditAchievement = true;
        $this->showAchievementModal = true;
    }

    public function saveAchievement()
    {
        $currentYear = (int) date('Y');

        $this->validate([
            'achievement_name' => 'required|string|max:255',
            'achievement_scale' => 'required|string|max:100',
            'achievement_month' => 'required|string',
            'achievement_year' => 'required|integer|digits:4|min:1950|max:' . ($currentYear + 5),
            'achievement_description' => 'nullable|string|max:2000',
            'achievement_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'achievement_name.required' => __('Nama prestasi / penghargaan wajib diisi.'),
            'achievement_scale.required' => __('Tingkat / skala prestasi wajib dipilih.'),
            'achievement_month.required' => __('Bulan perolehan wajib dipilih.'),
            'achievement_year.required' => __('Tahun perolehan wajib diisi.'),
            'achievement_year.digits' => __('Tahun harus 4 digit angka.'),
            'achievement_certificate.mimes' => __('Format sertifikat harus PDF, JPG, JPEG, atau PNG.'),
            'achievement_certificate.max' => __('Ukuran sertifikat tidak boleh melebihi 2MB.'),
        ]);

        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        $certPath = $this->existing_achievement_certificate;
        if ($this->achievement_certificate) {
            if ($certPath && Storage::disk('public')->exists($certPath)) {
                Storage::disk('public')->delete($certPath);
            }
            $certPath = $this->achievement_certificate->store('certificates/achievements', 'public');
        }

        if ($this->isEditAchievement && $this->achievement_id) {
            $ach = Achievement::where('profile_id', $profile->id)->where('id', $this->achievement_id)->firstOrFail();
            $ach->update([
                'name' => $this->achievement_name,
                'scale' => $this->achievement_scale,
                'month' => $this->achievement_month,
                'year' => $this->achievement_year,
                'description' => $this->achievement_description,
                'certificate_path' => $certPath,
            ]);
            $msg = __('Prestasi berhasil diperbarui.');
            $action = 'update';
        } else {
            Achievement::create([
                'profile_id' => $profile->id,
                'name' => $this->achievement_name,
                'scale' => $this->achievement_scale,
                'month' => $this->achievement_month,
                'year' => $this->achievement_year,
                'description' => $this->achievement_description,
                'certificate_path' => $certPath,
            ]);
            $msg = __('Prestasi berhasil ditambahkan.');
            $action = 'create';
        }

        $this->closeAchievementModal();
        $this->dispatch('profile-updated', message: $msg, action: $action);
    }

    public function deleteAchievement($id)
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        if ($profile) {
            $ach = Achievement::where('profile_id', $profile->id)->where('id', $id)->first();
            if ($ach) {
                if ($ach->certificate_path && Storage::disk('public')->exists($ach->certificate_path)) {
                    Storage::disk('public')->delete($ach->certificate_path);
                }
                $ach->delete();
            }
        }

        $this->cancelDelete();
        $this->dispatch('profile-updated', message: __('Prestasi berhasil dihapus.'), action: 'delete');
    }

    public function render()
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();

        $organizations = $profile ? Organization::where('profile_id', $profile->id)->orderBy('start_year', 'desc')->get() : collect();
        $achievements = $profile ? Achievement::where('profile_id', $profile->id)->orderBy('year', 'desc')->get() : collect();

        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $scales = [
            'Internasional',
            'Nasional',
            'Provinsi',
            'Kota / Kabupaten',
            'Universitas / Sekolah',
            'Internal / Perusahaan',
            'Lainnya',
        ];

        return view('livewire.applicant.prestasi', [
            'organizations' => $organizations,
            'achievements' => $achievements,
            'months' => $months,
            'scales' => $scales,
        ]);
    }
}

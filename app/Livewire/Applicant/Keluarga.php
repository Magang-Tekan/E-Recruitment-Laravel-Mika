<?php

namespace App\Livewire\Applicant;

use App\Models\ApplicantFamily;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Keluarga extends Component
{
    // Profile level fields
    public $child_sequence;
    public $total_siblings;
    public $marital_status;

    // Family level fields - Ayah
    public $father_name;
    public $father_birth_year;
    public $father_last_education;
    public $father_occupation;
    public $father_company;

    // Family level fields - Ibu
    public $mother_name;
    public $mother_birth_year;
    public $mother_last_education;
    public $mother_occupation;
    public $mother_company;

    // Emergency Contacts - Satu tempat tinggal (rumah)
    public $emergency_same_house_name_1;
    public $emergency_same_house_phone_1;
    public $emergency_same_house_relation_1;

    public $emergency_same_house_name_2;
    public $emergency_same_house_phone_2;
    public $emergency_same_house_relation_2;

    // Emergency Contacts - Tidak satu tempat tinggal (rumah)
    public $emergency_diff_house_name_1;
    public $emergency_diff_house_phone_1;
    public $emergency_diff_house_relation_1;

    public $emergency_diff_house_name_2;
    public $emergency_diff_house_phone_2;
    public $emergency_diff_house_relation_2;

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->applicantProfile) {
            $profile = $user->applicantProfile;
            $this->child_sequence = $profile->child_sequence;
            $this->total_siblings = $profile->total_siblings;
            $this->marital_status = $profile->marital_status;

            $family = $profile->family;
            if ($family) {
                $this->father_name = $family->father_name;
                $this->father_birth_year = $family->father_birth_year;
                $this->father_last_education = $family->father_last_education;
                $this->father_occupation = $family->father_occupation;
                $this->father_company = $family->father_company;

                $this->mother_name = $family->mother_name;
                $this->mother_birth_year = $family->mother_birth_year;
                $this->mother_last_education = $family->mother_last_education;
                $this->mother_occupation = $family->mother_occupation;
                $this->mother_company = $family->mother_company;

                $contacts = $family->emergency_contacts ?? [];
                $sameHouse = $contacts['same_house'] ?? [];
                $diffHouse = $contacts['diff_house'] ?? [];

                $this->emergency_same_house_name_1 = $sameHouse[0]['name'] ?? null;
                $this->emergency_same_house_phone_1 = $sameHouse[0]['phone'] ?? null;
                $this->emergency_same_house_relation_1 = $sameHouse[0]['relation'] ?? null;

                $this->emergency_same_house_name_2 = $sameHouse[1]['name'] ?? null;
                $this->emergency_same_house_phone_2 = $sameHouse[1]['phone'] ?? null;
                $this->emergency_same_house_relation_2 = $sameHouse[1]['relation'] ?? null;

                $this->emergency_diff_house_name_1 = $diffHouse[0]['name'] ?? null;
                $this->emergency_diff_house_phone_1 = $diffHouse[0]['phone'] ?? null;
                $this->emergency_diff_house_relation_1 = $diffHouse[0]['relation'] ?? null;

                $this->emergency_diff_house_name_2 = $diffHouse[1]['name'] ?? null;
                $this->emergency_diff_house_phone_2 = $diffHouse[1]['phone'] ?? null;
                $this->emergency_diff_house_relation_2 = $diffHouse[1]['relation'] ?? null;
            }
        }
    }

    public function save()
    {
        $this->validate([
            'child_sequence' => 'nullable|numeric|min:1',
            'total_siblings' => 'nullable|numeric|min:1',
            'marital_status' => 'nullable|in:lajang,menikah,bercerai',
            'father_name' => 'nullable|string|max:255',
            'father_birth_year' => 'nullable|numeric|digits:4',
            'father_last_education' => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'father_company' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_birth_year' => 'nullable|numeric|digits:4',
            'mother_last_education' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'mother_company' => 'nullable|string|max:255',

            'emergency_same_house_name_1' => 'nullable|string|max:255',
            'emergency_same_house_phone_1' => 'nullable|string|max:30',
            'emergency_same_house_relation_1' => 'nullable|string|max:100',
            'emergency_same_house_name_2' => 'nullable|string|max:255',
            'emergency_same_house_phone_2' => 'nullable|string|max:30',
            'emergency_same_house_relation_2' => 'nullable|string|max:100',

            'emergency_diff_house_name_1' => 'nullable|string|max:255',
            'emergency_diff_house_phone_1' => 'nullable|string|max:30',
            'emergency_diff_house_relation_1' => 'nullable|string|max:100',
            'emergency_diff_house_name_2' => 'nullable|string|max:255',
            'emergency_diff_house_phone_2' => 'nullable|string|max:30',
            'emergency_diff_house_relation_2' => 'nullable|string|max:100',
        ], [
            'child_sequence.numeric' => __('Urutan anak harus berupa angka.'),
            'child_sequence.min' => __('Urutan anak minimal 1.'),
            'total_siblings.numeric' => __('Jumlah saudara harus berupa angka.'),
            'total_siblings.min' => __('Jumlah saudara minimal 1.'),
            'father_birth_year.digits' => __('Tahun lahir ayah harus 4 digit angka.'),
            'mother_birth_year.digits' => __('Tahun lahir ibu harus 4 digit angka.'),
        ]);

        $user = Auth::user();
        if (!$user) {
            return;
        }

        $profile = $user->applicantProfile()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        // Update profile fields
        $profile->update([
            'child_sequence' => $this->child_sequence ?: null,
            'total_siblings' => $this->total_siblings ?: null,
            'marital_status' => $this->marital_status ?: null,
        ]);

        // Format emergency contacts json structure
        $sameHouse = array_values(array_filter([
            (!empty($this->emergency_same_house_name_1) || !empty($this->emergency_same_house_phone_1) || !empty($this->emergency_same_house_relation_1)) ? [
                'name' => $this->emergency_same_house_name_1,
                'phone' => $this->emergency_same_house_phone_1,
                'relation' => $this->emergency_same_house_relation_1,
            ] : null,
            (!empty($this->emergency_same_house_name_2) || !empty($this->emergency_same_house_phone_2) || !empty($this->emergency_same_house_relation_2)) ? [
                'name' => $this->emergency_same_house_name_2,
                'phone' => $this->emergency_same_house_phone_2,
                'relation' => $this->emergency_same_house_relation_2,
            ] : null,
        ]));

        $diffHouse = array_values(array_filter([
            (!empty($this->emergency_diff_house_name_1) || !empty($this->emergency_diff_house_phone_1) || !empty($this->emergency_diff_house_relation_1)) ? [
                'name' => $this->emergency_diff_house_name_1,
                'phone' => $this->emergency_diff_house_phone_1,
                'relation' => $this->emergency_diff_house_relation_1,
            ] : null,
            (!empty($this->emergency_diff_house_name_2) || !empty($this->emergency_diff_house_phone_2) || !empty($this->emergency_diff_house_relation_2)) ? [
                'name' => $this->emergency_diff_house_name_2,
                'phone' => $this->emergency_diff_house_phone_2,
                'relation' => $this->emergency_diff_house_relation_2,
            ] : null,
        ]));

        $emergencyContacts = (!empty($sameHouse) || !empty($diffHouse)) ? [
            'same_house' => $sameHouse,
            'diff_house' => $diffHouse,
        ] : null;

        // Update or create family fields
        ApplicantFamily::updateOrCreate(
            ['applicant_profile_id' => $profile->id],
            [
                'father_name' => $this->father_name,
                'father_birth_year' => $this->father_birth_year ?: null,
                'father_last_education' => $this->father_last_education,
                'father_occupation' => $this->father_occupation,
                'father_company' => $this->father_company,
                'mother_name' => $this->mother_name,
                'mother_birth_year' => $this->mother_birth_year ?: null,
                'mother_last_education' => $this->mother_last_education,
                'mother_occupation' => $this->mother_occupation,
                'mother_company' => $this->mother_company,
                'emergency_contacts' => $emergencyContacts,
            ]
        );

        $this->dispatch('profile-updated', message: __('Data keluarga berhasil diperbarui.'), action: 'update');
    }

    public function render()
    {
        $currentYear = (int) date('Y');
        $years = range($currentYear, $currentYear - 90);

        $educationOptions = array_map(fn($item) => __($item), [
            'SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3', 'Tidak Sekolah'
        ]);

        $occupationOptions = array_map(fn($item) => __($item), [
            'PNS / ASN', 'Pegawai Swasta', 'Wiraswasta / Pengusaha', 'TNI / Polri',
            'BUMN', 'Buruh / Pekerja Harian', 'Petani / Peternak', 'Nelayan',
            'Pensiunan', 'Ibu Rumah Tangga', 'Tidak Bekerja / Lainnya'
        ]);

        return view('livewire.applicant.keluarga', [
            'years' => $years,
            'educationOptions' => $educationOptions,
            'occupationOptions' => $occupationOptions,
        ]);
    }
}

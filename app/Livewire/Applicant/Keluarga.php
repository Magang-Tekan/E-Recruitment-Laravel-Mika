<?php

namespace App\Livewire\Applicant;

use App\Models\ApplicantFamily;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Keluarga extends Component
{
    // Profile level fields
    public $child_sequence;
    public $total_siblings;
    public $marital_status;

    // Structured family members matching the 8-column table in Gambar 2
    public $family_members = [];

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

    public $applicant_phone;

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->applicantProfile) {
            $profile = $user->applicantProfile;
            $this->applicant_phone = $profile->phone ?? $user->phone ?? null;
            $this->child_sequence = $profile->child_sequence;
            $this->total_siblings = $profile->total_siblings;
            $this->marital_status = $profile->marital_status;

            $family = $profile->family;
            if ($family) {
                // Emergency contacts
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

                // Load family_members
                if (!empty($family->family_members) && is_array($family->family_members)) {
                    $this->family_members = $family->family_members;
                } else {
                    // Populate initial rows from legacy father/mother fields
                    $fatherAge = $family->father_birth_year ? (string)(date('Y') - (int)$family->father_birth_year) : '';
                    $motherAge = $family->mother_birth_year ? (string)(date('Y') - (int)$family->mother_birth_year) : '';

                    $this->family_members = [
                        $this->createRow('Ayah', $family->father_name ?? '', $fatherAge, '', $family->father_birth_year ? ($family->father_birth_year . '-01-01') : '', $family->father_last_education ?? '', $family->father_occupation ?? '', $family->father_company ?? '', false),
                        $this->createRow('Ibu', $family->mother_name ?? '', $motherAge, '', $family->mother_birth_year ? ($family->mother_birth_year . '-01-01') : '', $family->mother_last_education ?? '', $family->mother_occupation ?? '', $family->mother_company ?? '', false),
                    ];
                }
            } else {
                $this->family_members = [
                    $this->createRow('Ayah', '', '', '', '', '', '', '', false),
                    $this->createRow('Ibu', '', '', '', '', '', '', '', false),
                ];
            }
        } else {
            $this->applicant_phone = $user?->phone ?? null;
            $this->family_members = [
                $this->createRow('Ayah', '', '', '', '', '', '', '', false),
                $this->createRow('Ibu', '', '', '', '', '', '', '', false),
            ];
        }

        $this->syncFamilyStructure();
    }

    public function createRow($relation, $name = '', $age = '', $birthPlace = '', $birthDate = '', $education = '', $occupation = '', $company = '', $deletable = true)
    {
        return [
            'relation' => $relation,
            'name' => $name,
            'age' => $age,
            'birth_place' => $birthPlace,
            'birth_date' => $birthDate,
            'education' => $education,
            'occupation' => $occupation,
            'company' => $company,
            'deletable' => $deletable,
        ];
    }

    public function updatedChildSequence()
    {
        $this->syncFamilyStructure();
    }

    public function updatedTotalSiblings()
    {
        $this->syncFamilyStructure();
    }

    public function updated($propertyName, $value)
    {
        // Auto-calculate age when birth_date is entered/changed
        if (str_contains($propertyName, 'birth_date') && !empty($value)) {
            $parts = explode('.', $propertyName);
            if (count($parts) >= 3 && $parts[0] === 'family_members' && $parts[2] === 'birth_date') {
                $idx = (int)$parts[1];
                if (isset($this->family_members[$idx])) {
                    try {
                        $this->family_members[$idx]['age'] = (string) Carbon::parse($value)->age;
                    } catch (\Exception $e) {
                        // ignore invalid dates
                    }
                }
            }
        }
    }

    public function syncFamilyStructure()
    {
        $childSeq = (int) $this->child_sequence;
        $totalSib = (int) $this->total_siblings;

        // Build lookup of current entered data by relation
        $existing = [];
        $customRows = [];
        $children = [];

        foreach ($this->family_members as $m) {
            $rel = $m['relation'] ?? '';
            if (str_starts_with($rel, 'Anak')) {
                $children[] = $m;
            } elseif (in_array($rel, ['Ayah', 'Ibu', 'Suami/Istri']) || str_starts_with($rel, 'Kakak') || str_starts_with($rel, 'Adik')) {
                $existing[$rel] = $m;
            } else {
                $customRows[] = $m;
            }
        }

        $newMembers = [];

        // 1. Ayah
        $newMembers[] = $existing['Ayah'] ?? $this->createRow('Ayah', '', '', '', '', '', '', '', false);

        // 2. Ibu
        $newMembers[] = $existing['Ibu'] ?? $this->createRow('Ibu', '', '', '', '', '', '', '', false);

        // 3. Kakak (jika anak ke > 1)
        if ($childSeq > 1) {
            $kakakCount = min(15, $childSeq - 1);
            for ($i = 1; $i <= $kakakCount; $i++) {
                $rel = "Kakak $i";
                $newMembers[] = $existing[$rel] ?? $this->createRow($rel, '', '', '', '', '', '', '', false);
            }
        }

        // 4. Adik (jika total bersaudara > anak ke)
        if ($totalSib > $childSeq && $childSeq >= 1) {
            $adikCount = min(15, $totalSib - $childSeq);
            for ($i = 1; $i <= $adikCount; $i++) {
                $rel = "Adik $i";
                $newMembers[] = $existing[$rel] ?? $this->createRow($rel, '', '', '', '', '', '', '', false);
            }
        }

        // 5. Suami / Istri (jika menikah)
        if ($this->marital_status === 'menikah') {
            $rel = 'Suami/Istri';
            $newMembers[] = $existing[$rel] ?? $this->createRow($rel, '', '', '', '', '', '', '', false);
        }

        // 6. Anak (pertahankan anak yang sudah diisi / ditambahkan)
        foreach ($children as $idx => $child) {
            $child['relation'] = 'Anak ' . ($idx + 1);
            $child['deletable'] = true;
            $newMembers[] = $child;
        }

        // 7. Custom rows (jika user menambahkan saudara lain dsb.)
        foreach ($customRows as $custom) {
            $newMembers[] = $custom;
        }

        $this->family_members = $newMembers;
    }

    public function addChild()
    {
        $childCount = collect($this->family_members)->filter(fn($m) => str_starts_with($m['relation'] ?? '', 'Anak'))->count();
        $this->family_members[] = $this->createRow('Anak ' . ($childCount + 1), '', '', '', '', '', '', '', true);
    }

    public function addSibling()
    {
        $customCount = collect($this->family_members)->filter(fn($m) => str_starts_with($m['relation'] ?? '', 'Saudara Lain'))->count();
        $this->family_members[] = $this->createRow('Saudara Lain ' . ($customCount + 1), '', '', '', '', '', '', '', true);
    }

    public function removeMember($index)
    {
        if (isset($this->family_members[$index])) {
            $rel = $this->family_members[$index]['relation'] ?? '';
            // Jangan izinkan hapus Ayah atau Ibu default
            if (in_array($rel, ['Ayah', 'Ibu'])) {
                return;
            }

            unset($this->family_members[$index]);
            $this->family_members = array_values($this->family_members);

            // Re-index Anak
            $childIndex = 1;
            foreach ($this->family_members as &$member) {
                if (str_starts_with($member['relation'] ?? '', 'Anak')) {
                    $member['relation'] = 'Anak ' . $childIndex;
                    $childIndex++;
                }
            }
        }
    }

    public function getApplicantPhone(): ?string
    {
        $user = Auth::user();
        if ($user) {
            $phone = $user->applicantProfile?->phone ?? $user->phone ?? null;
            if (!empty($phone)) {
                $this->applicant_phone = $phone;
                return $phone;
            }
        }
        return $this->applicant_phone;
    }

    public function normalizePhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        $digits = preg_replace('/[^\d]/', '', (string) $phone);
        if (empty($digits)) {
            return '';
        }

        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        } elseif (!str_starts_with($digits, '0') && (str_starts_with($digits, '8') || strlen($digits) >= 9)) {
            $digits = '0' . $digits;
        }

        return $digits;
    }

    public function isApplicantOwnPhone(?string $inputPhone): bool
    {
        $applicantPhone = $this->getApplicantPhone();
        if (empty($inputPhone) || empty($applicantPhone)) {
            return false;
        }

        $normInput = $this->normalizePhone($inputPhone);
        $normApplicant = $this->normalizePhone($applicantPhone);

        if (empty($normInput) || empty($normApplicant)) {
            return false;
        }

        if ($normInput === $normApplicant) {
            return true;
        }

        $tailInput = ltrim($normInput, '0');
        $tailApplicant = ltrim($normApplicant, '0');

        if (strlen($tailInput) >= 8 && strlen($tailApplicant) >= 8 && $tailInput === $tailApplicant) {
            return true;
        }

        return false;
    }

    public function isSamePhone(?string $phone1, ?string $phone2): bool
    {
        if (empty($phone1) || empty($phone2)) {
            return false;
        }

        $norm1 = $this->normalizePhone($phone1);
        $norm2 = $this->normalizePhone($phone2);

        if (empty($norm1) || empty($norm2)) {
            return false;
        }

        if ($norm1 === $norm2) {
            return true;
        }

        $tail1 = ltrim($norm1, '0');
        $tail2 = ltrim($norm2, '0');

        if (strlen($tail1) >= 8 && strlen($tail2) >= 8 && $tail1 === $tail2) {
            return true;
        }

        return false;
    }

    public function validateEmergencyPhone(string $field, ?string $value)
    {
        $this->resetErrorBag($field);

        if (empty($value)) {
            return;
        }

        // 1. Cek apakah nomor sama dengan nomor telepon pribadi pelamar
        if ($this->isApplicantOwnPhone($value)) {
            $this->addError($field, __('Nomor kontak darurat tidak boleh sama dengan nomor telepon pribadi Anda.'));
            return;
        }

        // 2. Format dasar nomor telepon (minimal 9 digit angka)
        $clean = preg_replace('/[^\d]/', '', (string) $value);
        if (strlen($clean) > 0 && strlen($clean) < 9) {
            $this->addError($field, __('Nomor telepon minimal 9 digit angka.'));
            return;
        }

        // 3. Cek duplikasi dengan field kontak darurat lainnya
        $fields = [
            'emergency_same_house_phone_1' => __('Kontak Darurat 1 (Satu Rumah)'),
            'emergency_same_house_phone_2' => __('Kontak Darurat 2 (Satu Rumah)'),
            'emergency_diff_house_phone_1' => __('Kontak Darurat 1 (Tidak Satu Rumah)'),
            'emergency_diff_house_phone_2' => __('Kontak Darurat 2 (Tidak Satu Rumah)'),
        ];

        foreach ($fields as $otherField => $label) {
            if ($otherField !== $field && !empty($this->$otherField)) {
                if ($this->isSamePhone($value, $this->$otherField)) {
                    $this->addError($field, __('Nomor ini tidak boleh sama dengan ') . $label . '.');
                    return;
                }
            }
        }
    }

    public function updatedEmergencySameHousePhone1($value)
    {
        $this->validateEmergencyPhone('emergency_same_house_phone_1', $value);
    }

    public function updatedEmergencySameHousePhone2($value)
    {
        $this->validateEmergencyPhone('emergency_same_house_phone_2', $value);
    }

    public function updatedEmergencyDiffHousePhone1($value)
    {
        $this->validateEmergencyPhone('emergency_diff_house_phone_1', $value);
    }

    public function updatedEmergencyDiffHousePhone2($value)
    {
        $this->validateEmergencyPhone('emergency_diff_house_phone_2', $value);
    }

    public function save()
    {
        $this->validate([
            'child_sequence' => 'nullable|numeric|min:1',
            'total_siblings' => 'nullable|numeric|min:1',
            'family_members.*.name' => 'nullable|string|max:255',
            'family_members.*.age' => 'nullable|numeric|min:0|max:130',
            'family_members.*.birth_place' => 'nullable|string|max:255',
            'family_members.*.birth_date' => 'nullable|date',
            'family_members.*.education' => 'nullable|string|max:100',
            'family_members.*.occupation' => 'nullable|string|max:100',
            'family_members.*.company' => 'nullable|string|max:255',

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
        ]);

        // Validasi kontak darurat tidak boleh sama dengan nomor sendiri / duplikat
        $emergencyFields = [
            'emergency_same_house_phone_1',
            'emergency_same_house_phone_2',
            'emergency_diff_house_phone_1',
            'emergency_diff_house_phone_2',
        ];

        $hasEmergencyError = false;
        foreach ($emergencyFields as $field) {
            if (!empty($this->$field)) {
                $this->validateEmergencyPhone($field, $this->$field);
                if ($this->getErrorBag()->has($field)) {
                    $hasEmergencyError = true;
                }
            }
        }

        if ($hasEmergencyError) {
            $this->dispatch('profile-updated',
                message: __('Terdapat kesalahan pada kontak darurat. Nomor darurat tidak boleh menggunakan nomor telepon pribadi Anda.'),
                action: 'error'
            );
            return;
        }

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

        // Clean family members: keep all defined members that have either relation or some data
        $cleanFamilyMembers = array_values(array_filter($this->family_members, function ($m) {
            return !empty($m['relation']) && (!empty($m['name']) || in_array($m['relation'], ['Ayah', 'Ibu']));
        }));

        // Find Ayah & Ibu to sync legacy columns
        $ayah = collect($this->family_members)->first(fn($m) => ($m['relation'] ?? '') === 'Ayah');
        $ibu = collect($this->family_members)->first(fn($m) => ($m['relation'] ?? '') === 'Ibu');

        $fatherBirthYear = null;
        if (!empty($ayah['birth_date'])) {
            $fatherBirthYear = (int) date('Y', strtotime($ayah['birth_date']));
        } elseif (!empty($ayah['age'])) {
            $fatherBirthYear = (int) date('Y') - (int) $ayah['age'];
        }

        $motherBirthYear = null;
        if (!empty($ibu['birth_date'])) {
            $motherBirthYear = (int) date('Y', strtotime($ibu['birth_date']));
        } elseif (!empty($ibu['age'])) {
            $motherBirthYear = (int) date('Y') - (int) $ibu['age'];
        }

        ApplicantFamily::updateOrCreate(
            ['applicant_profile_id' => $profile->id],
            [
                'father_name' => $ayah['name'] ?? null,
                'father_birth_year' => $fatherBirthYear,
                'father_last_education' => $ayah['education'] ?? null,
                'father_occupation' => $ayah['occupation'] ?? null,
                'father_company' => $ayah['company'] ?? null,
                'mother_name' => $ibu['name'] ?? null,
                'mother_birth_year' => $motherBirthYear,
                'mother_last_education' => $ibu['education'] ?? null,
                'mother_occupation' => $ibu['occupation'] ?? null,
                'mother_company' => $ibu['company'] ?? null,
                'emergency_contacts' => $emergencyContacts,
                'family_members' => $cleanFamilyMembers,
            ]
        );

        $this->dispatch('profile-updated', message: __('Data keluarga berhasil diperbarui.'), action: 'update');
    }

    public function render()
    {
        $educationOptions = [
            'SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3', 'Belum Sekolah', 'Tidak Sekolah'
        ];

        $occupationOptions = [
            'PNS / ASN', 'Pegawai Swasta', 'Wiraswasta / Pengusaha', 'TNI / Polri',
            'BUMN', 'Buruh / Pekerja Harian', 'Petani / Peternak', 'Nelayan',
            'Pensiunan', 'Ibu Rumah Tangga', 'Pelajar / Mahasiswa', 'Belum / Tidak Bekerja', 'Lainnya'
        ];

        return view('livewire.applicant.keluarga', [
            'educationOptions' => $educationOptions,
            'occupationOptions' => $occupationOptions,
        ]);
    }
}

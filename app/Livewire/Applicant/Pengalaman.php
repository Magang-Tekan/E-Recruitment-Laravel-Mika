<?php

namespace App\Livewire\Applicant;

use App\Models\ApplicantJobPreference;
use App\Models\ApplicantProfile;
use App\Models\Department;
use App\Models\WorkExperience;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Pengalaman extends Component
{
    // Work Experience Modal & Form State
    public $showModal = false;
    public $isEdit = false;
    public $work_experience_id;

    public $showDeleteModal = false;
    public $deleteId = null;

    public $company_name;
    public $business_field;
    public $company_address;
    public $supervisor_name;
    public $position;
    public $initial_position;
    public $employment_type;
    public $start_date;
    public $end_date;
    public $currently_working = false;
    public $initial_salary;
    public $final_salary;
    public $benefits;
    public $description;

    // Minat Kerja (Applicant Job Preference) State
    public $interested_field_1;
    public $interested_field_2;
    public $interested_field_3;
    public $notice_period;
    public $expected_salary;
    public $is_willing_to_relocate = true;

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->applicantProfile) {
            $profile = $user->applicantProfile;
            $pref = $profile->jobPreference;
            if ($pref) {
                $this->interested_field_1 = $pref->interested_field_1;
                $this->interested_field_2 = $pref->interested_field_2;
                $this->interested_field_3 = $pref->interested_field_3;
                $this->notice_period = $pref->notice_period;
                $this->expected_salary = $pref->expected_salary ? (int) $pref->expected_salary : null;
                $this->is_willing_to_relocate = $pref->is_willing_to_relocate ? '1' : '0';
            } else {
                $this->is_willing_to_relocate = '1';
            }

            // 🔄 Auto-Sync dari Data Tambahan jika pref belum ada tapi Data Tambahan sudah diisi
            if (empty($this->expected_salary) && !empty($profile->other_answers['expected_salary'])) {
                $parsed = ApplicantProfile::parseSalaryNumber($profile->other_answers['expected_salary']);
                if ($parsed !== null && $parsed > 0) {
                    $this->expected_salary = (int) $parsed;
                }
            }
        }
    }

    #[On('salary-updated')]
    public function syncSalaryFromEvent($salary = null, $formatted = null)
    {
        if ($salary !== null && $salary > 0) {
            $this->expected_salary = (int) $salary;
        } elseif ($formatted) {
            $parsed = ApplicantProfile::parseSalaryNumber($formatted);
            if ($parsed !== null && $parsed > 0) {
                $this->expected_salary = (int) $parsed;
            }
        }
    }

    public function updatedCurrentlyWorking($value)
    {
        if ($value) {
            $this->end_date = null;
        }
    }

    protected function rules()
    {
        return [
            'company_name' => 'required|string|max:255',
            'business_field' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:1000',
            'supervisor_name' => 'nullable|string|max:255',
            'position' => 'required|string|max:255',
            'initial_position' => 'nullable|string|max:255',
            'employment_type' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => $this->currently_working ? 'nullable' : 'nullable|date|after_or_equal:start_date',
            'currently_working' => 'boolean',
            'initial_salary' => 'nullable|numeric|min:0',
            'final_salary' => 'nullable|numeric|min:0',
            'benefits' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:2000',
        ];
    }

    protected function messages()
    {
        return [
            'company_name.required' => __('Nama perusahaan / instansi wajib diisi.'),
            'position.required' => __('Posisi / jabatan akhir wajib diisi.'),
            'employment_type.required' => __('Jenis pekerjaan wajib dipilih.'),
            'start_date.required' => __('Tanggal mulai wajib diisi.'),
            'start_date.date' => __('Format tanggal mulai tidak valid.'),
            'end_date.date' => __('Format tanggal selesai tidak valid.'),
            'end_date.after_or_equal' => __('Tanggal selesai harus setelah atau sama dengan tanggal mulai.'),
        ];
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'work_experience_id',
            'company_name',
            'business_field',
            'company_address',
            'supervisor_name',
            'position',
            'initial_position',
            'employment_type',
            'start_date',
            'end_date',
            'currently_working',
            'initial_salary',
            'final_salary',
            'benefits',
            'description',
        ]);
        $this->resetErrorBag();
    }

    public function edit($id)
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        if (!$profile) return;

        $exp = WorkExperience::where('profile_id', $profile->id)->where('id', $id)->firstOrFail();

        $this->work_experience_id = $exp->id;
        $this->company_name = $exp->company_name;
        $this->business_field = $exp->business_field;
        $this->company_address = $exp->company_address;
        $this->supervisor_name = $exp->supervisor_name;
        $this->position = $exp->position;
        $this->initial_position = $exp->initial_position;
        $this->employment_type = $exp->employment_type;
        $this->start_date = $exp->start_date ? $exp->start_date->format('Y-m-d') : null;
        $this->end_date = $exp->end_date ? $exp->end_date->format('Y-m-d') : null;
        $this->currently_working = (bool) $exp->currently_working;
        $this->initial_salary = $exp->initial_salary ? (float) $exp->initial_salary : null;
        $this->final_salary = $exp->final_salary ? (float) $exp->final_salary : null;
        $this->benefits = $exp->benefits;
        $this->description = $exp->description;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $validatedData = $this->validate();

        if ($this->currently_working) {
            $validatedData['end_date'] = null;
        }

        $validatedData['initial_salary'] = $this->initial_salary ?: null;
        $validatedData['final_salary'] = $this->final_salary ?: null;

        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        if ($this->isEdit && $this->work_experience_id) {
            $exp = WorkExperience::where('profile_id', $profile->id)->where('id', $this->work_experience_id)->firstOrFail();
            $exp->update($validatedData);
            $msg = __('Pengalaman kerja berhasil diperbarui.');
            $action = 'update';
        } else {
            $validatedData['profile_id'] = $profile->id;
            WorkExperience::create($validatedData);
            $msg = __('Pengalaman kerja berhasil ditambahkan.');
            $action = 'create';
        }

        $this->closeModal();
        $this->dispatch('profile-updated', message: $msg, action: $action);
    }

    public function savePreference()
    {
        $this->validate([
            'interested_field_1' => 'nullable|string|max:255',
            'interested_field_2' => 'nullable|string|max:255',
            'interested_field_3' => 'nullable|string|max:255',
            'notice_period' => 'nullable|string|max:255',
            'expected_salary' => 'nullable|numeric|min:0',
            'is_willing_to_relocate' => 'required|boolean',
        ]);

        $user = Auth::user();
        if (!$user) return;

        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        ApplicantJobPreference::updateOrCreate(
            ['applicant_profile_id' => $profile->id],
            [
                'interested_field_1' => $this->interested_field_1,
                'interested_field_2' => $this->interested_field_2,
                'interested_field_3' => $this->interested_field_3,
                'notice_period' => $this->notice_period,
                'expected_salary' => $this->expected_salary,
                'is_willing_to_relocate' => (bool) $this->is_willing_to_relocate,
            ]
        );

        // 🔄 Auto-Sync ke Data Tambahan (other_answers)
        $otherAnswers = $profile->other_answers ?? [];
        $needsOtherUpdate = false;

        if ($this->expected_salary !== null && $this->expected_salary !== '') {
            $formattedSalary = ApplicantProfile::formatSalaryString($this->expected_salary);
            $existing = trim((string)($otherAnswers['expected_salary'] ?? ''));

            if ($existing === '' || is_numeric($existing) || preg_match('/^Rp\s*[\d\.]+\s*$/i', $existing)) {
                $otherAnswers['expected_salary'] = $formattedSalary;
                $needsOtherUpdate = true;
            } else {
                $count = 0;
                $replaced = preg_replace(
                    '/(?:rp\.?\s*)?(\d{1,3}(?:\.\d{3})+|\d{4,})/i',
                    $formattedSalary,
                    $existing,
                    1,
                    $count
                );
                if ($count > 0) {
                    $otherAnswers['expected_salary'] = $replaced;
                } else {
                    $otherAnswers['expected_salary'] = $formattedSalary . ' (' . $existing . ')';
                }
                $needsOtherUpdate = true;
            }
        }

        if ($needsOtherUpdate) {
            $profile->update(['other_answers' => $otherAnswers]);
        }

        $formattedSalary = ApplicantProfile::formatSalaryString($this->expected_salary);
        $this->dispatch('salary-updated', salary: $this->expected_salary, formatted: $formattedSalary);
        $this->dispatch('profile-updated', message: __('Minat kerja berhasil diperbarui (dan otomatis disinkronkan ke Data Tambahan)!'), action: 'update');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function delete()
    {
        if ($this->deleteId) {
            $user = Auth::user();
            $profile = ApplicantProfile::where('user_id', $user->id)->first();
            if ($profile) {
                WorkExperience::where('profile_id', $profile->id)->where('id', $this->deleteId)->delete();
            }
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->dispatch('profile-updated', message: __('Pengalaman kerja berhasil dihapus.'), action: 'delete');
    }

    public function render()
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        $experiences = $profile ? WorkExperience::where('profile_id', $profile->id)->orderBy('start_date', 'desc')->get() : collect();

        $departments = Department::select('name')->distinct()->orderBy('name')->pluck('name')->toArray();

        $employmentTypes = [
            'Full-time',
            'Part-time',
            'Kontrak',
            'Magang / Internship',
            'Freelance',
            'Paruh Waktu',
            'Lainnya',
        ];

        $noticePeriodOptions = [
            'Immediate / < 1 Bulan',
            '1 Bulan',
            '2 Bulan',
            '3 Bulan',
            '>= 4 Bulan',
        ];

        return view('livewire.applicant.pengalaman', [
            'experiences' => $experiences,
            'employmentTypes' => $employmentTypes,
            'departments' => $departments,
            'noticePeriodOptions' => $noticePeriodOptions,
        ]);
    }
}

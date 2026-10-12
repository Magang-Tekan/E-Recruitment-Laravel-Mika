<?php

namespace App\Livewire\Applicant;

use App\Models\ApplicantJobPreference;
use App\Models\ApplicantProfile;
use App\Models\Department;
use App\Models\WorkExperience;
use Illuminate\Support\Facades\Auth;
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
    public $position;
    public $employment_type;
    public $start_date;
    public $end_date;
    public $currently_working = false;
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
            $pref = $user->applicantProfile->jobPreference;
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
            'position' => 'required|string|max:255',
            'employment_type' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => $this->currently_working ? 'nullable' : 'nullable|date|after_or_equal:start_date',
            'currently_working' => 'boolean',
            'description' => 'nullable|string|max:2000',
        ];
    }

    protected function messages()
    {
        return [
            'company_name.required' => __('Nama perusahaan / instansi wajib diisi.'),
            'position.required' => __('Posisi / jabatan wajib diisi.'),
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
            'position',
            'employment_type',
            'start_date',
            'end_date',
            'currently_working',
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
        $this->position = $exp->position;
        $this->employment_type = $exp->employment_type;
        $this->start_date = $exp->start_date ? $exp->start_date->format('Y-m-d') : null;
        $this->end_date = $exp->end_date ? $exp->end_date->format('Y-m-d') : null;
        $this->currently_working = (bool) $exp->currently_working;
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

        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        if ($this->isEdit && $this->work_experience_id) {
            $exp = WorkExperience::where('profile_id', $profile->id)->where('id', $this->work_experience_id)->firstOrFail();
            $exp->update($validatedData);
            session()->flash('message', __('Pengalaman kerja berhasil diperbarui.'));
        } else {
            $validatedData['profile_id'] = $profile->id;
            WorkExperience::create($validatedData);
            session()->flash('message', __('Pengalaman kerja berhasil ditambahkan.'));
        }

        $this->closeModal();
        return redirect(route('profile', ['tab' => 'pengalaman']));
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

        $this->dispatch('profile-updated');
        session()->flash('preference_message', __('Minat kerja berhasil diperbarui!'));
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
                session()->flash('message', __('Pengalaman kerja berhasil dihapus.'));
            }
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;

        return redirect(route('profile', ['tab' => 'pengalaman']));
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

<?php

namespace App\Livewire\Applicant;

use App\Models\ApplicantProfile;
use App\Models\Degree;
use App\Models\Education;
use App\Models\Major;
use App\Models\Skill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Pendidikan extends Component
{
    use WithFileUploads;

    // --- FORM MODAL EDUCATION STATE ---
    public $showModal = false;
    public $isEdit = false;
    public $education_id;

    public $showDeleteModal = false;
    public $deleteId = null;

    public $degree_id;
    public $degree;
    public $major_id;
    public $major;
    public $is_custom_major = false;
    public $custom_major = '';
    public $school_name;
    public $study_program;
    public $start_year;
    public $end_year;
    public $is_ongoing = false;
    public $gpa;
    public $description;

    // --- FORM MODAL SKILL STATE ---
    public $showSkillModal = false;
    public $isSkillEdit = false;
    public $skill_id = null;
    public $skill_name = '';
    public $skill_certificate = null;
    public $existing_skill_certificate = null;

    public $showDeleteSkillModal = false;
    public $deleteSkillId = null;

    public function updatedIsOngoing($value)
    {
        if ($value) {
            $this->end_year = null;
        }
    }

    public function isSchoolDegree()
    {
        if ($this->degree_id) {
            $deg = Degree::find($this->degree_id);
            if ($deg) {
                $name = strtoupper($deg->name);
                return str_contains($name, 'SMA') || str_contains($name, 'SMK') || str_contains($name, 'SLTA') || str_contains($name, 'SMP') || str_contains($name, 'SD');
            }
        }
        if ($this->degree) {
            $name = strtoupper($this->degree);
            return str_contains($name, 'SMA') || str_contains($name, 'SMK') || str_contains($name, 'SLTA') || str_contains($name, 'SMP') || str_contains($name, 'SD');
        }
        return false;
    }

    protected function rules()
    {
        $isSchool = $this->isSchoolDegree();
        $gpaRule = $isSchool ? 'nullable|numeric|between:0,100.00' : 'nullable|numeric|between:0,4.00';

        return [
            'school_name' => 'required|string|max:255',
            'degree_id' => 'nullable|exists:degrees,id',
            'degree' => 'nullable|string|max:100',
            'major_id' => $this->is_custom_major ? 'nullable' : 'nullable|exists:majors,id',
            'major' => 'nullable|string|max:100',
            'custom_major' => $this->is_custom_major ? 'required|string|max:100' : 'nullable|string|max:100',
            'study_program' => 'nullable|string|max:255',
            'start_year' => 'required|integer|digits:4|min:1950|max:' . (date('Y') + 5),
            'end_year' => $this->is_ongoing ? 'nullable' : 'nullable|integer|digits:4|gte:start_year|max:' . (date('Y') + 10),
            'gpa' => $gpaRule,
            'description' => 'nullable|string|max:1000',
        ];
    }

    protected function messages()
    {
        $isSchool = $this->isSchoolDegree();
        return [
            'school_name.required' => 'Nama sekolah / perguruan tinggi wajib diisi.',
            'custom_major.required' => 'Nama jurusan wajib diisi jika memilih lainnya.',
            'start_year.required' => 'Tahun mulai wajib diisi.',
            'start_year.digits' => 'Tahun mulai harus 4 digit angka.',
            'end_year.gte' => 'Tahun selesai harus lebih besar atau sama dengan tahun mulai.',
            'gpa.between' => $isSchool 
                ? 'Nilai Rata-rata harus di antara 0 sampai 100.' 
                : 'IPK harus di antara 0.00 sampai 4.00.',
        ];
    }

    public function updatedMajorId($value)
    {
        if ($value === 'other') {
            $this->is_custom_major = true;
        } else {
            $this->is_custom_major = false;
            $this->custom_major = '';
        }
    }

    public function setCustomMajorMode($isCustom)
    {
        $this->is_custom_major = $isCustom;
        if ($isCustom) {
            $this->major_id = 'other';
        } else {
            $this->major_id = null;
            $this->custom_major = '';
        }
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
            'education_id',
            'degree_id',
            'degree',
            'major_id',
            'major',
            'is_custom_major',
            'custom_major',
            'school_name',
            'study_program',
            'start_year',
            'end_year',
            'is_ongoing',
            'gpa',
            'description',
        ]);
        $this->resetErrorBag();
    }

    public function edit($id)
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        if (!$profile) return;

        $education = Education::where('profile_id', $profile->id)->where('id', $id)->firstOrFail();

        $this->education_id = $education->id;
        $this->degree_id = $education->degree_id;
        $this->degree = $education->degree;
        $this->major_id = $education->major_id;
        $this->major = $education->major;
        
        // Cek jika jurusan menggunakan custom text ("Lainnya")
        if (empty($education->major_id) && !empty($education->major) && $education->major !== '-') {
            $this->is_custom_major = true;
            $this->custom_major = $education->major;
            $this->major_id = 'other';
        } else {
            $this->is_custom_major = false;
            $this->custom_major = '';
        }

        $this->school_name = $education->school_name;
        $this->study_program = $education->study_program;
        $this->start_year = $education->start_year;
        $this->end_year = $education->end_year;
        $this->is_ongoing = empty($education->end_year);
        $this->gpa = $education->gpa;
        $this->description = $education->description;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $validatedData = $this->validate();

        if ($this->is_ongoing) {
            $validatedData['end_year'] = null;
        }

        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        if (!empty($this->degree_id)) {
            $degreeObj = Degree::find($this->degree_id);
            $validatedData['degree'] = $degreeObj ? $degreeObj->name : ($this->degree ?? '-');
        } else {
            $validatedData['degree'] = !empty($this->degree) ? $this->degree : '-';
        }

        if ($this->isSchoolDegree()) {
            $validatedData['major'] = '-';
            $validatedData['major_id'] = null;
        } else {
            if ($this->is_custom_major || $this->major_id === 'other') {
                $validatedData['major_id'] = null;
                $validatedData['major'] = trim($this->custom_major);
            } elseif (!empty($this->major_id)) {
                $majorObj = Major::find($this->major_id);
                $validatedData['major'] = $majorObj ? $majorObj->name : ($this->major ?? '-');
            } else {
                $validatedData['major'] = !empty($this->major) ? $this->major : '-';
            }
        }
        unset($validatedData['custom_major']);

        if ($this->isEdit && $this->education_id) {
            $education = Education::where('profile_id', $profile->id)->where('id', $this->education_id)->firstOrFail();
            $education->update($validatedData);
            session()->flash('message', 'Riwayat pendidikan berhasil diperbarui.');
        } else {
            $validatedData['profile_id'] = $profile->id;
            Education::create($validatedData);
            session()->flash('message', 'Riwayat pendidikan berhasil ditambahkan.');
        }

        $this->closeModal();
        $this->dispatch('profile-updated');
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
                Education::where('profile_id', $profile->id)->where('id', $this->deleteId)->delete();
                session()->flash('message', 'Riwayat pendidikan berhasil dihapus.');
            }
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->dispatch('profile-updated');
    }

    // --- SKILL CRUD METHODS ---
    public function openSkillModal()
    {
        $this->resetSkillForm();
        $this->isSkillEdit = false;
        $this->showSkillModal = true;
    }

    public function closeSkillModal()
    {
        $this->showSkillModal = false;
        $this->resetSkillForm();
    }

    public function resetSkillForm()
    {
        $this->reset([
            'skill_id',
            'skill_name',
            'skill_certificate',
            'existing_skill_certificate',
        ]);
        $this->resetErrorBag();
    }

    public function editSkill($id)
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        if (!$profile) return;

        $skill = Skill::where('profile_id', $profile->id)->where('id', $id)->firstOrFail();

        $this->skill_id = $skill->id;
        $this->skill_name = $skill->name;
        $this->existing_skill_certificate = $skill->certificate_path;

        $this->isSkillEdit = true;
        $this->showSkillModal = true;
    }

    public function saveSkill()
    {
        $this->validate([
            'skill_name' => 'required|string|max:255',
            'skill_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'skill_name.required' => 'Nama keahlian / skill wajib diisi.',
            'skill_certificate.mimes' => 'Format file sertifikat/bukti harus PDF, JPG, JPEG, atau PNG.',
            'skill_certificate.max' => 'Ukuran file tidak boleh melebihi 2MB.',
        ]);

        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        $certPath = $this->existing_skill_certificate;
        if ($this->skill_certificate) {
            if ($certPath && Storage::disk('public')->exists($certPath)) {
                Storage::disk('public')->delete($certPath);
            }
            $certPath = $this->skill_certificate->store('certificates/skills', 'public');
        }

        if ($this->isSkillEdit && $this->skill_id) {
            $skill = Skill::where('profile_id', $profile->id)->where('id', $this->skill_id)->firstOrFail();
            $skill->update([
                'name' => $this->skill_name,
                'certificate_path' => $certPath,
            ]);
            session()->flash('message', 'Skill / Keahlian berhasil diperbarui.');
        } else {
            Skill::create([
                'profile_id' => $profile->id,
                'name' => $this->skill_name,
                'certificate_path' => $certPath,
            ]);
            session()->flash('message', 'Skill / Keahlian berhasil ditambahkan.');
        }

        $this->closeSkillModal();
        $this->dispatch('profile-updated');
    }

    public function confirmDeleteSkill($id)
    {
        $this->deleteSkillId = $id;
        $this->showDeleteSkillModal = true;
    }

    public function cancelDeleteSkill()
    {
        $this->showDeleteSkillModal = false;
        $this->deleteSkillId = null;
    }

    public function deleteSkill()
    {
        if ($this->deleteSkillId) {
            $user = Auth::user();
            $profile = ApplicantProfile::where('user_id', $user->id)->first();
            if ($profile) {
                $skill = Skill::where('profile_id', $profile->id)->where('id', $this->deleteSkillId)->first();
                if ($skill) {
                    if ($skill->certificate_path && Storage::disk('public')->exists($skill->certificate_path)) {
                        Storage::disk('public')->delete($skill->certificate_path);
                    }
                    $skill->delete();
                    session()->flash('message', 'Skill / Keahlian berhasil dihapus.');
                }
            }
        }

        $this->showDeleteSkillModal = false;
        $this->deleteSkillId = null;
        $this->dispatch('profile-updated');
    }

    public function render()
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        $educations = $profile ? Education::where('profile_id', $profile->id)->orderBy('start_year', 'desc')->get() : collect();
        $skills = $profile ? Skill::where('profile_id', $profile->id)->get() : collect();

        $degrees = Cache::remember('master_degrees_ordered', 86400, function () {
            return Degree::orderBy('rank', 'asc')->get();
        });
        $majors = Cache::remember('master_majors_ordered', 86400, function () {
            return Major::orderBy('name', 'asc')->get();
        });

        return view('livewire.applicant.pendidikan', [
            'educations' => $educations,
            'skills' => $skills,
            'degrees' => $degrees,
            'majors' => $majors,
        ]);
    }
}

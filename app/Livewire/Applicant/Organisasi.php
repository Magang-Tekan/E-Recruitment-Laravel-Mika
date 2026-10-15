<?php

namespace App\Livewire\Applicant;

use App\Models\ApplicantProfile;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Organisasi extends Component
{
    public $showModal = false;
    public $isEdit = false;
    public $organization_id;

    public $showDeleteModal = false;
    public $deleteId = null;

    public $name;
    public $position;
    public $description;
    public $is_active = false;
    public $start_month;
    public $start_year;
    public $end_month;
    public $end_year;

    public function updatedIsActive($value)
    {
        if ($value) {
            $this->end_month = null;
            $this->end_year = null;
        }
    }

    protected function rules()
    {
        $currentYear = (int) date('Y');

        return [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'start_month' => 'required|string',
            'start_year' => 'required|integer|digits:4|min:1950|max:' . ($currentYear + 5),
            'is_active' => 'boolean',
            'end_month' => $this->is_active ? 'nullable' : 'nullable|string',
            'end_year' => $this->is_active ? 'nullable' : 'nullable|integer|digits:4|gte:start_year|max:' . ($currentYear + 10),
            'description' => 'nullable|string|max:2000',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => __('Nama organisasi wajib diisi.'),
            'position.required' => __('Jabatan / posisi wajib diisi.'),
            'start_month.required' => __('Bulan mulai wajib dipilih.'),
            'start_year.required' => __('Tahun mulai wajib diisi.'),
            'start_year.digits' => __('Tahun mulai harus 4 digit angka.'),
            'end_year.gte' => __('Tahun selesai harus lebih besar atau sama dengan tahun mulai.'),
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
            'organization_id',
            'name',
            'position',
            'description',
            'is_active',
            'start_month',
            'start_year',
            'end_month',
            'end_year',
        ]);
        $this->resetErrorBag();
    }

    public function edit($id)
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        if (!$profile) return;

        $org = Organization::where('profile_id', $profile->id)->where('id', $id)->firstOrFail();

        $this->organization_id = $org->id;
        $this->name = $org->name;
        $this->position = $org->position;
        $this->description = $org->description;
        $this->is_active = (bool) $org->is_active;
        $this->start_month = $org->start_month;
        $this->start_year = $org->start_year;
        $this->end_month = $org->end_month;
        $this->end_year = $org->end_year;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function save()
    {
        $validatedData = $this->validate();

        if ($this->is_active) {
            $validatedData['end_month'] = null;
            $validatedData['end_year'] = null;
        }

        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        if ($this->isEdit && $this->organization_id) {
            $org = Organization::where('profile_id', $profile->id)->where('id', $this->organization_id)->firstOrFail();
            $org->update($validatedData);
            $msg = __('Riwayat organisasi berhasil diperbarui.');
            $action = 'update';
        } else {
            $validatedData['profile_id'] = $profile->id;
            Organization::create($validatedData);
            $msg = __('Riwayat organisasi berhasil ditambahkan.');
            $action = 'create';
        }

        $this->closeModal();
        $this->dispatch('profile-updated', message: $msg, action: $action);
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
                Organization::where('profile_id', $profile->id)->where('id', $this->deleteId)->delete();
            }
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;

        $this->dispatch('profile-updated', message: __('Riwayat organisasi berhasil dihapus.'), action: 'delete');
    }

    public function render()
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        $organizations = $profile ? Organization::where('profile_id', $profile->id)->orderBy('start_year', 'desc')->get() : collect();

        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('livewire.applicant.organisasi', [
            'organizations' => $organizations,
            'months' => $months,
        ]);
    }
}

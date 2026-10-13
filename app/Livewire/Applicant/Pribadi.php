<?php

namespace App\Livewire\Applicant;

use App\Models\ApplicantProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Pribadi extends Component
{
    use WithFileUploads;

    public $nik;

    public $full_name;

    public $gender;

    public $birth_place;

    public $birth_date;

    public $phone;

    public $address;

    public $city;

    public $province;

    public $npwp;

    public $religion;

    public $postal_code;

    public $blood_type;

    public $about_me;

    public $photo;

    public $cropped_photo_base64;

    public $current_photo_url;

    public $cv_file;

    public $current_cv_path;

    public $current_cv_url;

    public function mount()
    {
        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $user->name,
                'phone' => $user->phone ?? null,
            ]
        );

        $this->nik = $profile->nik;
        $this->full_name = $profile->full_name ?? $user->name;
        $this->gender = $profile->gender;
        $this->birth_place = $profile->birth_place;
        $this->birth_date = $profile->birth_date;
        $this->phone = $profile->phone;
        $this->address = $profile->address;
        $this->city = $profile->city;
        $this->province = $profile->province;
        $this->npwp = $profile->npwp;
        $this->religion = $profile->religion;
        $this->postal_code = $profile->postal_code;
        $this->blood_type = $profile->blood_type;
        $this->about_me = $profile->about_me;
        $photoUrl = $profile->photo_url;
        $this->current_photo_url = null;
        if ($photoUrl) {
            $this->current_photo_url = \Illuminate\Support\Str::startsWith($photoUrl, ['http://', 'https://'])
                ? $photoUrl
                : ($photoUrl . (str_contains($photoUrl, '?') ? '&v=' : '?v=') . time());
        }
        $this->current_cv_path = $profile->cv_file_path;
        $this->current_cv_url = $profile->cv_file_path ? asset('storage/'.$profile->cv_file_path) : null;
    }

    protected function rules()
    {
        return [
            'nik' => 'nullable|digits:16',
            'full_name' => 'required|string|max:255',
            'gender' => 'nullable|string|in:Laki-laki,Perempuan',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'npwp' => 'nullable|string|max:30',
            'religion' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:10',
            'blood_type' => 'nullable|string|in:A,B,AB,O',
            'about_me' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:5120',
            'cv_file' => 'nullable|file|max:10240',
        ];
    }

    protected function messages()
    {
        return [
            'nik.digits' => __('NIK harus terdiri dari 16 digit angka.'),
            'blood_type.in' => __('Pilihan golongan darah tidak valid.'),
        ];
    }

    public function updatedCvFile()
    {
        $this->validate([
            'cv_file' => 'required|file|max:10240',
        ], [
            'cv_file.required' => __('Silakan pilih file CV.'),
            'cv_file.file' => __('File CV tidak valid.'),
            'cv_file.max' => __('Ukuran file CV maksimal 10MB.'),
        ]);

        $extension = strtolower($this->cv_file->getClientOriginalExtension());
        if (! in_array($extension, ['pdf', 'doc', 'docx'])) {
            $this->addError('cv_file', __('Format file CV harus berupa PDF, DOC, atau DOCX.'));
            $this->cv_file = null;
            return;
        }

        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        if ($profile->cv_file_path && Storage::disk('public')->exists($profile->cv_file_path)) {
            Storage::disk('public')->delete($profile->cv_file_path);
        }

        $cvPath = $this->cv_file->store('applicant-cvs', 'public');
        $profile->update(['cv_file_path' => $cvPath]);

        $this->current_cv_path = $cvPath;
        $this->current_cv_url = asset('storage/' . $cvPath) . '?v=' . time();
        $this->cv_file = null;

        session()->flash('message', __('File CV berhasil diunggah.'));
        $this->dispatch('profile-updated', name: $this->full_name);
    }

    public function save()
    {
        $validatedData = $this->validate();

        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        if ($this->cropped_photo_base64) {
            if ($profile->photo && !str_starts_with($profile->photo, 'http') && Storage::disk('public')->exists($profile->photo)) {
                Storage::disk('public')->delete($profile->photo);
            }

            // Decode base64 image from Cropper.js
            $imageParts = explode(';base64,', $this->cropped_photo_base64);
            $imageTypeAux = explode('image/', $imageParts[0]);
            $imageType = isset($imageTypeAux[1]) ? strtolower($imageTypeAux[1]) : 'jpeg';
            if ($imageType === 'jpeg') {
                $imageType = 'jpg';
            }
            $imageBase64 = base64_decode($imageParts[1]);

            $fileName = 'applicant-photos/'.uniqid().'.'.$imageType;
            Storage::disk('public')->put($fileName, $imageBase64);

            $validatedData['photo'] = $fileName;
            $this->current_photo_url = asset('storage/'.$fileName).'?v='.time();
            $this->photo = null;
            $this->cropped_photo_base64 = null;
        } elseif ($this->photo) {
            if ($profile->photo && !str_starts_with($profile->photo, 'http') && Storage::disk('public')->exists($profile->photo)) {
                Storage::disk('public')->delete($profile->photo);
            }
            $photoPath = $this->photo->store('applicant-photos', 'public');
            $validatedData['photo'] = $photoPath;
            $this->current_photo_url = asset('storage/'.$photoPath).'?v='.time();
            $this->photo = null;
        } else {
            unset($validatedData['photo']);
        }

        if ($this->cv_file) {
            $extension = strtolower($this->cv_file->getClientOriginalExtension());
            if (in_array($extension, ['pdf', 'doc', 'docx'])) {
                if ($profile->cv_file_path && Storage::disk('public')->exists($profile->cv_file_path)) {
                    Storage::disk('public')->delete($profile->cv_file_path);
                }
                $cvPath = $this->cv_file->store('applicant-cvs', 'public');
                $validatedData['cv_file_path'] = $cvPath;
                $this->current_cv_path = $cvPath;
                $this->current_cv_url = asset('storage/' . $cvPath) . '?v=' . time();
                $this->cv_file = null;
            }
        }
        unset($validatedData['cv_file']);

        $profile->update($validatedData);

        $userUpdates = [];
        if ($user->name !== $this->full_name) {
            $userUpdates['name'] = $this->full_name;
        }
        if (! empty($this->nik) && $user->nik !== $this->nik) {
            $userUpdates['nik'] = $this->nik;
        }
        if (! empty($validatedData['photo'])) {
            $userUpdates['avatar'] = $validatedData['photo'];
        }
        if (! empty($userUpdates)) {
            $user->update($userUpdates);
        }

        session()->flash('message', __('Data pribadi berhasil diperbarui.'));
        $this->dispatch('profile-updated', name: $this->full_name, photo: $this->current_photo_url);
    }

    public function deleteCv()
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        if ($profile && $profile->cv_file_path) {
            if (Storage::disk('public')->exists($profile->cv_file_path)) {
                Storage::disk('public')->delete($profile->cv_file_path);
            }
            $profile->update(['cv_file_path' => null]);
            $this->current_cv_path = null;
            $this->current_cv_url = null;
            session()->flash('message', __('File CV berhasil dihapus.'));
        }
    }

    public function render()
    {
        return view('livewire.applicant.pribadi');
    }
}

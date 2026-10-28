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

    public $ktp_address;

    public $is_same_address = false;

    public $address;

    public $city;

    public $province;

    public $npwp;

    public $religion;

    public $ethnicity;

    public $nationality;

    public $marital_status;

    public $marriage_date;

    public $driving_licenses = [];

    public $daily_transportation;

    public $work_distance_km;

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
        $this->birth_date = $profile->birth_date ? \Carbon\Carbon::parse($profile->birth_date)->format('Y-m-d') : null;
        $this->phone = $profile->phone;
        $this->ktp_address = $profile->ktp_address;
        $this->address = $profile->address;
        $this->city = $profile->city;
        $this->province = $profile->province;
        $this->npwp = $profile->npwp;
        $this->religion = $profile->religion;
        $this->ethnicity = $profile->ethnicity;
        $this->nationality = $profile->nationality;
        $this->marital_status = $profile->marital_status;
        $this->marriage_date = $profile->marriage_date ? \Carbon\Carbon::parse($profile->marriage_date)->format('Y-m-d') : null;

        $licenses = is_array($profile->driving_licenses) ? $profile->driving_licenses : [];
        while (count($licenses) < 2) {
            $licenses[] = ['type' => '', 'number' => ''];
        }
        $this->driving_licenses = $licenses;

        $this->daily_transportation = $profile->daily_transportation;
        $this->work_distance_km = $profile->work_distance_km;
        $this->postal_code = $profile->postal_code;
        $this->blood_type = $profile->blood_type;
        $this->about_me = $profile->about_me;

        if (!empty($this->ktp_address) && !empty($this->address) && trim($this->ktp_address) === trim($this->address)) {
            $this->is_same_address = true;
        }

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

    public function updatedIsSameAddress($value)
    {
        if ($value && !empty($this->ktp_address)) {
            $this->address = $this->ktp_address;
        }
    }

    public function updatedKtpAddress($value)
    {
        if ($this->is_same_address) {
            $this->address = $value;
        }
    }

    public function addDrivingLicense()
    {
        $this->driving_licenses[] = ['type' => '', 'number' => ''];
    }

    public function removeDrivingLicense($index)
    {
        if (isset($this->driving_licenses[$index])) {
            unset($this->driving_licenses[$index]);
            $this->driving_licenses = array_values($this->driving_licenses);
        }
        while (count($this->driving_licenses) < 2) {
            $this->driving_licenses[] = ['type' => '', 'number' => ''];
        }
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
            'ktp_address' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'npwp' => 'nullable|string|max:30',
            'religion' => 'nullable|string|max:50',
            'ethnicity' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|in:lajang,menikah,janda,duda,bercerai',
            'marriage_date' => 'nullable|date',
            'driving_licenses' => 'nullable|array',
            'driving_licenses.*.type' => 'nullable|string|max:20',
            'driving_licenses.*.number' => 'nullable|string|max:50',
            'daily_transportation' => 'nullable|string|max:100',
            'work_distance_km' => 'nullable|numeric|min:0|max:999.9',
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
            'work_distance_km.numeric' => __('Jarak kerja harus berupa angka.'),
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

        $this->dispatch('profile-updated', name: $this->full_name, message: __('File CV berhasil diunggah.'), action: 'create');
    }

    public function save()
    {
        if ($this->nik) {
            $this->nik = preg_replace('/[^0-9]/', '', $this->nik);
        }

        $validatedData = $this->validate();

        // Normalisasi birth_date agar berupa string Y-m-d atau null jika kosong
        if (!empty($this->birth_date)) {
            $validatedData['birth_date'] = \Carbon\Carbon::parse($this->birth_date)->format('Y-m-d');
            $this->birth_date = $validatedData['birth_date'];
        } else {
            $validatedData['birth_date'] = null;
            $this->birth_date = null;
        }

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

        // Clean & filter SIM entries
        $cleanLicenses = [];
        if (is_array($this->driving_licenses)) {
            foreach ($this->driving_licenses as $lic) {
                $type = trim($lic['type'] ?? '');
                $number = trim($lic['number'] ?? '');
                if ($type !== '' || $number !== '') {
                    $cleanLicenses[] = [
                        'type' => $type,
                        'number' => $number,
                    ];
                }
            }
        }
        $validatedData['driving_licenses'] = !empty($cleanLicenses) ? $cleanLicenses : null;

        // Normalisasi marriage_date
        if (($this->marital_status ?? '') === 'menikah' && !empty($this->marriage_date)) {
            $validatedData['marriage_date'] = \Carbon\Carbon::parse($this->marriage_date)->format('Y-m-d');
            $this->marriage_date = $validatedData['marriage_date'];
        } else {
            $validatedData['marriage_date'] = null;
            $this->marriage_date = null;
        }

        // Normalisasi work_distance_km
        if ($this->work_distance_km === '' || $this->work_distance_km === null) {
            $validatedData['work_distance_km'] = null;
            $this->work_distance_km = null;
        } else {
            $validatedData['work_distance_km'] = (float) $this->work_distance_km;
        }

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

        $this->dispatch('profile-updated', name: $this->full_name, photo: $this->current_photo_url, message: __('Data pribadi berhasil diperbarui.'), action: 'update');
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
            $this->dispatch('profile-updated', message: __('File CV berhasil dihapus.'), action: 'delete');
        }
    }

    public function render()
    {
        return view('livewire.applicant.pribadi');
    }
}

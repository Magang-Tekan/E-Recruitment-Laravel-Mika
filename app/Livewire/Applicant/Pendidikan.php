<?php

namespace App\Livewire\Applicant;

use App\Models\ApplicantProfile;
use App\Models\Certification;
use App\Models\Degree;
use App\Models\Education;
use App\Models\Language;
use App\Models\Major;
use App\Models\Skill;
use App\Models\Training;
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
    public $school_suggestion = null;
    public $study_program;
    public $start_year;
    public $end_year;
    public $is_ongoing = false;
    public $gpa;
    public $description;
    public $certificate_file = null;
    public $existing_certificate_path = null;

    // --- FORM MODAL SKILL STATE ---
    public $showSkillModal = false;
    public $isSkillEdit = false;
    public $skill_id = null;
    public $skill_name = '';
    public $skill_proficiency = 'baik';
    public $skill_description = '';
    public $skill_certificate = null;
    public $existing_skill_certificate = null;

    public $showDeleteSkillModal = false;
    public $deleteSkillId = null;

    // --- FORM MODAL SERTIFIKASI, PELATIHAN, BAHASA STATE ---
    public $showExtraModal = false;
    public $extraModalType = 'certification'; // 'certification', 'training', 'language'
    public $isExtraEdit = false;
    public $extraItemId = null;
    public $extraName = '';
    public $extraCertificate = null;
    public $existingExtraCertificate = null;

    // Language specific fields
    public $extraOral = 'baik';
    public $extraWritten = 'baik';
    public $extraConversation = 'baik';

    // --- DELETE EXTRA MODAL STATE ---
    public $showDeleteExtraModal = false;
    public $deleteExtraId = null;
    public $deleteExtraType = null; // 'certification', 'training', 'language'

    public function updatedIsOngoing($value)
    {
        if ($value) {
            $this->end_year = null;
            $this->certificate_file = null;
            $this->resetErrorBag('certificate_file');
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

    public static function getUniversityAbbreviationDictionary(): array
    {
        return [
            // Semarang & Jawa Tengah
            'polines' => 'Politeknik Negeri Semarang',
            'poline' => 'Politeknik Negeri Semarang',
            'undip' => 'Universitas Diponegoro',
            'unwahas' => 'Universitas Wahid Hasyim',
            'udinus' => 'Universitas Dian Nuswantoro',
            'upgris' => 'Universitas PGRI Semarang',
            'unnes' => 'Universitas Negeri Semarang',
            'unissula' => 'Universitas Islam Sultan Agung',
            'usm' => 'Universitas Semarang',
            'unimus' => 'Universitas Muhammadiyah Semarang',
            'unika' => 'Universitas Katolik Soegijapranata',
            'scu' => 'Universitas Katolik Soegijapranata',
            'soegijapranata' => 'Universitas Katolik Soegijapranata',
            'polimarin' => 'Politeknik Maritim Negeri Indonesia',
            'stikes telogorejo' => 'STIKES Telogorejo Semarang',
            'akpelni' => 'Politeknik Bumi Akpelni',
            'pip semarang' => 'Politeknik Ilmu Pelayaran Semarang',
            'stie bank bpd' => 'STIE Bank BPD Jateng',
            'uin walisongo' => 'UIN Walisongo Semarang',
            'walisongo' => 'UIN Walisongo Semarang',
            'uns' => 'Universitas Sebelas Maret',
            'ums' => 'Universitas Muhammadiyah Surakarta',
            'uksw' => 'Universitas Kristen Satya Wacana',
            'satya wacana' => 'Universitas Kristen Satya Wacana',
            'unsoed' => 'Universitas Jenderal Soedirman',
            'untidar' => 'Universitas Tidar',
            'iain kudus' => 'IAIN Kudus',
            'iain salatiga' => 'UIN Salatiga',
            'uin salatiga' => 'UIN Salatiga',
            'iain pekalongan' => 'UIN K.H. Abdurrahman Wahid Pekalongan',
            'iain purwokerto' => 'UIN Prof. K.H. Saifuddin Zuhri Purwokerto',
            'poltekkes semarang' => 'Poltekkes Kemenkes Semarang',
            'poltekkes surakarta' => 'Poltekkes Kemenkes Surakarta',
            'isi surakarta' => 'Institut Seni Indonesia Surakarta',

            // DI Yogyakarta
            'ugm' => 'Universitas Gadjah Mada',
            'uny' => 'Universitas Negeri Yogyakarta',
            'umy' => 'Universitas Muhammadiyah Yogyakarta',
            'uad' => 'Universitas Ahmad Dahlan',
            'uajy' => 'Universitas Atma Jaya Yogyakarta',
            'atmajaya jogja' => 'Universitas Atma Jaya Yogyakarta',
            'uii' => 'Universitas Islam Indonesia',
            'upn jogja' => 'UPN "Veteran" Yogyakarta',
            'upn yk' => 'UPN "Veteran" Yogyakarta',
            'uin suka' => 'UIN Sunan Kalijaga Yogyakarta',
            'isi jogja' => 'Institut Seni Indonesia Yogyakarta',
            'sanata dharma' => 'Universitas Sanata Dharma',
            'usd' => 'Universitas Sanata Dharma',
            'amikom' => 'Universitas AMIKOM Yogyakarta',
            'ukdw' => 'Universitas Kristen Duta Wacana',
            'sttnas' => 'Institut Teknologi Nasional Yogyakarta',

            // Jawa Barat, Banten & Jakarta
            'ui' => 'Universitas Indonesia',
            'itb' => 'Institut Teknologi Bandung',
            'ipb' => 'IPB University (Institut Pertanian Bogor)',
            'unpad' => 'Universitas Padjadjaran',
            'upi' => 'Universitas Pendidikan Indonesia',
            'polban' => 'Politeknik Negeri Bandung',
            'polman bandung' => 'Politeknik Manufaktur Bandung',
            'telkom' => 'Telkom University',
            'tel-u' => 'Telkom University',
            'itenas' => 'Institut Teknologi Nasional Bandung',
            'unpar' => 'Universitas Katolik Parahyangan',
            'maranatha' => 'Universitas Kristen Maranatha',
            'uin sgd' => 'UIN Sunan Gunung Djati Bandung',
            'binus' => 'Universitas Bina Nusantara',
            'trisakti' => 'Universitas Trisakti',
            'untar' => 'Universitas Tarumanagara',
            'tarumanagara' => 'Universitas Tarumanagara',
            'mercubuana' => 'Universitas Mercu Buana',
            'umb' => 'Universitas Mercu Buana',
            'gunadarma' => 'Universitas Gunadarma',
            'gundar' => 'Universitas Gunadarma',
            'esa unggul' => 'Universitas Esa Unggul',
            'paramadina' => 'Universitas Paramadina',
            'atmajaya' => 'Universitas Katolik Indonesia Atma Jaya',
            'unj' => 'Universitas Negeri Jakarta',
            'pnj' => 'Politeknik Negeri Jakarta',
            'upn jakarta' => 'UPN "Veteran" Jakarta',
            'uin jakarta' => 'UIN Syarif Hidayatullah Jakarta',
            'untirta' => 'Universitas Sultan Ageng Tirtayasa',
            'uhamka' => 'Universitas Muhammadiyah Prof. DR. HAMKA',
            'unas' => 'Universitas Nasional',
            'stis' => 'Politeknik Statistika STIS',
            'stan' => 'Politeknik Keuangan Negara STAN',
            'pkn stan' => 'Politeknik Keuangan Negara STAN',

            // Jawa Timur
            'its' => 'Institut Teknologi Sepuluh Nopember',
            'unair' => 'Universitas Airlangga',
            'ub' => 'Universitas Brawijaya',
            'unbraw' => 'Universitas Brawijaya',
            'unesa' => 'Universitas Negeri Surabaya',
            'um' => 'Universitas Negeri Malang',
            'pens' => 'Politeknik Elektronika Negeri Surabaya',
            'ppns' => 'Politeknik Perkapalan Negeri Surabaya',
            'polinema' => 'Politeknik Negeri Malang',
            'polije' => 'Politeknik Negeri Jember',
            'unej' => 'Universitas Jember',
            'upn jatim' => 'UPN "Veteran" Jawa Timur',
            'uin malang' => 'UIN Maulana Malik Ibrahim Malang',
            'uin sunan ampel' => 'UIN Sunan Ampel Surabaya',
            'petra' => 'Universitas Kristen Petra',
            'ubaya' => 'Universitas Surabaya',
            'wima' => 'Universitas Katolik Widya Mandala Surabaya',
            'umm' => 'Universitas Muhammadiyah Malang',

            // Luar Jawa
            'usu' => 'Universitas Sumatera Utara',
            'unand' => 'Universitas Andalas',
            'unri' => 'Universitas Riau',
            'unsri' => 'Universitas Sriwijaya',
            'unila' => 'Universitas Lampung',
            'polmed' => 'Politeknik Negeri Medan',
            'polsri' => 'Politeknik Negeri Sriwijaya',
            'itera' => 'Institut Teknologi Sumatera',
            'usk' => 'Universitas Syiah Kuala',
            'unsyiah' => 'Universitas Syiah Kuala',
            'untan' => 'Universitas Tanjungpura',
            'unmul' => 'Universitas Mulawarman',
            'itk' => 'Institut Teknologi Kalimantan',
            'polnep' => 'Politeknik Negeri Pontianak',
            'unhas' => 'Universitas Hasanuddin',
            'unm' => 'Universitas Negeri Makassar',
            'unsrat' => 'Universitas Sam Ratulangi',
            'untad' => 'Universitas Tadulako',
            'unud' => 'Universitas Udayana',
            'undiksha' => 'Universitas Pendidikan Ganesha',
            'unram' => 'Universitas Mataram',
            'undana' => 'Universitas Nusa Cendana',
            'uncen' => 'Universitas Cenderawasih',
        ];
    }

    public function findSchoolSuggestion(?string $input): ?string
    {
        if (empty($input)) {
            return null;
        }

        $clean = strtolower(trim($input));
        $clean = preg_replace('/[^\w\s\-\"\']/', '', $clean);
        $clean = preg_replace('/\s+/', ' ', $clean);

        if (empty($clean)) {
            return null;
        }

        $dict = self::getUniversityAbbreviationDictionary();

        // 1. Cek langsung kunci singkatan
        if (isset($dict[$clean])) {
            return $dict[$clean];
        }

        // 2. Cek apakah singkatan merupakan kata mandiri di dalam input
        foreach ($dict as $abbr => $fullName) {
            if ($clean === $abbr) {
                return $fullName;
            }

            if (preg_match('/\b' . preg_quote($abbr, '/') . '\b/i', $clean)) {
                if (strcasecmp($clean, $fullName) !== 0 && !str_starts_with($clean, 'universitas') && !str_starts_with($clean, 'politeknik') && !str_starts_with($clean, 'institut')) {
                    return $fullName;
                }
            }
        }

        return null;
    }

    public function updatedSchoolName($value)
    {
        $this->school_suggestion = $this->findSchoolSuggestion($value);
        $this->validateOnly('school_name');
    }

    public function applySchoolSuggestion($value = null)
    {
        $target = $value ?: $this->school_suggestion;
        if (!empty($target)) {
            $this->school_name = $target;
            $this->school_suggestion = null;
            $this->resetErrorBag('school_name');
        }
    }

    protected function rules()
    {
        $isSchool = $this->isSchoolDegree();
        $gpaRule = $isSchool ? 'nullable|numeric|between:0,100.00' : 'nullable|numeric|between:0,4.00';

        $isGraduated = !$this->is_ongoing;
        $needsCertificate = $isGraduated && empty($this->existing_certificate_path);

        $certificateRule = $needsCertificate
            ? 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
            : 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';

        return [
            'school_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $trimmed = trim((string) $value);
                    if (empty($trimmed)) {
                        return;
                    }

                    // 1. Cek minimal 2 kata
                    $words = array_values(array_filter(preg_split('/\s+/', $trimmed)));
                    if (count($words) < 2) {
                        $suggestion = $this->findSchoolSuggestion($trimmed);
                        if ($suggestion) {
                            $fail(__('Nama institusi tidak boleh disingkat. Gunakan: ":suggestion".', ['suggestion' => $suggestion]));
                        } else {
                            $fail(__('Nama institusi tidak boleh disingkat (wajib minimal 2 kata).'));
                        }
                        return;
                    }

                    // 2. Cek panjang karakter minimal
                    if (strlen($trimmed) < 4) {
                        $fail(__('Nama institusi terlalu pendek.'));
                        return;
                    }

                    // 3. Cek apakah di dalam input masih terdapat singkatan terlarang (misal "Polines Semarang")
                    $suggestion = $this->findSchoolSuggestion($trimmed);
                    if ($suggestion && strcasecmp($trimmed, $suggestion) !== 0) {
                        $fail(__('Harap gunakan nama resmi: ":suggestion".', ['suggestion' => $suggestion]));
                        return;
                    }
                },
            ],
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
            'certificate_file' => $certificateRule,
        ];
    }

    protected function messages()
    {
        $isSchool = $this->isSchoolDegree();
        return [
            'school_name.required' => __('Nama sekolah / perguruan tinggi wajib diisi.'),
            'custom_major.required' => __('Nama jurusan wajib diisi jika memilih lainnya.'),
            'start_year.required' => __('Tahun mulai wajib diisi.'),
            'start_year.digits' => __('Tahun mulai harus 4 digit angka.'),
            'end_year.gte' => __('Tahun selesai harus lebih besar atau sama dengan tahun mulai.'),
            'gpa.between' => $isSchool 
                ? __('Nilai Rata-rata harus di antara 0 sampai 100.') 
                : __('IPK harus di antara 0.00 sampai 4.00.'),
            'certificate_file.required' => __('Ijazah / Surat Keterangan Lulus (SKL) wajib diunggah untuk pendidikan yang sudah lulus.'),
            'certificate_file.mimes' => __('Format file ijazah harus berupa PDF, JPG, JPEG, atau PNG.'),
            'certificate_file.max' => __('Ukuran file ijazah tidak boleh melebihi 2MB.'),
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
            'school_suggestion',
            'study_program',
            'start_year',
            'end_year',
            'is_ongoing',
            'gpa',
            'description',
            'certificate_file',
            'existing_certificate_path',
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
        $this->existing_certificate_path = $education->certificate_path;
        $this->certificate_file = null;

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

        // Kelola file Ijazah / SKL
        $certPath = $this->existing_certificate_path;
        if ($this->certificate_file) {
            if ($certPath && Storage::disk('public')->exists($certPath)) {
                Storage::disk('public')->delete($certPath);
            }
            $certPath = $this->certificate_file->store('certificates/educations', 'public');
        }
        $validatedData['certificate_path'] = $this->is_ongoing ? null : $certPath;
        unset($validatedData['certificate_file']);

        if ($this->isEdit && $this->education_id) {
            $education = Education::where('profile_id', $profile->id)->where('id', $this->education_id)->firstOrFail();
            $education->update($validatedData);
            $msg = __('Riwayat pendidikan berhasil diperbarui.');
            $action = 'update';
        } else {
            $validatedData['profile_id'] = $profile->id;
            Education::create($validatedData);
            $msg = __('Riwayat pendidikan berhasil ditambahkan.');
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
                $education = Education::where('profile_id', $profile->id)->where('id', $this->deleteId)->first();
                if ($education) {
                    if ($education->certificate_path && Storage::disk('public')->exists($education->certificate_path)) {
                        Storage::disk('public')->delete($education->certificate_path);
                    }
                    $education->delete();
                }
            }
        }

        $this->showDeleteModal = false;
        $this->deleteId = null;
        $this->dispatch('profile-updated', message: __('Riwayat pendidikan berhasil dihapus.'), action: 'delete');
    }

    // --- SKILL CRUD METHODS ---
    public function openSkillModal()
    {
        $this->resetSkillForm();
        $this->isSkillEdit = false;
        $this->skill_proficiency = 'baik';
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
            'skill_proficiency',
            'skill_description',
            'skill_certificate',
            'existing_skill_certificate',
        ]);
        $this->skill_proficiency = 'baik';
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
        $this->skill_proficiency = $skill->proficiency ?? 'baik';
        $this->skill_description = $skill->description ?? '';
        $this->existing_skill_certificate = $skill->certificate_path;

        $this->isSkillEdit = true;
        $this->showSkillModal = true;
    }

    public function saveSkill()
    {
        $this->validate([
            'skill_name' => 'required|string|max:255',
            'skill_proficiency' => 'nullable|in:sangat_baik,baik,cukup,kurang',
            'skill_description' => 'nullable|string|max:500',
            'skill_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'skill_name.required' => 'Nama software / aplikasi komputer wajib diisi.',
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
                'category' => 'computer',
                'proficiency' => $this->skill_proficiency,
                'description' => $this->skill_description,
                'certificate_path' => $certPath,
            ]);
            $msg = __('Kemampuan aplikasi komputer berhasil diperbarui.');
            $action = 'update';
        } else {
            Skill::create([
                'profile_id' => $profile->id,
                'category' => 'computer',
                'name' => $this->skill_name,
                'proficiency' => $this->skill_proficiency,
                'description' => $this->skill_description,
                'certificate_path' => $certPath,
            ]);
            $msg = __('Kemampuan aplikasi komputer berhasil ditambahkan.');
            $action = 'create';
        }

        $this->closeSkillModal();
        $this->dispatch('profile-updated', message: $msg, action: $action);
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
                }
            }
        }

        $this->showDeleteSkillModal = false;
        $this->deleteSkillId = null;
        $this->dispatch('profile-updated', message: __('Kemampuan aplikasi komputer berhasil dihapus.'), action: 'delete');
    }

    // --- EXTRA CRUD METHODS (SERTIFIKASI, PELATIHAN, BAHASA) ---
    public function openExtraModal($type)
    {
        $this->resetExtraForm();
        $this->extraModalType = $type;
        $this->isExtraEdit = false;
        if ($type === 'language') {
            $this->extraOral = 'baik';
            $this->extraWritten = 'baik';
            $this->extraConversation = 'baik';
        }
        $this->showExtraModal = true;
    }

    public function closeExtraModal()
    {
        $this->showExtraModal = false;
        $this->resetExtraForm();
    }

    public function resetExtraForm()
    {
        $this->reset([
            'extraItemId',
            'extraName',
            'extraCertificate',
            'existingExtraCertificate',
            'extraOral',
            'extraWritten',
            'extraConversation',
        ]);
        $this->extraOral = 'baik';
        $this->extraWritten = 'baik';
        $this->extraConversation = 'baik';
        $this->resetErrorBag();
    }

    public function editExtra($type, $id)
    {
        $user = Auth::user();
        $profile = ApplicantProfile::where('user_id', $user->id)->first();
        if (!$profile) return;

        $model = $this->getExtraModelInstance($type, $profile->id, $id);
        if (!$model) return;

        $this->extraModalType = $type;
        $this->extraItemId = $model->id;
        $this->extraName = $model->name;
        $this->existingExtraCertificate = $model->certificate_path;

        if ($type === 'language') {
            $this->extraOral = $model->oral ?? 'baik';
            $this->extraWritten = $model->written ?? 'baik';
            $this->extraConversation = $model->conversation ?? 'baik';
        }

        $this->isExtraEdit = true;
        $this->showExtraModal = true;
    }

    public function saveExtra()
    {
        $typeLabel = $this->getExtraTypeLabel($this->extraModalType);

        $rules = [
            'extraName' => 'required|string|max:255',
            'extraCertificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        if ($this->extraModalType === 'language') {
            $rules['extraOral'] = 'nullable|in:baik,cukup,kurang';
            $rules['extraWritten'] = 'nullable|in:baik,cukup,kurang';
            $rules['extraConversation'] = 'nullable|in:baik,cukup,kurang';
        }

        $this->validate($rules, [
            'extraName.required' => "Nama {$typeLabel} wajib diisi.",
            'extraCertificate.mimes' => 'Format file sertifikat/bukti harus PDF, JPG, JPEG, atau PNG.',
            'extraCertificate.max' => 'Ukuran file tidak boleh melebihi 2MB.',
        ]);

        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        $certPath = $this->existingExtraCertificate;
        if ($this->extraCertificate) {
            if ($certPath && Storage::disk('public')->exists($certPath)) {
                Storage::disk('public')->delete($certPath);
            }
            $folder = "certificates/{$this->extraModalType}s";
            $certPath = $this->extraCertificate->store($folder, 'public');
        }

        $data = [
            'name' => $this->extraName,
            'certificate_path' => $certPath,
        ];

        if ($this->extraModalType === 'language') {
            $data['oral'] = $this->extraOral;
            $data['written'] = $this->extraWritten;
            $data['conversation'] = $this->extraConversation;
        }

        if ($this->isExtraEdit && $this->extraItemId) {
            $model = $this->getExtraModelInstance($this->extraModalType, $profile->id, $this->extraItemId);
            if ($model) {
                $model->update($data);
                $msg = __("{$typeLabel} berhasil diperbarui.");
                $action = 'update';
            } else {
                $msg = __("Data tidak ditemukan.");
                $action = 'update';
            }
        } else {
            $class = $this->getExtraModelClass($this->extraModalType);
            $data['profile_id'] = $profile->id;
            $class::create($data);
            $msg = __("{$typeLabel} berhasil ditambahkan.");
            $action = 'create';
        }

        $this->closeExtraModal();
        $this->dispatch('profile-updated', message: $msg, action: $action);
    }

    public function confirmDeleteExtra($type, $id)
    {
        $this->deleteExtraId = $id;
        $this->deleteExtraType = $type;
        $this->showDeleteExtraModal = true;
    }

    public function cancelDeleteExtra()
    {
        $this->showDeleteExtraModal = false;
        $this->deleteExtraId = null;
        $this->deleteExtraType = null;
    }

    public function deleteExtra()
    {
        $deletedLabel = 'Data';
        if ($this->deleteExtraId && $this->deleteExtraType) {
            $user = Auth::user();
            $profile = ApplicantProfile::where('user_id', $user->id)->first();
            if ($profile) {
                $model = $this->getExtraModelInstance($this->deleteExtraType, $profile->id, $this->deleteExtraId);
                if ($model) {
                    if ($model->certificate_path && Storage::disk('public')->exists($model->certificate_path)) {
                        Storage::disk('public')->delete($model->certificate_path);
                    }
                    $deletedLabel = $this->getExtraTypeLabel($this->deleteExtraType);
                    $model->delete();
                }
            }
        }

        $this->cancelDeleteExtra();
        $this->dispatch('profile-updated', message: __("{$deletedLabel} berhasil dihapus."), action: 'delete');
    }

    protected function getExtraModelClass($type)
    {
        return match ($type) {
            'training' => Training::class,
            'language' => Language::class,
            default => Certification::class,
        };
    }

    protected function getExtraModelInstance($type, $profileId, $id)
    {
        $class = $this->getExtraModelClass($type);
        return $class::where('profile_id', $profileId)->where('id', $id)->first();
    }

    public function getExtraTypeLabel($type)
    {
        return match ($type) {
            'training' => 'Pelatihan & Kursus',
            'language' => 'Kemampuan Bahasa',
            default => 'Sertifikasi Profesional',
        };
    }

    public function render()
    {
        $user = Auth::user();
        $profile = $user ? ApplicantProfile::where('user_id', $user->id)->first() : null;
        $educations = $profile ? Education::where('profile_id', $profile->id)->orderBy('start_year', 'desc')->get() : collect();
        $skills = $profile ? Skill::where('profile_id', $profile->id)->get() : collect();
        $certifications = $profile ? Certification::where('profile_id', $profile->id)->get() : collect();
        $trainings = $profile ? Training::where('profile_id', $profile->id)->get() : collect();
        $languages = $profile ? Language::where('profile_id', $profile->id)->get() : collect();

        $degrees = Cache::remember('master_degrees_ordered', 86400, function () {
            return Degree::orderBy('rank', 'asc')->get();
        });
        $majors = Cache::remember('master_majors_ordered', 86400, function () {
            return Major::orderBy('name', 'asc')->get();
        });

        return view('livewire.applicant.pendidikan', [
            'educations' => $educations,
            'skills' => $skills,
            'certifications' => $certifications,
            'trainings' => $trainings,
            'languages' => $languages,
            'degrees' => $degrees,
            'majors' => $majors,
            'uniDict' => self::getUniversityAbbreviationDictionary(),
        ]);
    }
}

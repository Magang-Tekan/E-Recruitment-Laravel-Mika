<?php

namespace App\Livewire\Applicant;

use App\Models\ApplicantJobPreference;
use App\Models\ApplicantProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class DataTambahan extends Component
{
    // ==========================================
    // F. KONSEP DIRI (Kelebihan & Kekurangan)
    // ==========================================
    public $self_concept = [
        'strengths' => ['', '', ''],
        'weaknesses' => ['', '', ''],
    ];

    // ==========================================
    // G. KESEHATAN (7 Pertanyaan)
    // Default null: belum dipilih (bukan TIDAK, bukan IYA)
    // ==========================================
    public $health = [
        'smoking' => ['is_yes' => null, 'detail' => ''],
        'alcohol' => ['is_yes' => null, 'detail' => ''],
        'hospitalized' => ['is_yes' => null, 'detail' => ''],
        'allergy' => ['is_yes' => null, 'detail' => ''],
        'ongoing_treatment' => ['is_yes' => null, 'detail' => ''],
        'infectious_disease' => ['is_yes' => null, 'detail' => ''],
        'disease_history' => ['is_yes' => null, 'detail' => ''],
    ];

    // ==========================================
    // H. INFORMASI LAIN (16 Pertanyaan)
    // Default null / kosong: belum dipilih/diisi apapun
    // ==========================================
    public $other = [
        // 1. Waktu luang
        'free_time_activity' => '',
        // 2 & 3. Kasus kejahatan & tahanan
        'crime_involvement' => ['is_yes' => null, 'detail' => ''],
        'custody' => ['is_yes' => null, 'detail' => ''],
        // 4 & 5. Motivasi melamar
        'reason_applying_company' => '',
        'reason_applying_position' => '',
        // 6. Sumber info
        'vacancy_source' => '',
        // 7. Melamar di perusahaan lain
        'other_applications' => ['is_yes' => null, 'detail' => ''],
        // 8 - 11. Kesiapan penempatan & mutasi (Default null: belum dipilih)
        'business_trip_ready' => null,
        'mutation_position_ready' => null,
        'mutation_group_ready' => null,
        'relocation_city_ready' => null,
        // 12. Relasi keluarga/kerabat di perusahaan
        'internal_relations' => ['is_yes' => null, 'detail' => ''],
        // 13. Pernah melamar sebelumnya
        'previously_applied' => ['is_yes' => null, 'detail' => ''],
        // 14. Kesiapan kerja
        'ready_to_work' => '',
        // 15. Ekspektasi gaji
        'expected_salary' => '',
        // 16. Fasilitas / tunjangan
        'expected_benefits' => '',
        // 17. Pernyataan kebenaran data
        'statement_agreed' => false,
    ];

    public function mount()
    {
        $user = Auth::user();
        $profile = $user ? ApplicantProfile::where('user_id', $user->id)->first() : null;

        if ($profile) {
            if (!empty($profile->self_concept) && is_array($profile->self_concept)) {
                $strengths = $profile->self_concept['strengths'] ?? [];
                $weaknesses = $profile->self_concept['weaknesses'] ?? [];
                while (count($strengths) < 3) {
                    $strengths[] = '';
                }
                while (count($weaknesses) < 3) {
                    $weaknesses[] = '';
                }
                $this->self_concept = [
                    'strengths' => array_values($strengths),
                    'weaknesses' => array_values($weaknesses),
                ];
            }
            if (!empty($profile->health_answers) && is_array($profile->health_answers)) {
                $this->health = array_replace_recursive($this->health, $profile->health_answers);
            }
            if (!empty($profile->other_answers) && is_array($profile->other_answers)) {
                $this->other = array_replace_recursive($this->other, $profile->other_answers);
            }

            // 🔄 Auto-Sync dari Minat Kerja (Job Preference)
            $pref = $profile->jobPreference;
            if ($pref) {
                if (!empty($pref->expected_salary)) {
                    $formattedSalary = ApplicantProfile::formatSalaryString($pref->expected_salary);
                    if (empty($this->other['expected_salary'])) {
                        $this->other['expected_salary'] = $formattedSalary;
                    } else {
                        // Jika sudah ada tapi berbeda dengan di Minat Kerja
                        $currNum = ApplicantProfile::parseSalaryNumber($this->other['expected_salary']);
                        if ($currNum === null || (float)$currNum !== (float)$pref->expected_salary) {
                            $existing = trim((string)$this->other['expected_salary']);
                            if (is_numeric($existing) || preg_match('/^Rp\s*[\d\.]+\s*$/i', $existing)) {
                                $this->other['expected_salary'] = $formattedSalary;
                            } else {
                                $this->other['expected_salary'] = preg_replace(
                                    '/(?:rp\.?\s*)?(\d{1,3}(?:\.\d{3})+|\d{4,})/i',
                                    $formattedSalary,
                                    $existing,
                                    1
                                );
                            }
                        }
                    }
                } elseif (!empty($this->other['expected_salary'])) {
                    // Jika di preference belum ada nominal tapi di data tambahan ada
                    $parsed = ApplicantProfile::parseSalaryNumber($this->other['expected_salary']);
                    if ($parsed !== null && $parsed > 0) {
                        $pref->update(['expected_salary' => $parsed]);
                    }
                }
            }
        }
    }

    #[On('salary-updated')]
    public function syncSalaryFromEvent($salary = null, $formatted = null)
    {
        if ($formatted) {
            $this->other['expected_salary'] = $formatted;
        } elseif ($salary) {
            $this->other['expected_salary'] = ApplicantProfile::formatSalaryString($salary);
        }
    }

    public function save($healthData = null, $otherData = null, $selfConceptData = null)
    {
        if (is_array($selfConceptData)) {
            $this->self_concept = $selfConceptData;
        }
        if (is_array($healthData)) {
            $this->health = array_replace_recursive($this->health, $healthData);
        }
        if (is_array($otherData)) {
            $this->other = array_replace_recursive($this->other, $otherData);
        }

        // 0. Validasi Konsep Diri (minimal 1 kelebihan dan 1 kekurangan diisi)
        $cleanStrengths = array_values(array_filter($this->self_concept['strengths'] ?? [], fn($v) => !empty(trim((string)$v))));
        $cleanWeaknesses = array_values(array_filter($this->self_concept['weaknesses'] ?? [], fn($v) => !empty(trim((string)$v))));

        if (count($cleanStrengths) < 1 || count($cleanWeaknesses) < 1) {
            $this->dispatch('profile-updated', message: __('Mohon isi minimal 1 poin Kelebihan dan 1 poin Kekurangan pada Konsep Diri.'), action: 'error');
            return;
        }

        // 1. Validasi Kesehatan (7 pertanyaan wajib dijawab)
        foreach ($this->health as $key => $val) {
            if (!isset($val['is_yes']) || $val['is_yes'] === null) {
                $this->dispatch('profile-updated', message: __('Seluruh pertanyaan Kesehatan wajib dijawab (pilih IYA atau TIDAK).'), action: 'error');
                return;
            }
            if ($val['is_yes'] === true && empty(trim($val['detail'] ?? ''))) {
                $this->dispatch('profile-updated', message: __('Mohon isi penjelasan rincian untuk pertanyaan Kesehatan yang dijawab IYA.'), action: 'error');
                return;
            }
        }

        // 2. Validasi Kesiapan Kerja (Siap / Tidak Siap)
        $readyKeys = ['business_trip_ready', 'mutation_position_ready', 'mutation_group_ready', 'relocation_city_ready'];
        foreach ($readyKeys as $rk) {
            if (empty($this->other[$rk])) {
                $this->dispatch('profile-updated', message: __('Seluruh opsi Kesiapan Perjalanan Dinas & Penempatan wajib dipilih (Siap atau Tidak Siap).'), action: 'error');
                return;
            }
        }

        // 3. Validasi Informasi Lain (IYA / TIDAK)
        $yesNoKeys = ['crime_involvement', 'custody', 'other_applications', 'internal_relations', 'previously_applied'];
        foreach ($yesNoKeys as $yk) {
            if (!isset($this->other[$yk]['is_yes']) || $this->other[$yk]['is_yes'] === null) {
                $this->dispatch('profile-updated', message: __('Seluruh pertanyaan pilihan IYA/TIDAK pada Informasi Lain wajib dijawab.'), action: 'error');
                return;
            }
            if ($this->other[$yk]['is_yes'] === true && empty(trim($this->other[$yk]['detail'] ?? ''))) {
                $this->dispatch('profile-updated', message: __('Mohon isi penjelasan untuk pertanyaan Informasi Lain yang dijawab IYA.'), action: 'error');
                return;
            }
        }

        // 4. Validasi Isian Teks Informasi Lain
        $textKeys = [
            'free_time_activity' => __('Aktivitas waktu luang/libur'),
            'reason_applying_company' => __('Alasan melamar di perusahaan ini'),
            'reason_applying_position' => __('Alasan melamar posisi ini'),
            'vacancy_source' => __('Darimana info lowongan didapat'),
            'ready_to_work' => __('Kapan Anda siap bekerja'),
            'expected_salary' => __('Gaji yang Anda inginkan'),
            'expected_benefits' => __('Fasilitas/tunjangan yang diinginkan'),
        ];
        foreach ($textKeys as $tk => $label) {
            if (empty(trim($this->other[$tk] ?? ''))) {
                $this->dispatch('profile-updated', message: __(':field wajib diisi.', ['field' => $label]), action: 'error');
                return;
            }
        }

        // 5. Validasi Pernyataan Kebenaran Data Pelamar
        if (empty($this->other['statement_agreed'])) {
            $this->dispatch('profile-updated', message: __('Anda wajib menyetujui pernyataan kebenaran data pelamar sebelum menyimpan.'), action: 'error');
            return;
        }

        $user = Auth::user();
        $profile = ApplicantProfile::firstOrCreate(['user_id' => $user->id]);

        $profile->update([
            'self_concept' => [
                'strengths' => array_values(array_filter($this->self_concept['strengths'] ?? [], fn($v) => !empty(trim((string)$v)))),
                'weaknesses' => array_values(array_filter($this->self_concept['weaknesses'] ?? [], fn($v) => !empty(trim((string)$v)))),
            ],
            'health_answers' => $this->health,
            'other_answers' => $this->other,
        ]);

        // 🔄 Auto-Sync ke Minat Kerja (ApplicantJobPreference)
        $parsedSalary = ApplicantProfile::parseSalaryNumber($this->other['expected_salary'] ?? null);
        if ($parsedSalary !== null && $parsedSalary > 0) {
            ApplicantJobPreference::updateOrCreate(
                ['applicant_profile_id' => $profile->id],
                ['expected_salary' => $parsedSalary]
            );
            $this->dispatch('salary-updated', salary: $parsedSalary, formatted: ApplicantProfile::formatSalaryString($parsedSalary));
        }

        $this->dispatch('profile-updated', message: __('Kuesioner Data Tambahan berhasil disimpan (dan otomatis disinkronkan ke Minat Kerja).'), action: 'update');
    }

    public function render()
    {
        return view('livewire.applicant.data_tambahan');
    }
}

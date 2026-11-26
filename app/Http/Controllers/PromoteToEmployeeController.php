<?php

namespace App\Http\Controllers;

use App\Mail\EmployeePromotedMail;
use App\Models\ApplicationStatusHistory;
use App\Models\EmployeeProfile;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class PromoteToEmployeeController extends Controller
{
    /**
     * Angkat pelamar dengan status Accepted menjadi karyawan (role Employee).
     */
    public function promote(Request $request, string $id)
    {
        $request->validate([
            'employee_type'       => 'required|in:permanent,contract,internship,probation',
            'contract_start_date' => 'required|date',
            'contract_end_date'   => [
                'nullable',
                'date',
                'after:contract_start_date',
                // PKWT & magang wajib punya batas waktu; masa percobaan opsional.
                Rule::requiredIf(fn () => in_array($request->input('employee_type'), ['contract', 'internship'], true)),
            ],
            'internal_note' => 'nullable|string|max:1000',
        ], [
            'contract_start_date.required' => 'Tanggal mulai bekerja wajib diisi.',
            'contract_end_date.required'   => 'Tanggal berakhir wajib diisi untuk tipe karyawan ini.',
            'contract_end_date.after'      => 'Tanggal berakhir harus setelah tanggal mulai bekerja.',
        ]);

        $application = JobApplication::with(['job.company', 'job.department', 'applicantProfile.user'])
            ->findOrFail($id);

        if (strtolower(trim((string) $application->status)) !== 'accepted') {
            return redirect()->back()
                ->with('error', 'Hanya pelamar dengan status Diterima (Accepted) yang dapat diangkat menjadi karyawan.');
        }

        $applicant = $application->applicantProfile;
        $user = $applicant?->user;

        if (! $applicant || ! $user) {
            return redirect()->back()
                ->with('error', 'Data akun pelamar tidak ditemukan. Pengangkatan karyawan dibatalkan.');
        }

        if ($user->role_id == 4) {
            return redirect()->back()
                ->with('error', 'Pelamar ini sudah terdaftar sebagai karyawan.');
        }

        $job = $application->job;
        $employeeType = $request->input('employee_type');
        $startDate = $request->date('contract_start_date');
        // Karyawan tetap tidak punya tanggal berakhir.
        $endDate = $employeeType === 'permanent'
            ? null
            : ($request->filled('contract_end_date') ? $request->date('contract_end_date') : null);
        $internalNote = trim((string) $request->input('internal_note'));

        // Gender pelamar disimpan sebagai "Laki-laki"/"Perempuan",
        // sedangkan employee_profiles memakai enum male/female.
        $gender = match (strtolower(trim((string) $applicant->gender))) {
            'laki-laki', 'laki laki', 'pria', 'male' => 'male',
            'perempuan', 'wanita', 'female'         => 'female',
            default                                 => null,
        };

        // Kolom nik bersifat unik; jangan sampai pengangkatan gagal karena NIK sudah dipakai.
        $nik = $applicant->nik;
        $nikConflict = false;
        if (! empty($nik) && EmployeeProfile::where('nik', $nik)->where('user_id', '!=', $user->id)->exists()) {
            $nikConflict = true;
            $nik = null;
        }

        $prefill = [
            'full_name'      => $applicant->full_name ?: $user->name,
            'employee_type'  => $employeeType,
            'contract_start_date' => $startDate,
            'nik'            => $nik,
            'gender'         => $gender,
            'birth_date'     => $applicant->birth_date,
            'phone_number'   => $applicant->phone,
            'photo'          => $applicant->photo ?: $user->avatar,
            'company_id'     => $job?->company_id,
            'department_id'  => $job?->department_id,
            'position_id'    => $job?->position_id,
            'position_title' => $job?->title,
        ];

        $closedOthersCount = 0;
        $employeeProfile = null;

        DB::transaction(function () use ($user, $applicant, $prefill, $application, $job, $employeeType, $startDate, $endDate, $internalNote, &$closedOthersCount, &$employeeProfile) {
            // Buat profil lebih dulu agar hook `updated` pada User tidak membuat profil kosong.
            $profile = EmployeeProfile::firstOrNew(['user_id' => $user->id]);
            foreach ($prefill as $field => $value) {
                if ($field === 'employee_type' || ! blank($value)) {
                    $profile->{$field} = $value;
                }
            }
            $profile->employment_status = EmployeeProfile::STATUS_ACTIVE;
            $profile->contract_start_date = $startDate;
            $profile->contract_end_date = $endDate;
            $profile->save();
            $employeeProfile = $profile;

            $user->update(['role_id' => 4]);

            $labels = [
                'permanent'  => 'Karyawan Tetap',
                'contract'   => 'Karyawan Kontrak',
                'internship' => 'Magang',
                'probation'  => 'Masa Percobaan',
            ];

            $note = 'Pelamar resmi diangkat jadi karyawan sebagai ' . $labels[$employeeType] . '.';
            $note .= ' Tanggal mulai kerja: ' . $startDate->translatedFormat('d F Y') . '.';
            if ($endDate) {
                $note .= ' Bekerja sampai: ' . $endDate->translatedFormat('d F Y') . '.';
            }
            if ($internalNote !== '') {
                $note .= ' Catatan internal: ' . $internalNote;
            }

            ApplicationStatusHistory::create([
                'job_applications_id' => $application->id,
                'status'              => $application->status,
                'notes'               => $note,
                'changed_by'          => auth()->id(),
                'changed_at'          => now(),
            ]);

            // Tutup lamaran lain yang masih berjalan milik pelamar yang sama.
            $jobTitle = $job?->title ?? 'posisi terpilih';
            $otherActiveApps = JobApplication::where('profile_id', $applicant->id)
                ->where('id', '!=', $application->id)
                ->whereRaw("LOWER(TRIM(COALESCE(status, ''))) NOT IN ('accepted', 'hired', 'rejected', 'withdrawn', 'dibatalkan')")
                ->get();

            $autoCloseNote = "Ditutup otomatis karena pelamar telah diterima dan diangkat menjadi karyawan pada posisi {$jobTitle}.";

            foreach ($otherActiveApps as $otherApp) {
                $otherApp->update([
                    'status' => 'Withdrawn',
                    'notes'  => $autoCloseNote,
                ]);

                ApplicationStatusHistory::create([
                    'job_applications_id' => $otherApp->id,
                    'status'              => 'Withdrawn',
                    'notes'               => $autoCloseNote,
                    'changed_by'          => auth()->id(),
                    'changed_at'          => now(),
                ]);

                $closedOthersCount++;
            }
        });

        $emailSent = false;
        $shouldSendEmail = $request->boolean('send_email', true);

        if ($shouldSendEmail && $user->email && $employeeProfile) {
            $employeeProfile->loadMissing(['company', 'department', 'user']);
            try {
                Mail::to($user->email)->send(new EmployeePromotedMail($application, $employeeProfile));
                $emailSent = true;
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim email pemberitahuan pengangkatan karyawan: ' . $e->getMessage(), [
                    'application_id' => $application->id,
                    'user_id'        => $user->id,
                    'recipient'      => $user->email,
                ]);
            }
        }

        $name = $applicant->full_name ?: $user->name;
        $successMsg = "{$name} berhasil diangkat menjadi karyawan. Profil karyawan sudah dibuat dan dapat dilengkapi dari menu Kepegawaian.";

        if ($emailSent) {
            $successMsg .= " Email pemberitahuan aktivasi Portal Karyawan telah dikirim ke {$user->email}.";
        }

        if ($closedOthersCount > 0) {
            $successMsg .= " {$closedOthersCount} lamaran lain yang masih aktif milik pelamar ini otomatis ditutup.";
        }

        if ($nikConflict) {
            $successMsg .= " Perhatian: NIK pelamar sudah digunakan akun pegawai lain sehingga NIK pada profil karyawan dikosongkan sementara — silakan perbarui di menu Kepegawaian.";
        }

        return redirect()->route('admin.application')
            ->with('update', $successMsg);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles
        DB::table('roles')->updateOrInsert(['id' => 1], ['name' => 'Admin']);
        DB::table('roles')->updateOrInsert(['id' => 2], ['name' => 'Recruiter']);
        DB::table('roles')->updateOrInsert(['id' => 3], ['name' => 'Applicant']);
        DB::table('roles')->updateOrInsert(['id' => 4], ['name' => 'Employee']);

        // 2. Seed Default Users
        User::firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'role_id' => 1,
                'nik' => '0000000000000000',
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'ilham@gmail.com'],
            [
                'role_id' => 3,
                'nik' => '3374000011112222',
                'name' => 'Ilham Taruprasetyo',
                'password' => Hash::make('ilham123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'recruiter@mail.com'],
            [
                'role_id' => 2,
                'nik' => '9999999999999999',
                'name' => 'Recruiter Team',
                'password' => Hash::make('recruiter123'),
            ]
        );

        // 3. Seed Companies, Departments & Positions (MIKA & AKA)
        $this->call(CompanyDepartmentPositionSeeder::class);

        // 4. Seed Company Profiles (Visi, Misi, Deskripsi, Kontak)
        $this->call(CompanyProfileSeeder::class);

        // 5. Seed Sample Jobs (Terkoneksi ke Posisi & Departemen Nyata)
        $mika = Company::where('name', 'like', '%Mitra Karya Analitika%')->first();
        $mikaDept = Department::where('company_id', $mika?->id)->where('name', 'PRODUK & MARKETING')->first();
        $mikaPos = Position::where('department_id', $mikaDept?->id)->where('name', 'Digital Marketing')->first();

        $aka = Company::where('name', 'like', '%Autentik%')->first();
        $akaDept = Department::where('company_id', $aka?->id)->where('name', 'PLANT / PABRIK')->first();
        $akaPos = Position::where('department_id', $akaDept?->id)->where('name', 'Embedded Engineer')->first();

        if ($mika && $mikaDept && $mikaPos && DB::table('jobs')->where('title', 'Digital Marketing Specialist')->doesntExist()) {
            DB::table('jobs')->insert([
                'company_id' => $mika->id,
                'department_id' => $mikaDept->id,
                'position_id' => $mikaPos->id,
                'title' => 'Digital Marketing Specialist',
                'description' => 'Mengelola strategi pemasaran digital, konten media sosial, dan kampanye iklan produk analitika.',
                'employment_type' => 'Full-time',
                'location' => 'Semarang',
                'salary_min' => 5000000,
                'salary_max' => 8000000,
                'quota' => 2,
                'deadline' => Carbon::now()->addDays(30),
                'status' => 'Open',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($aka && $akaDept && $akaPos && DB::table('jobs')->where('title', 'Embedded Hardware Engineer')->doesntExist()) {
            DB::table('jobs')->insert([
                'company_id' => $aka->id,
                'department_id' => $akaDept->id,
                'position_id' => $akaPos->id,
                'title' => 'Embedded Hardware Engineer',
                'description' => 'Pengalaman dengan mikrokontroler (ESP32 / STM32), desain sirkuit PCB, dan integrasi perangkat IoT.',
                'employment_type' => 'Contract',
                'location' => 'Semarang',
                'salary_min' => 6000000,
                'salary_max' => 10000000,
                'quota' => 1,
                'deadline' => Carbon::now()->addDays(15),
                'status' => 'Open',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 6. Seed Degrees & Majors
        $degrees = ['SMA/SMK', 'D3', 'D4/S1', 'S2', 'S3'];
        foreach ($degrees as $rank => $name) {
            DB::table('degrees')->updateOrInsert(
                ['name' => $name],
                ['rank' => $rank + 1, 'updated_at' => now()]
            );
        }

        $majors = ['Teknik Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Teknik Elektro', 'Manajemen', 'Akuntansi'];
        foreach ($majors as $major) {
            DB::table('majors')->updateOrInsert(
                ['name' => $major],
                ['updated_at' => now()]
            );
        }

        // 7. Seed DISC Master Data & Questions
        $this->call(DiscMasterSeeder::class);
        // $this->call(DiscQuestionSeeder::class);

        // 8. Seed PAPI Kostick Master Data & Questions
        $this->call(PapiKostickMasterSeeder::class);
        $this->call(PapiKostickQuestionSeeder::class);

        // 9. Seed Company Showcase / Kegiatan Perusahaan
        $this->call(CompanyShowcaseSeeder::class);
    }
}


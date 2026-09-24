<?php

namespace Database\Seeders;

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

        // 2. Seed Users
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

        // 3. Seed Company & Department
        $company = DB::table('companies')->where('name', 'PT Autentik Karya Analitika')->first();
        if (!$company) {
            $companyId = DB::table('companies')->insertGetId([
                'name' => 'PT Autentik Karya Analitika',
                'city' => 'Semarang',
                'province' => 'Jawa Tengah'
            ]);
        } else {
            $companyId = $company->id;
        }

        $dept = DB::table('departments')->where('company_id', $companyId)->where('name', 'Engineering')->first();
        if (!$dept) {
            $deptId = DB::table('departments')->insertGetId([
                'company_id' => $companyId,
                'name' => 'Engineering',
                'description' => 'Software & Hardware Division'
            ]);
        } else {
            $deptId = $dept->id;
        }

        // 3b. Seed Positions
        $positionsData = [
            ['name' => 'Frontend Developer', 'description' => 'Mengembangkan antarmuka pengguna berbasis web.'],
            ['name' => 'Backend Developer', 'description' => 'Mengembangkan arsitektur server, API, dan basis data.'],
            ['name' => 'IoT Engineer', 'description' => 'Merancang dan mengintegrasikan perangkat keras mikrokontroler.'],
            ['name' => 'Quality Assurance (QA)', 'description' => 'Melakukan pengujian mutu dan otomatisasi sistem perangkat lunak.'],
        ];

        $positionMap = [];
        foreach ($positionsData as $pos) {
            $existing = DB::table('positions')->where('department_id', $deptId)->where('name', $pos['name'])->first();
            if (!$existing) {
                $posId = DB::table('positions')->insertGetId([
                    'department_id' => $deptId,
                    'name' => $pos['name'],
                    'description' => $pos['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $posId = $existing->id;
            }
            $positionMap[$pos['name']] = $posId;
        }

        // 4. Seed Jobs
        if (DB::table('jobs')->where('company_id', $companyId)->where('title', 'Frontend Developer')->count() === 0) {
            DB::table('jobs')->insert([
                'company_id' => $companyId,
                'department_id' => $deptId,
                'position_id' => $positionMap['Frontend Developer'] ?? null,
                'title' => 'Frontend Developer',
                'description' => 'Menguasai React JS, TailwindCSS.',
                'employment_type' => 'Full-time',
                'location' => 'Semarang',
                'salary_min' => 5000000,
                'salary_max' => 8000000,
                'quota' => 2,
                'deadline' => Carbon::now()->addDays(30),
                'status' => 'Open'
            ]);
        }

        if (DB::table('jobs')->where('company_id', $companyId)->where('title', 'IoT Engineer')->count() === 0) {
            DB::table('jobs')->insert([
                'company_id' => $companyId,
                'department_id' => $deptId,
                'position_id' => $positionMap['IoT Engineer'] ?? null,
                'title' => 'IoT Engineer',
                'description' => 'Pengalaman dengan ESP32 dan Arduino.',
                'employment_type' => 'Contract',
                'location' => 'Remote',
                'salary_min' => 6000000,
                'salary_max' => 10000000,
                'quota' => 1,
                'deadline' => Carbon::now()->addDays(15),
                'status' => 'Open'
            ]);
        }

        // 5. Seed Degrees & Majors
        $degrees = [
            ['name' => 'SMA/SMK', 'rank' => 1],
            ['name' => 'D3', 'rank' => 2],
            ['name' => 'D4/S1', 'rank' => 3],
            ['name' => 'S2', 'rank' => 4],
            ['name' => 'S3', 'rank' => 5],
        ];
        foreach ($degrees as $degree) {
            if (!DB::table('degrees')->where('name', $degree['name'])->exists()) {
                DB::table('degrees')->insert([
                    'name' => $degree['name'],
                    'rank' => $degree['rank'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $majors = ['Teknik Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Teknik Elektro', 'Manajemen', 'Akuntansi'];
        foreach ($majors as $majorName) {
            if (!DB::table('majors')->where('name', $majorName)->exists()) {
                DB::table('majors')->insert([
                    'name' => $majorName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 6. Seed DISC Master Data & Questions
        $this->call(DiscMasterSeeder::class);
        // $this->call(DiscQuestionSeeder::class);

        // 7. Seed PAPI Kostick Master Data & Questions
        $this->call(PapiKostickMasterSeeder::class);
        $this->call(PapiKostickQuestionSeeder::class);
    }
}

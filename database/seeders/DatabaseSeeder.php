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
        $superadmin = User::create([
            'role_id' => 1,
            'nik' => '0000000000000000',
            'name' => 'Administrator',
            'email' => 'admin@mail.com',
            'password' => Hash::make('admin123'),
        ]);

        $applicant = User::create([
            'role_id' => 3,
            'nik' => '3374000011112222',
            'name' => 'Ilham Taruprasetyo',
            'email' => 'ilham@gmail.com',
            'password' => Hash::make('ilham123'),
        ]);

        $recruiter = User::create([
            'role_id' => 2,
            'nik' => '9999999999999999',
            'name' => 'Recruiter Team',
            'email' => 'recruiter@mail.com',
            'password' => Hash::make('recruiter123'),
        ]);

        // 3. Seed Company & Department
        $companyId = DB::table('companies')->insertGetId([
            'name' => 'PT Autentik Karya Analitika',
            'city' => 'Semarang',
            'province' => 'Jawa Tengah'
        ]);

        $deptId = DB::table('departments')->insertGetId([
            'company_id' => $companyId,
            'name' => 'Engineering',
            'description' => 'Software & Hardware Division'
        ]);

        // 3b. Seed Positions
        $frontendPosId = DB::table('positions')->insertGetId([
            'department_id' => $deptId,
            'name' => 'Frontend Developer',
            'description' => 'Mengembangkan antarmuka pengguna berbasis web.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $backendPosId = DB::table('positions')->insertGetId([
            'department_id' => $deptId,
            'name' => 'Backend Developer',
            'description' => 'Mengembangkan arsitektur server, API, dan basis data.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $iotPosId = DB::table('positions')->insertGetId([
            'department_id' => $deptId,
            'name' => 'IoT Engineer',
            'description' => 'Merancang dan mengintegrasikan perangkat keras mikrokontroler.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $qaPosId = DB::table('positions')->insertGetId([
            'department_id' => $deptId,
            'name' => 'Quality Assurance (QA)',
            'description' => 'Melakukan pengujian mutu dan otomatisasi sistem perangkat lunak.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Seed Jobs
        DB::table('jobs')->insert([
            [
                'company_id' => $companyId,
                'department_id' => $deptId,
                'position_id' => $frontendPosId,
                'title' => 'Frontend Developer',
                'description' => 'Menguasai React JS, TailwindCSS.',
                'employment_type' => 'Full-time',
                'location' => 'Semarang',
                'salary_min' => 5000000,
                'salary_max' => 8000000,
                'quota' => 2,
                'deadline' => Carbon::now()->addDays(30),
                'status' => 'Open'
            ],
            [
                'company_id' => $companyId,
                'department_id' => $deptId,
                'position_id' => $iotPosId,
                'title' => 'IoT Engineer',
                'description' => 'Pengalaman dengan ESP32 dan Arduino.',
                'employment_type' => 'Contract',
                'location' => 'Remote',
                'salary_min' => 6000000,
                'salary_max' => 10000000,
                'quota' => 1,
                'deadline' => Carbon::now()->addDays(15),
                'status' => 'Open'
            ]
        ]);

        // 5. Seed Degrees & Majors
        DB::table('degrees')->insert([
            ['name' => 'SMA/SMK', 'rank' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'D3', 'rank' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'D4/S1', 'rank' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'S2', 'rank' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'S3', 'rank' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('majors')->insert([
            ['name' => 'Teknik Informatika', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sistem Informasi', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Teknik Komputer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Teknik Elektro', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manajemen', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Akuntansi', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 6. Seed DISC Master Data & Questions
        $this->call(DiscMasterSeeder::class);
        // $this->call(DiscQuestionSeeder::class);

        // 7. Seed PAPI Kostick Master Data & Questions
        $this->call(PapiKostickMasterSeeder::class);
        $this->call(PapiKostickQuestionSeeder::class);
    }
}

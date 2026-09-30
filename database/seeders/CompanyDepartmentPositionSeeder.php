<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyDepartmentPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Struktur Organisasi MIKA & AKA (Perusahaan -> Departemen -> Daftar Posisi)
        $organizations = [
            // -------------------------------------------------------------
            // 1. PT Mitra Karya Analitika (MIKA)
            // -------------------------------------------------------------
            'PT Mitra Karya Analitika' => [
                'city' => 'Semarang',
                'province' => 'Jawa Tengah',
                'departments' => [
                    'FA & OPERASIONAL' => [
                        'FA & Operational Manager',
                        'Finance & Accounting SPV',
                        'Finance & Accounting',
                        'Tax & Collection',
                        'Operational SPV',
                        'PPIC',
                        'Logistic',
                        'HRD SPV',
                        'HRD & Legal Staff',
                        'General Affair Staff',
                    ],
                    'PRODUK & MARKETING' => [
                        'Produk & Marketing Manager',
                        'Digital Marketing',
                        'Sales Office NFP',
                        'Product Development SPV',
                        'PS Distributor',
                        'PS Manufaktur',
                        'Marketing & Sales SPV',
                        'Marketing Admin',
                        'BE SG1 AS1 HBO',
                        'BE SG2 AS1 IBD',
                        'BE AS2 IBD SG4',
                        'BE AS2 IBD SG3',
                        'Maintenance & Service SPV',
                        'Service & Maintenance',
                    ],
                ],
            ],

            // -------------------------------------------------------------
            // 2. PT Autentik Karya Analitika (AKA)
            // -------------------------------------------------------------
            'PT Autentik Karya Analitika' => [
                'city' => 'Semarang',
                'province' => 'Jawa Tengah',
                'departments' => [
                    'PLANT / PABRIK' => [
                        'Plant Manager',
                        'PPIC & Procurement SPV',
                        'Purchase Staff',
                        'Warehouse Staff',
                        'PPIC Staff',
                        'Production SPV',
                        'Mainboard Staff',
                        'Assembly Staff',
                        'QA/QC SPV',
                        'QA/QC Staff',
                        'RND SPV',
                        'Embedded Engineer',
                        'Hardware Engineer',
                        'Product Design Engineer',
                    ],
                    'FIELD / LAPANGAN' => [
                        'Field Manager',
                        'Field SPV',
                        'Field Engineer',
                    ],
                    'OPERATIONAL' => [
                        'Operational Manager',
                        'Marketing SPV',
                        'Marketing Executive',
                        'Admin Marketing',
                        'HRD & GA SPV',
                        'Human Resource Development',
                        'General Affair',
                        'FA SPV',
                        'Finance & Collection',
                        'Accounting & Tax',
                    ],
                ],
            ],
        ];

        DB::transaction(function () use ($organizations) {
            foreach ($organizations as $companyName => $companyData) {
                // 1. Dapatkan atau Buat Perusahaan (Company)
                $company = Company::firstOrCreate(
                    ['name' => $companyName],
                    [
                        'city' => $companyData['city'] ?? 'Semarang',
                        'province' => $companyData['province'] ?? 'Jawa Tengah',
                    ]
                );

                foreach ($companyData['departments'] as $deptName => $positions) {
                    // 2. Dapatkan atau Buat Departemen (Department)
                    $department = Department::firstOrCreate(
                        [
                            'company_id' => $company->id,
                            'name' => $deptName,
                        ]
                    );

                    // 3. Daftarkan seluruh Posisi (Position) di bawah departemen
                    foreach ($positions as $posName) {
                        Position::firstOrCreate(
                            [
                                'department_id' => $department->id,
                                'name' => $posName,
                            ]
                        );
                    }
                }
            }
        });

        $this->command?->info('Seeder Company, Department, dan Position (MIKA & AKA) berhasil disiapkan.');
    }
}

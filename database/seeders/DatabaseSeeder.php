<?php

namespace Database\Seeders;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create standard demo accounts
        $admin = User::firstOrCreate(
            ['email' => 'admin@jajalmedical.com'],
            [
                'name' => 'Dr. Eleanor Vance (Admin)',
                'password' => Hash::make('AdminPassword123!'),
                'role' => 'Admin',
                'email_verified_at' => now(),
            ]
        );

        $viewer = User::firstOrCreate(
            ['email' => 'viewer@jajalmedical.com'],
            [
                'name' => 'Marcus Thorne (Viewer)',
                'password' => Hash::make('ViewerPassword123!'),
                'role' => 'Viewer',
                'email_verified_at' => now(),
            ]
        );

        // 2. Seed curated realistic demo cases (at least 20 cases)
        $curatedCases = [
            [
                'case_number' => 'CAS-10001',
                'patient_reference' => 'PT-9011',
                'surgeon_name' => 'Dr. Sophia Reyes',
                'implant_type' => 'Titanium Total Knee System',
                'status' => MedicalCase::STATUS_PLANNING,
                'priority' => MedicalCase::PRIORITY_HIGH,
                'surgery_date' => now()->addDays(5)->format('Y-m-d'),
            ],
            [
                'case_number' => 'CAS-10002',
                'patient_reference' => 'PT-9012',
                'surgeon_name' => 'Dr. David Chen',
                'implant_type' => 'Ceramic Acetabular Hip Cup',
                'status' => MedicalCase::STATUS_IN_REVIEW,
                'priority' => MedicalCase::PRIORITY_MEDIUM,
                'surgery_date' => now()->addDays(12)->format('Y-m-d'),
            ],
            [
                'case_number' => 'CAS-10003',
                'patient_reference' => 'PT-9013',
                'surgeon_name' => 'Dr. Sarah Jenkins',
                'implant_type' => 'Spinal Lumbar Fusion Cage',
                'status' => MedicalCase::STATUS_APPROVED,
                'priority' => MedicalCase::PRIORITY_HIGH,
                'surgery_date' => now()->addDays(3)->format('Y-m-d'),
            ],
            [
                'case_number' => 'CAS-10004',
                'patient_reference' => 'PT-9014',
                'surgeon_name' => 'Dr. Michael Larson',
                'implant_type' => 'Bioresorbable Cardiac Stent',
                'status' => MedicalCase::STATUS_COMPLETED,
                'priority' => MedicalCase::PRIORITY_HIGH,
                'surgery_date' => now()->subDays(4)->format('Y-m-d'),
            ],
            [
                'case_number' => 'CAS-10005',
                'patient_reference' => 'PT-9015',
                'surgeon_name' => 'Dr. Priya Patel',
                'implant_type' => '3D Cranial Titanium Mesh',
                'status' => MedicalCase::STATUS_PLANNING,
                'priority' => MedicalCase::PRIORITY_LOW,
                'surgery_date' => now()->addDays(20)->format('Y-m-d'),
            ],
            [
                'case_number' => 'CAS-10006',
                'patient_reference' => 'PT-9016',
                'surgeon_name' => 'Dr. Robert Garcia',
                'implant_type' => 'Reverse Shoulder Prosthesis',
                'status' => MedicalCase::STATUS_IN_REVIEW,
                'priority' => MedicalCase::PRIORITY_MEDIUM,
                'surgery_date' => now()->addDays(8)->format('Y-m-d'),
            ],
            [
                'case_number' => 'CAS-10007',
                'patient_reference' => 'PT-9017',
                'surgeon_name' => 'Dr. Emily Watson',
                'implant_type' => 'Distal Radius Plate',
                'status' => MedicalCase::STATUS_APPROVED,
                'priority' => MedicalCase::PRIORITY_LOW,
                'surgery_date' => now()->addDays(15)->format('Y-m-d'),
            ],
            [
                'case_number' => 'CAS-10008',
                'patient_reference' => 'PT-9018',
                'surgeon_name' => 'Dr. Alexander Wright',
                'implant_type' => 'Endovascular Aortic Graft',
                'status' => MedicalCase::STATUS_COMPLETED,
                'priority' => MedicalCase::PRIORITY_HIGH,
                'surgery_date' => now()->subDays(10)->format('Y-m-d'),
            ],
            [
                'case_number' => 'CAS-10009',
                'patient_reference' => 'PT-9019',
                'surgeon_name' => 'Dr. Hannah Kim',
                'implant_type' => 'Anterior Cervical Plate',
                'status' => MedicalCase::STATUS_DRAFT,
                'priority' => MedicalCase::PRIORITY_LOW,
                'surgery_date' => null,
            ],
            [
                'case_number' => 'CAS-10010',
                'patient_reference' => 'PT-9020',
                'surgeon_name' => 'Dr. David Chen',
                'implant_type' => 'Femoral Intramedullary Nail',
                'status' => MedicalCase::STATUS_CANCELLED,
                'priority' => MedicalCase::PRIORITY_MEDIUM,
                'surgery_date' => null,
            ],
        ];

        foreach ($curatedCases as $caseData) {
            MedicalCase::updateOrCreate(
                ['case_number' => $caseData['case_number']],
                array_merge($caseData, ['created_by' => $admin->id])
            );
        }

        // Additional 15 cases via factory to reach 25 total cases
        if (MedicalCase::count() < 25) {
            MedicalCase::factory(15)->create([
                'created_by' => $admin->id,
            ]);
        }
    }
}

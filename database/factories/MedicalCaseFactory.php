<?php

namespace Database\Factories;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalCase>
 */
class MedicalCaseFactory extends Factory
{
    protected $model = MedicalCase::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $implantTypes = [
            'Titanium Knee System',
            'Ceramic Hip Prosthesis',
            'Spinal Fusion Cage',
            'Bioresorbable Cardiac Stent',
            'Custom Cranial Mesh',
            'Shoulder Arthroplasty Kit',
            'Ankle Fixation Plate',
            'Endovascular Graft',
        ];

        return [
            'case_number' => 'CAS-' . fake()->unique()->numerify('#####'),
            'patient_reference' => 'PT-' . fake()->numerify('####'),
            'surgeon_name' => 'Dr. ' . fake()->name(),
            'implant_type' => fake()->randomElement($implantTypes),
            'status' => fake()->randomElement(MedicalCase::STATUSES),
            'priority' => fake()->randomElement(MedicalCase::PRIORITIES),
            'surgery_date' => fake()->optional(0.85)->dateTimeBetween('+1 days', '+60 days')?->format('Y-m-d'),
            'created_by' => User::factory(),
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAssessment;
use App\Models\NutritionalRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NutriSightLongitudinalDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create School Years
        $sy2023 = Program::create([
            'school_year' => '2023-2024',
            'start_date' => '2023-06-05',
            'end_date' => '2024-03-29',
            'is_active' => false,
        ]);

        $sy2024 = Program::create([
            'school_year' => '2024-2025',
            'start_date' => '2024-06-03',
            'end_date' => '2025-03-28',
            'is_active' => false,
        ]);

        $sy2025 = Program::create([
            'school_year' => '2025-2026',
            'start_date' => '2025-06-02',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);

        $schoolYears = [$sy2023, $sy2024, $sy2025];

        // 2. Create Admins
        User::firstOrCreate(
            ['email' => 'superadmin@nutrisight.test'],
            [
                'name' => 'Super Admin User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPER_ADMIN,
                'sex' => 'Male',
                'birthdate' => '1985-05-15',
                'position' => 'Master Teacher II',
                'deped_id' => 'DEPED-SUPER-001',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@nutrisight.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'sex' => 'Female',
                'birthdate' => '1988-08-20',
                'position' => 'Master Teacher I',
                'deped_id' => 'DEPED-ADM-001',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin2@nutrisight.test'],
            [
                'name' => 'Maria Santos (Admin)',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'sex' => 'Female',
                'birthdate' => '1990-03-12',
                'position' => 'Teacher III',
                'deped_id' => 'DEPED-ADM-002',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin3@nutrisight.test'],
            [
                'name' => 'Juan Dela Cruz (Admin)',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'sex' => 'Male',
                'birthdate' => '1987-11-04',
                'position' => 'Master Teacher I',
                'deped_id' => 'DEPED-ADM-003',
                'is_active' => true,
            ]
        );

        // 3. Create 14 Encoders (Advisers)
        $encoders = [];
        for ($i = 1; $i <= 14; $i++) {
            $encoders[] = User::firstOrCreate(
                ['email' => "encoder{$i}@nutrisight.test"],
                [
                    'name' => "Teacher Encoder {$i}",
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_ENCODER,
                    'sex' => $i % 2 === 0 ? 'Female' : 'Male',
                    'birthdate' => '1992-01-01',
                    'position' => 'Teacher II',
                    'deped_id' => sprintf('DEPED-ENC-%03d', $i),
                    'is_active' => true,
                ]
            );
        }

        $gradeLevels = ['Kinder', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $sectionNames = ['A', 'B'];

        // 4. Set up sections and students across the 3 school years
        $studentIndex = 0;
        foreach ($schoolYears as $syIndex => $sy) {
            $sySections = [];
            $encoderIndex = 0;
            foreach ($gradeLevels as $grade) {
                foreach ($sectionNames as $secName) {
                    $encoder = $encoders[$encoderIndex % count($encoders)];
                    $encoderIndex++;

                    $sySections[] = Section::create([
                        'school_year_id' => $sy->id,
                        'grade_level' => $grade,
                        'name' => $secName,
                        'adviser_id' => $encoder->id,
                    ]);
                }
            }

            $assessmentsBatch = [];
            $nutritionalRecordsBatch = [];

            foreach ($sySections as $section) {
                for ($k = 0; $k < 5; $k++) {
                    $studentIndex++;
                    $gender = $studentIndex % 2 === 0 ? 'Female' : 'Male';
                    $weight = round(rand(160, 450) / 10, 1);
                    $height = round(rand(105, 150) / 100, 2);
                    $bmi = round($weight / ($height ** 2), 2);
                    $status = $this->nutritionStatusForBmi($bmi);
                    $isWasted = in_array($status, ['Wasted', 'Severely Wasted']);

                    $student = Student::create([
                        'school_year_id' => $sy->id,
                        'student_number' => sprintf('MBES-%s-%04d', str_replace('-', '', $sy->school_year), $studentIndex),
                        'first_name' => fake()->firstName($gender === 'Male' ? 'male' : 'female'),
                        'last_name' => fake()->lastName(),
                        'gender' => $gender,
                        'birth_date' => fake()->dateTimeBetween('-13 years', '-5 years')->format('Y-m-d'),
                        'grade_level' => $section->grade_level,
                        'section' => $section->name,
                        'section_id' => $section->id,
                        'guardian_name' => fake()->name(),
                        'guardian_contact' => fake()->numerify('09#########'),
                        'address' => fake()->city() . ', Misamis Oriental',
                        'is_active' => true,
                        'is_permitted' => $isWasted,
                        'parent_approval_status' => $isWasted ? 'approved' : null,
                    ]);

                    // Nutritional Record (baseline)
                    $nutritionalRecordsBatch[] = [
                        'school_year_id' => $sy->id,
                        'student_id' => $student->id,
                        'type' => 'baseline',
                        'weight' => $weight,
                        'height' => $height,
                        'bmi' => $bmi,
                        'bmi_category' => $status,
                        'height_for_age' => 'Normal',
                        'remarks' => match ($status) {
                            'Severely Wasted' => 'Needs immediate nutritional intervention.',
                            'Wasted' => 'Prioritize daily feeding and monitoring.',
                            'Overweight' => 'Monitor meal portions and physical activity.',
                            'Obese' => 'Coordinate with parents for follow-up guidance.',
                            default => 'Stable and within expected range.',
                        },
                        'recorded_at' => Carbon::parse("202" . ($syIndex + 3) . "-06-15")->toDateTimeString(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Assessments for Term 1, 2, 3
                    foreach (['1' => '10-01', '2' => '12-01', '3' => '02-01'] as $term => $md) {
                        $assessmentsBatch[] = [
                            'school_year_id' => $sy->id,
                            'student_id' => $student->id,
                            'assessed_by_user_id' => $section->adviser_id,
                            'assessment_date' => Carbon::parse("202" . ($syIndex + 3) . "-" . $md)->toDateString(),
                            'weight_kg' => $weight,
                            'height_m' => $height,
                            'bmi' => $bmi,
                            'nutritional_status' => $status,
                            'remarks' => "Term {$term} longitudinal assessment recorded.",
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            if (!empty($nutritionalRecordsBatch)) {
                foreach (array_chunk($nutritionalRecordsBatch, 50) as $chunk) {
                    NutritionalRecord::insert($chunk);
                }
            }
            if (!empty($assessmentsBatch)) {
                foreach (array_chunk($assessmentsBatch, 50) as $chunk) {
                    StudentAssessment::insert($chunk);
                }
            }
        }
    }

    private function nutritionStatusForBmi(float $bmi): string
    {
        return match (true) {
            $bmi < 14.0 => 'Severely Wasted',
            $bmi < 16.0 => 'Wasted',
            $bmi < 20.0 => 'Normal',
            $bmi < 24.0 => 'Overweight',
            default => 'Obese',
        };
    }
}

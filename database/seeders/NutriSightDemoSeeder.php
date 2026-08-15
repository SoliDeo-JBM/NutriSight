<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\FeedingRecord;
use App\Models\Student;
use App\Models\StudentAssessment;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class NutriSightDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstWhere('role', User::ROLE_ADMIN);
        $encoder = User::firstWhere('role', User::ROLE_ENCODER);

        $schoolSections = [
            ['grade_level' => 'Grade 1', 'section' => 'A'],
            ['grade_level' => 'Grade 1', 'section' => 'B'],
            ['grade_level' => 'Grade 2', 'section' => 'A'],
            ['grade_level' => 'Grade 2', 'section' => 'B'],
            ['grade_level' => 'Grade 3', 'section' => 'A'],
            ['grade_level' => 'Grade 3', 'section' => 'B'],
            ['grade_level' => 'Grade 4', 'section' => 'A'],
            ['grade_level' => 'Grade 4', 'section' => 'B'],
            ['grade_level' => 'Grade 5', 'section' => 'A'],
            ['grade_level' => 'Grade 5', 'section' => 'B'],
            ['grade_level' => 'Grade 6', 'section' => 'A'],
            ['grade_level' => 'Grade 6', 'section' => 'B'],
        ];

        $students = collect();

        for ($index = 1; $index <= 36; $index++) {
            $section = $schoolSections[array_rand($schoolSections)];
            $gender = fake()->randomElement(['Male', 'Female']);
            $birthDate = fake()->dateTimeBetween('-13 years', '-6 years');
            $weight = fake()->randomFloat(1, 17.5, 48.0);
            $height = fake()->randomFloat(2, 1.05, 1.54);
            $bmi = round($weight / ($height ** 2), 2);

            $student = Student::create([
                'student_number' => sprintf('MBES-%04d', $index),
                'first_name' => fake()->firstName($gender === 'Male' ? 'male' : 'female'),
                'last_name' => fake()->lastName(),
                'gender' => $gender,
                'birth_date' => $birthDate->format('Y-m-d'),
                'grade_level' => $section['grade_level'],
                'section' => $section['section'],
                'guardian_name' => fake()->name(),
                'guardian_contact' => fake()->numerify('09#########'),
                'address' => fake()->city() . ', Misamis Oriental',
                'is_active' => true,
            ]);

            $status = $this->nutritionStatusForBmi($bmi);

            StudentAssessment::create([
                'student_id' => $student->id,
                'assessed_by_user_id' => $admin?->id,
                'assessment_date' => now()->subDays(fake()->numberBetween(0, 10))->toDateString(),
                'weight_kg' => $weight,
                'height_m' => $height,
                'bmi' => $bmi,
                'nutritional_status' => $status,
                'remarks' => match ($status) {
                    'Severely Wasted' => 'Needs immediate nutritional intervention.',
                    'Wasted' => 'Prioritize daily feeding and monitoring.',
                    'Overweight' => 'Monitor meal portions and physical activity.',
                    'Obese' => 'Coordinate with parents for follow-up guidance.',
                    default => 'Stable and within expected range.',
                },
            ]);

            $students->push($student);
        }

        $feedingMeals = [
            'Rice porridge with egg',
            'Chicken arroz caldo',
            'Banana and peanut butter sandwich',
            'Vegetable soup with bread',
            'Milk and oatmeal bowl',
            'Puto with fortified soy drink',
        ];

        $attendanceWindow = CarbonPeriod::create(now()->subDays(14), now()->subDay());

        foreach ($attendanceWindow as $date) {
            foreach ($students as $student) {
                $attendanceRoll = fake()->numberBetween(1, 100);
                $attendanceStatus = match (true) {
                    $attendanceRoll <= 84 => 'Present',
                    $attendanceRoll <= 92 => 'Late',
                    default => 'Absent',
                };

                AttendanceRecord::create([
                    'student_id' => $student->id,
                    'recorded_by_user_id' => $encoder?->id,
                    'attendance_date' => $date->toDateString(),
                    'status' => $attendanceStatus,
                    'notes' => $attendanceStatus === 'Absent' ? 'Home visit follow-up scheduled.' : null,
                ]);

                if ($attendanceStatus !== 'Absent' && fake()->boolean(82)) {
                    FeedingRecord::create([
                        'student_id' => $student->id,
                        'recorded_by_user_id' => $encoder?->id,
                        'feeding_date' => $date->toDateString(),
                        'meal_type' => fake()->randomElement(['Breakfast', 'Snack', 'Lunch']),
                        'meal_served' => fake()->randomElement($feedingMeals),
                        'photo_path' => null,
                        'remarks' => fake()->randomElement([
                            'Finished serving within the schedule.',
                            'Ate well and stayed for documentation.',
                            'Parent notification sent for transparency.',
                            'Recorded after QR attendance scan.',
                        ]),
                    ]);
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
<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\NutritionMeasurement;
use App\Models\ReportPeriod;
use App\Models\SbfpParticipant;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaselineJune2026TestSeeder extends Seeder
{
    public function run(): void
    {
        $schoolYear = SchoolYear::where('year', '2026-2027')->firstOrFail();
        $measurementDate = '2026-06-15 09:00:00';
        $categories = ['Severely Wasted', 'Wasted', 'Normal', 'Overweight', 'Obese'];
        $hfaCategories = ['Severely Stunted', 'Stunted', 'Normal', 'Tall'];

        DB::transaction(function () use ($schoolYear, $measurementDate, $categories, $hfaCategories) {
            ReportPeriod::firstOrCreate(
                ['school_year_id' => $schoolYear->id, 'measurement_period' => 'baseline', 'month' => 6],
                ['name' => 'June']
            );

            for ($number = 1; $number <= 50; $number++) {
                $gradeLevel = ($number - 1) % 8;
                $sex = $number % 2 === 0 ? 'Female' : 'Male';
                $lrn = 20260000000 + $number;
                $weight = 18 + (($number * 3) % 25) / 10;
                $height = 105 + (($number * 7) % 55);
                $bmi = round($weight / (($height / 100) ** 2), 2);
                $category = $categories[($number - 1) % count($categories)];
                $hfa = $hfaCategories[($number - 1) % count($hfaCategories)];

                $student = Student::updateOrCreate(
                    ['lrn' => $lrn],
                    [
                        'first_name' => 'JuneTest' . $number,
                        'last_name' => 'BaselineStudent',
                        'middle_name' => null,
                        'name_extension' => null,
                        'sex' => $sex,
                        'birth_date' => sprintf('%04d-%02d-%02d', 2014 + ($number % 5), (($number - 1) % 12) + 1, (($number - 1) % 28) + 1),
                        'guardian_name' => 'Test Guardian ' . $number,
                        'guardian_email' => 'june-test-' . $number . '@example.test',
                        'address' => 'Marisol Bliss Elementary School Test Address',
                    ]
                );

                $enrollment = Enrollment::updateOrCreate(
                    ['student_id' => $student->id, 'school_year_id' => $schoolYear->id],
                    ['grade_level' => $gradeLevel, 'section' => 'A', 'status' => 'enrolled']
                );

                $participant = SbfpParticipant::updateOrCreate(
                    ['enrollment_id' => $enrollment->id],
                    ['parent_consent' => 'approved', 'disapproval_reason' => null]
                );

                $measurement = NutritionMeasurement::where('sbfp_participant_id', $participant->id)
                    ->where('measurement_period', 'baseline')
                    ->whereDate('created_at', '2026-06-15')
                    ->first();

                $measurementData = [
                    'height' => (string) $height,
                    'weight' => (string) $weight,
                    'bmi' => $bmi,
                    'bmi_category' => $category,
                    'hfa' => $hfa,
                    'remarks' => 'June 2026 baseline test data',
                ];

                if ($measurement) {
                    $measurement->update($measurementData);
                } else {
                    $measurement = NutritionMeasurement::create(array_merge(
                        ['sbfp_participant_id' => $participant->id, 'measurement_period' => 'baseline'],
                        $measurementData
                    ));
                    $measurement->created_at = $measurementDate;
                    $measurement->updated_at = $measurementDate;
                    $measurement->saveQuietly();
                }
            }
        });

        $this->command?->info('Created or updated 50 June 2026 baseline test students for SY 2026-2027.');
    }
}

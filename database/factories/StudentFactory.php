<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        $gender = fake()->randomElement(['Male', 'Female']);
        $section = fake()->randomElement(['A', 'B']);
        $gradeLevel = fake()->randomElement(['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6']);

        return [
            'student_number' => 'MBES-' . fake()->unique()->numerify('####'),
            'first_name' => fake()->firstName($gender === 'Male' ? 'male' : 'female'),
            'last_name' => fake()->lastName(),
            'gender' => $gender,
            'birth_date' => fake()->dateTimeBetween('-13 years', '-6 years')->format('Y-m-d'),
            'grade_level' => $gradeLevel,
            'section' => $section,
            'guardian_name' => fake()->name(),
            'guardian_contact' => fake()->numerify('09#########'),
            'address' => fake()->streetAddress() . ', Misamis Oriental',
            'is_active' => true,
        ];
    }
}
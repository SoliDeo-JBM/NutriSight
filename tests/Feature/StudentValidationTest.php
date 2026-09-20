<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentValidationTest extends TestCase
{
    use RefreshDatabase;

    private function validStudentPayload(): array
    {
        return [
            'lrn' => '136542100012',
            'last_name' => 'Dela Cruz',
            'first_name' => 'Juan',
            'birth_date' => '2015-01-01',
            'sex' => 'Male',
            'grade_level' => 1,
            'section' => 'Mabini',
            'weight' => '18.5',
            'height' => '115',
            'guardian_name' => 'Maria Dela Cruz',
            'guardian_contact' => '09171234567',
            'guardian_email' => 'guardian@example.com',
            'address' => '1 Main Street',
        ];
    }

    public function test_student_creation_rejects_non_numeric_lrn_and_measurements(): void
    {
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 1,
            'advisory_section' => 'Mabini',
        ]);

        $response = $this->actingAs($encoder)->from(route('encoder.students.create'))->post(
            route('encoder.students.store'),
            array_merge($this->validStudentPayload(), [
                'lrn' => '1365A2100012',
                'weight' => 'eighteen',
                'height' => 'one hundred fifteen',
            ])
        );

        $response->assertRedirect(route('encoder.students.create'));
        $response->assertSessionHasErrors(['lrn', 'weight', 'height']);
        $this->assertDatabaseCount('students', 0);
    }

    public function test_student_creation_requires_guardian_phone_to_have_eleven_or_twelve_digits(): void
    {
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 1,
            'advisory_section' => 'Mabini',
        ]);

        foreach (['0917123456', '0917123456789'] as $phone) {
            $response = $this->actingAs($encoder)->from(route('encoder.students.create'))->post(
                route('encoder.students.store'),
                array_merge($this->validStudentPayload(), ['guardian_contact' => $phone])
            );

            $response->assertRedirect(route('encoder.students.create'));
            $response->assertSessionHasErrors('guardian_contact');
        }

        $this->assertDatabaseCount('students', 0);
    }
}

<?php

namespace Tests\Feature;

use App\Mail\SbfpAutomaticEnrollmentNotice;
use App\Models\SchoolYear;
use App\Models\User;
use App\Models\PhilippineBarangay;
use App\Models\PhilippineMunicipality;
use App\Models\PhilippineProvince;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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

    public function test_student_creation_requires_at_least_one_parent_or_guardian_name(): void
    {
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 1,
            'advisory_section' => 'Mabini',
        ]);

        $response = $this->actingAs($encoder)->from(route('encoder.students.create'))->post(
            route('encoder.students.store'),
            array_merge($this->validStudentPayload(), [
                'father_name' => '',
                'mother_name' => '',
                'guardian_name' => '',
            ])
        );

        $response->assertRedirect(route('encoder.students.create'));
        $response->assertSessionHasErrors(['father_name', 'mother_name', 'guardian_name']);
        $this->assertDatabaseCount('students', 0);
    }

    public function test_automatically_approved_grade_queues_an_informational_parent_notice(): void
    {
        Mail::fake();
        SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 1,
            'advisory_section' => 'Mabini',
        ]);

        $response = $this->actingAs($encoder)->post(
            route('encoder.students.store'),
            $this->validStudentPayload()
        );

        $response->assertRedirect(route('encoder.students.index'));
        Mail::assertQueued(SbfpAutomaticEnrollmentNotice::class, function ($mail) {
            return $mail->student->first_name === 'Juan'
                && $mail->gradeLevel === 1;
        });
    }

    public function test_student_creation_creates_an_enrollment_address_snapshot(): void
    {
        SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);
        PhilippineProvince::create(['code' => '01', 'name' => 'Province']);
        PhilippineMunicipality::create(['code' => '0101', 'name' => 'Municipality', 'province_code' => '01']);
        PhilippineBarangay::create(['code' => '010101', 'name' => 'Barangay', 'municipality_code' => '0101', 'province_code' => '01']);
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 1,
            'advisory_section' => 'Mabini',
        ]);

        $response = $this->actingAs($encoder)->post(
            route('encoder.students.store'),
            array_merge($this->validStudentPayload(), [
                'house_number' => '12',
                'street' => 'Rizal Street',
                'purok' => 'Purok 1',
                'province_code' => '01',
                'municipality_code' => '0101',
                'barangay_code' => '010101',
            ])
        );

        $response->assertRedirect(route('encoder.students.index'));
        $this->assertDatabaseHas('enrollment_addresses', [
            'house_number' => '12',
            'street' => 'Rizal Street',
            'province_code' => '01',
            'municipality_code' => '0101',
            'barangay_code' => '010101',
        ]);
    }
}

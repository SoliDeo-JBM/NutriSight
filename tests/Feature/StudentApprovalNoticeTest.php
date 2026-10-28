<?php

namespace Tests\Feature;

use App\Mail\SbfpManualEnrollmentNotice;
use App\Models\Enrollment;
use App\Models\NutritionMeasurement;
use App\Models\SchoolYear;
use App\Models\SbfpParticipant;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StudentApprovalNoticeTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_approval_sends_one_notice_only_when_consent_changes_to_approved(): void
    {
        [$admin, $student] = $this->createParticipant(2, null);
        $participant = $student->enrollments()->first()->sbfpParticipant;
        $measurement = NutritionMeasurement::create([
            'sbfp_participant_id' => $participant->id,
            'height' => '115',
            'weight' => '18.5',
            'bmi' => 14.0,
            'bmi_category' => 'Wasted',
            'hfa' => 'Normal',
            'measurement_period' => 'baseline',
        ]);
        Mail::fake();

        $this->actingAs($admin)->patch(route('encoder.students.approval', $student), [
            'parent_consent' => 'approved',
        ])->assertRedirect();

        $this->actingAs($admin)->patch(route('encoder.students.approval', $student), [
            'parent_consent' => 'approved',
        ])->assertRedirect();

        Mail::assertSentTimes(SbfpManualEnrollmentNotice::class, 1);
        Mail::assertSent(SbfpManualEnrollmentNotice::class, fn ($mail) => $mail->student->is($student)
            && $mail->measurement?->is($measurement));

        $body = (new SbfpManualEnrollmentNotice($student, 2, $measurement))->render();
        $this->assertStringContainsString('The school has approved', $body);
        $this->assertStringContainsString('Juan Dela Cruz', $body);
        $this->assertStringContainsString('18.50 kg', $body);
        $this->assertStringContainsString('115.00 cm', $body);
        $this->assertStringContainsString('14.00', $body);
        $this->assertStringContainsString('Wasted', $body);
    }

    public function test_bulk_approval_sends_a_notice_for_the_approval_transition(): void
    {
        [$admin, $student] = $this->createParticipant(2, null);
        Mail::fake();

        $this->actingAs($admin)->patch(route('encoder.students.approval.bulk'), [
            'approvals' => [[
                'student_id' => $student->id,
                'parent_consent' => 'approved',
            ]],
        ])->assertRedirect();

        Mail::assertSent(SbfpManualEnrollmentNotice::class, fn ($mail) => $mail->student->is($student));
        $body = (new SbfpManualEnrollmentNotice($student, 2))->render();
        $this->assertStringContainsString('Baseline measurements were not available', $body);
    }

    public function test_automatic_grade_approval_does_not_send_a_manual_approval_notice(): void
    {
        [$admin, $student] = $this->createParticipant(1, 'approved');
        Mail::fake();

        $this->actingAs($admin)->patch(route('encoder.students.approval', $student), [
            'parent_consent' => 'approved',
        ])->assertRedirect();

        Mail::assertNothingSent();
    }

    private function createParticipant(int $gradeLevel, ?string $consent): array
    {
        $schoolYear = SchoolYear::create([
            'year' => '2026-2027',
            'start_date' => '2026-06-01',
            'end_date' => '2027-03-31',
            'is_active' => true,
        ]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => $gradeLevel,
            'advisory_section' => 'A',
        ]);
        $student = Student::create([
            'lrn' => '136542100051',
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'sex' => 'Male',
            'birth_date' => '2015-01-01',
            'guardian_name' => 'Maria Dela Cruz',
            'guardian_email' => 'guardian@example.com',
            'guardian_contact' => '09171234567',
            'address' => '1 Main Street',
        ]);
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'grade_level' => $gradeLevel,
            'section' => 'A',
            'status' => Enrollment::STATUS_ENROLLED,
        ]);
        SbfpParticipant::create([
            'enrollment_id' => $enrollment->id,
            'parent_consent' => $consent,
        ]);

        return [$admin, $student];
    }
}
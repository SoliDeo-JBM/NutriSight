<?php

namespace Tests\Feature;

use App\Mail\SbfpParentApprovalRequest;
use App\Mail\FeedingDayNotice;
use App\Models\Enrollment;
use App\Models\NutritionMeasurement;
use App\Models\SchoolYear;
use App\Models\SbfpParticipant;
use App\Models\Student;
use App\Services\SbfpParentApprovalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SbfpParentApprovalNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_eligible_participant_receives_an_informational_notification_without_approval_actions(): void
    {
        $schoolYear = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);
        $student = Student::create([
            'lrn' => 136542100012,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'name_extension' => null,
            'middle_name' => null,
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
            'grade_level' => 2,
            'section' => 'A',
            'status' => Enrollment::STATUS_ENROLLED,
        ]);
        $participant = SbfpParticipant::create([
            'enrollment_id' => $enrollment->id,
            'parent_consent' => null,
        ]);
        $measurement = NutritionMeasurement::create([
            'sbfp_participant_id' => $participant->id,
            'height' => '115',
            'weight' => '18.5',
            'bmi' => 14.0,
            'bmi_category' => 'Wasted',
            'hfa' => 'Normal',
            'measurement_period' => 'baseline',
        ]);

        $request = app(SbfpParentApprovalService::class)->syncBaseline($participant, $measurement);

        $this->assertNotNull($request);

        $body = (new SbfpParentApprovalRequest($request))->render();

        $this->assertStringContainsString('now part of the School-Based Feeding Program', $body);
        $this->assertStringContainsString('18.50 kg', $body);
        $this->assertStringContainsString('115.00 cm', $body);
        $this->assertStringContainsString('14.00', $body);
        $this->assertStringContainsString('Republic Act No. 10173', $body);
        $this->assertStringContainsString('Data Privacy Act of 2012', $body);
        $this->assertStringNotContainsString('parent.sbfp.approval', $body);
        $this->assertStringNotContainsString('Review and Respond', $body);
        $this->assertStringNotContainsString('Submit decision', $body);
    }

    public function test_feeding_day_notice_includes_the_privacy_notice(): void
    {
        $student = new Student([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
        ]);

        $notice = new FeedingDayNotice($student, 'Champorado', '2026-09-25', null, Carbon::parse('2026-09-25 10:42:00'), 'afternoon');
        $body = $notice->render();

        $this->assertStringContainsString('Republic Act No. 10173', $body);
        $this->assertStringContainsString('Data Privacy Act of 2012', $body);
        $this->assertStringContainsString('School-Based Feeding Program coordination', $body);
        $this->assertStringContainsString('Afternoon', $body);
        $this->assertStringContainsString('Scanned at:', $body);
        $this->assertStringContainsString('September 25, 2026 at 10:42 AM', $body);
        $this->assertStringContainsString('SBFP Afternoon attendance notice', $notice->envelope()->subject);

        $manualBody = (new FeedingDayNotice($student, 'Champorado', '2026-09-25'))->render();

        $this->assertStringNotContainsString('Scanned at:', $manualBody);
    }
}

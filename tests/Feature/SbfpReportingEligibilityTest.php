<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\NutritionMeasurement;
use App\Models\ReportPeriod;
use App\Models\ReportPeriodRow;
use App\Models\SchoolYear;
use App\Models\SbfpParticipant;
use App\Models\Student;
use App\Models\StudentAttendanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SbfpReportingEligibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_and_assessment_reports_only_include_active_approved_at_risk_advisory_students(): void
    {
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 4,
            'advisory_section' => 'Mabini',
        ]);
        $schoolYear = SchoolYear::create([
            'year' => '2026-2027',
            'start_date' => '2026-06-01',
            'end_date' => '2027-03-31',
            'is_active' => true,
        ]);
        $date = now()->toDateString();
        $recordedBy = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->createParticipant($schoolYear, $recordedBy, '136542100001', 'Eligible', 'approved', 'Wasted', 'enrolled', 'Mabini', $date);
        $this->createParticipant($schoolYear, $recordedBy, '136542100002', 'Normalized', 'approved', 'Normal', 'enrolled', 'Mabini', $date, attendanceStatus: 'absent');
        $this->createParticipant($schoolYear, $recordedBy, '136542100003', 'Disapproved', 'disapproved', 'Wasted', 'enrolled', 'Mabini', $date, attendanceStatus: 'absent');
        $this->createParticipant($schoolYear, $recordedBy, '136542100004', 'Withdrawn', 'approved', 'Wasted', 'withdrawn', 'Mabini', $date, attendanceStatus: 'absent');
        $this->createParticipant($schoolYear, $recordedBy, '136542100005', 'OtherSection', 'approved', 'Wasted', 'enrolled', 'Rizal', $date);
        $this->createParticipant($schoolYear, $recordedBy, '136542100006', 'AutoApproved', 'approved', 'Normal', 'enrolled', 'A', $date, 1);
        $this->assertSame(3, StudentAttendanceRecord::whereDate('attendance_date', $date)->where('status', 'present')->count());
        $this->assertSame(1, StudentAttendanceRecord::whereDate('attendance_date', $date)->where('status', 'present')
            ->whereHas('sbfpParticipant', fn($participant) => $participant->where('parent_consent', 'approved')
                ->whereHas('enrollment', fn($enrollment) => $enrollment->where('school_year_id', $schoolYear->id)->activeApprovedSbfp()
                    ->where('grade_level', 4)->where('section', 'Mabini')))->count());

        $encoderDashboard = $this->actingAs($encoder)->get(route('encoder.dashboard'));
        $encoderDashboard->assertOk()
            ->assertViewHas('totalStudents', 3)
            ->assertViewHas('totalSbfp', 1)
            ->assertViewHas('attendanceDates', fn(array $dates): bool => $dates[6] === $date)
            ->assertViewHas('attendanceCounts', function (array $counts): bool {
                $this->assertSame(1, $counts[6], json_encode($counts));

                return true;
            });

        $attendanceResponse = $this->actingAs($encoder)->get(route('encoder.attendance.index', ['date' => $date]));
        $attendanceResponse->assertOk()
            ->assertSee('Eligible')
            ->assertDontSee('Normalized')
            ->assertDontSee('Disapproved')
            ->assertDontSee('Withdrawn')
            ->assertDontSee('OtherSection');

        $assessmentResponse = $this->actingAs($encoder)->get(route('encoder.reports.sbfp.assessment'));
        $assessmentResponse->assertOk()->assertViewHas('assessment', function (array $assessment): bool {
            return array_sum(array_column($assessment['participant_demographics'], 'count')) === 1
                && $assessment['malnourished'] === 1;
        });

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $adminDashboard = $this->actingAs($admin)->get(route('admin.dashboard'));
        $adminDashboard->assertOk()
            ->assertViewHas('totalSbfpStudents', 3)
            ->assertViewHas('sbfpStudents', function ($students): bool {
                $names = $students->pluck('first_name')->all();
                return in_array('Eligible', $names, true)
                    && in_array('OtherSection', $names, true)
                    && in_array('AutoApproved', $names, true)
                    && !in_array('Normalized', $names, true)
                    && !in_array('Disapproved', $names, true)
                    && !in_array('Withdrawn', $names, true);
            })
            ->assertViewHas('sectionAttendanceRates', fn(array $rates): bool => $rates[4] === 100.0);

        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $this->actingAs($superAdmin)->get(route('super-admin.dashboard'))
            ->assertOk()
            ->assertViewHas('totalSbfpStudents', 3);

        $adminSbfpList = $this->actingAs($admin)->get(route('admin.students.sbfp'));
        $adminSbfpList->assertOk()
            ->assertSee('Eligible')
            ->assertSee('OtherSection')
            ->assertSee('AutoApproved')
            ->assertDontSee('Normalized')
            ->assertDontSee('Disapproved')
            ->assertDontSee('Withdrawn');

        $superAdminSbfpList = $this->actingAs($superAdmin)->get(route('super-admin.students.sbfp'));
        $superAdminSbfpList->assertOk()
            ->assertSee('Eligible')
            ->assertSee('OtherSection')
            ->assertSee('AutoApproved')
            ->assertDontSee('Normalized')
            ->assertDontSee('Disapproved')
            ->assertDontSee('Withdrawn');

        $adminAttendanceResponse = $this->actingAs($admin)->get(route('admin.attendance.index', ['date' => $date]));
        $adminAttendanceResponse->assertOk()
            ->assertSee('OtherSection')
            ->assertSee('AutoApproved')
            ->assertDontSee('Normalized')
            ->assertDontSee('Disapproved')
            ->assertDontSee('Withdrawn');

        $adminAssessmentResponse = $this->actingAs($admin)->get(route('admin.reports.sbfp.assessment'));
        $adminAssessmentResponse->assertOk()->assertViewHas('assessment', function (array $assessment): bool {
            return array_sum(array_column($assessment['participant_demographics'], 'count')) === 3
                && $assessment['malnourished'] === 2;
        });

        $period = ReportPeriod::create([
            'school_year_id' => $schoolYear->id,
            'name' => 'September',
            'month' => 9,
            'measurement_period' => 'baseline',
        ]);
        $this->actingAs($admin)->get(route('admin.reports.sbfp.annual.period', $period))->assertOk();
        $this->assertSame(2, (int) ReportPeriodRow::where('report_period_id', $period->id)
            ->where('grade_level', 4)
            ->sum('enrollment'));
    }

    private function createParticipant(
        SchoolYear $schoolYear,
        User $recordedBy,
        string $lrn,
        string $firstName,
        string $consent,
        string $bmiCategory,
        string $enrollmentStatus,
        string $section,
        string $date,
        int $gradeLevel = 4,
        string $attendanceStatus = 'present',
    ): void {
        $student = Student::create([
            'lrn' => $lrn,
            'first_name' => $firstName,
            'last_name' => 'Test',
            'sex' => 'Male',
            'birth_date' => '2015-01-01',
            'guardian_name' => 'Guardian',
            'guardian_contact' => '09171234567',
            'guardian_email' => 'guardian@example.com',
            'address' => '1 Main Street',
        ]);
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'grade_level' => $gradeLevel,
            'section' => $section,
            'status' => $enrollmentStatus,
        ]);
        $participant = SbfpParticipant::create([
            'enrollment_id' => $enrollment->id,
            'parent_consent' => $consent,
        ]);
        NutritionMeasurement::create([
            'sbfp_participant_id' => $participant->id,
            'height' => 120,
            'weight' => 20,
            'bmi' => 13.9,
            'bmi_category' => $bmiCategory,
            'hfa' => 'Normal',
            'measurement_period' => 'baseline',
        ]);
        StudentAttendanceRecord::create([
            'sbfp_participant_id' => $participant->id,
            'recorded_by_user_id' => $recordedBy->id,
            'attendance_date' => $date,
            'meal_period' => StudentAttendanceRecord::PERIOD_MORNING,
            'status' => $attendanceStatus,
        ]);
    }
}

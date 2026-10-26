<?php

namespace Tests\Feature;

use App\Models\AttendanceReportMonth;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\SbfpParticipant;
use App\Models\Student;
use App\Models\StudentAttendanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EncoderReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_encoder_dashboard_and_attendance_report_are_advisory_scoped(): void
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
        $month = AttendanceReportMonth::create([
            'school_year_id' => $schoolYear->id,
            'month' => 9,
        ]);

        $this->actingAs($encoder)
            ->get(route('encoder.dashboard'))
            ->assertOk()
            ->assertSee(route('encoder.reports.sbfp.attendance'))
            ->assertSee(route('encoder.reports.sbfp.assessment'));

        $this->actingAs($encoder)
            ->get(route('encoder.reports.sbfp.attendance'))
            ->assertOk()
            ->assertSee('September')
            ->assertSee('Grade 4 - Section Mabini');

        $this->actingAs($encoder)
            ->get(route('encoder.reports.sbfp.attendance.month', $month))
            ->assertOk()
            ->assertSee('September Attendance')
            ->assertSee(route('encoder.reports.sbfp.attendance.month.pdf', $month))
            ->assertDontSee(route('admin.reports.sbfp.attendance.month.pdf', $month));
    }

    public function test_attendance_pdf_loads_only_attendance_records_for_the_requested_month(): void
    {
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 1,
            'advisory_section' => 'Mabini',
        ]);
        $schoolYear = SchoolYear::create([
            'year' => '2026-2027',
            'start_date' => '2026-06-01',
            'end_date' => '2027-03-31',
            'is_active' => true,
        ]);
        $month = AttendanceReportMonth::create([
            'school_year_id' => $schoolYear->id,
            'month' => 1,
        ]);
        $student = Student::create([
            'lrn' => 123456789012,
            'first_name' => 'Sample',
            'last_name' => 'Learner',
            'sex' => 'Female',
            'birth_date' => '2018-01-01',
            'guardian_name' => 'Guardian',
            'guardian_email' => 'guardian@example.test',
            'address' => 'Sample address',
        ]);
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'grade_level' => 1,
            'section' => 'Mabini',
            'status' => Enrollment::STATUS_ENROLLED,
        ]);
        $participant = SbfpParticipant::create([
            'enrollment_id' => $enrollment->id,
            'parent_consent' => 'approved',
        ]);
        StudentAttendanceRecord::create([
            'sbfp_participant_id' => $participant->id,
            'recorded_by_user_id' => $encoder->id,
            'attendance_date' => '2027-01-01',
            'status' => 'present',
        ]);
        StudentAttendanceRecord::create([
            'sbfp_participant_id' => $participant->id,
            'recorded_by_user_id' => $encoder->id,
            'attendance_date' => '2026-12-31',
            'status' => 'present',
        ]);

        $attendanceQueries = [];
        DB::listen(function (QueryExecuted $query) use (&$attendanceQueries): void {
            if (str_contains($query->sql, 'student_attendance_records')) {
                $attendanceQueries[] = $query;
            }
        });

        $this->actingAs($encoder)
            ->get(route('encoder.reports.sbfp.attendance.month.pdf', $month))
            ->assertDownload('attendance-report.pdf');

        $scopedQuery = collect($attendanceQueries)->first(fn (QueryExecuted $query) => in_array('2027-01-01', $query->bindings, true)
            && in_array('2027-01-31', $query->bindings, true));
        $this->assertNotNull($scopedQuery, 'The attendance eager-load query should be scoped to the requested month.');
    }
}

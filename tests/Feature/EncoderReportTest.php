<?php

namespace Tests\Feature;

use App\Models\AttendanceReportMonth;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}

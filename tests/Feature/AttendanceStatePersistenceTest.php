<?php

namespace Tests\Feature;

use App\Mail\FeedingDayNotice;
use App\Models\Enrollment;
use App\Models\MealPlan;
use App\Models\SchoolYear;
use App\Models\SbfpParticipant;
use App\Models\Student;
use App\Models\StudentAttendanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AttendanceStatePersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_scan_sends_the_attendance_notice_without_queueing(): void
    {
        $schoolYear = $this->createActiveSchoolYear();
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 2,
            'advisory_section' => 'A',
        ]);
        $student = $this->createApprovedStudent($schoolYear, '136542100041');
        MealPlan::create([
            'meal_date' => Carbon::today()->toDateString(),
            'meal_name' => 'Arroz caldo',
            'meal_period' => MealPlan::PERIOD_MORNING,
            'recorded_by_user_id' => $encoder->id,
        ]);
        Mail::fake();

        $response = $this->actingAs($encoder)->postJson(route('encoder.attendance.scan'), [
            'lrn' => $student->lrn,
            'meal_period' => MealPlan::PERIOD_MORNING,
        ]);

        $response->assertOk();
        Mail::assertSent(FeedingDayNotice::class, fn ($mail) => $mail->student->is($student));
        $this->assertDatabaseHas('student_attendance_records', [
            'sbfp_participant_id' => $student->enrollments()->first()->sbfpParticipant->id,
            'status' => 'present',
        ]);
    }

    public function test_qr_scan_restores_cleared_attendance_and_sends_only_one_notice(): void
    {
        $schoolYear = $this->createActiveSchoolYear();
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 2,
            'advisory_section' => 'A',
        ]);
        $student = $this->createApprovedStudent($schoolYear, '136542100043');
        $participant = $student->enrollments()->first()->sbfpParticipant;
        MealPlan::create([
            'meal_date' => Carbon::today()->toDateString(),
            'meal_name' => 'Arroz caldo',
            'meal_period' => MealPlan::PERIOD_MORNING,
            'recorded_by_user_id' => $encoder->id,
        ]);
        StudentAttendanceRecord::create([
            'sbfp_participant_id' => $participant->id,
            'recorded_by_user_id' => $encoder->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'meal_period' => MealPlan::PERIOD_MORNING,
            'status' => 'unmarked',
        ]);
        Mail::fake();

        $this->actingAs($encoder)->postJson(route('encoder.attendance.scan'), [
            'lrn' => $student->lrn,
            'meal_period' => MealPlan::PERIOD_MORNING,
        ])->assertOk();

        $this->actingAs($encoder)->postJson(route('encoder.attendance.scan'), [
            'lrn' => $student->lrn,
            'meal_period' => MealPlan::PERIOD_MORNING,
        ])->assertStatus(409);

        $records = StudentAttendanceRecord::query()
            ->where('sbfp_participant_id', $participant->id)
            ->whereDate('attendance_date', Carbon::today()->toDateString())
            ->where('meal_period', MealPlan::PERIOD_MORNING)
            ->get();
        $this->assertCount(1, $records);
        $this->assertSame('present', $records->first()->status);
        Mail::assertSentTimes(FeedingDayNotice::class, 1);
    }

    public function test_clear_and_qr_rescan_reconcile_duplicate_legacy_rows(): void
    {
        $schoolYear = $this->createActiveSchoolYear();
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 2,
            'advisory_section' => 'A',
        ]);
        $student = $this->createApprovedStudent($schoolYear, '136542100045');
        $participant = $student->enrollments()->first()->sbfpParticipant;
        $date = Carbon::today()->toDateString();
        MealPlan::create([
            'meal_date' => $date,
            'meal_name' => 'Arroz caldo',
            'meal_period' => MealPlan::PERIOD_MORNING,
            'recorded_by_user_id' => $encoder->id,
        ]);
        foreach (['present', 'absent'] as $status) {
            StudentAttendanceRecord::create([
                'sbfp_participant_id' => $participant->id,
                'recorded_by_user_id' => $encoder->id,
                'attendance_date' => $date,
                'meal_period' => MealPlan::PERIOD_MORNING,
                'status' => $status,
            ]);
        }
        Mail::fake();

        $this->actingAs($encoder)->post(route('encoder.attendance.update'), [
            'sbfp_participant_id' => $participant->id,
            'date' => $date,
            'meal_period' => MealPlan::PERIOD_MORNING,
            'status' => 'unmarked',
        ])->assertRedirect();

        $records = StudentAttendanceRecord::query()
            ->where('sbfp_participant_id', $participant->id)
            ->whereDate('attendance_date', $date)
            ->where('meal_period', MealPlan::PERIOD_MORNING)
            ->get();
        $this->assertCount(2, $records);
        $this->assertSame(['unmarked', 'unmarked'], $records->pluck('status')->all());

        $this->actingAs($encoder)->postJson(route('encoder.attendance.scan'), [
            'lrn' => $student->lrn,
            'meal_period' => MealPlan::PERIOD_MORNING,
        ])->assertOk();

        $records = StudentAttendanceRecord::query()
            ->where('sbfp_participant_id', $participant->id)
            ->whereDate('attendance_date', $date)
            ->where('meal_period', MealPlan::PERIOD_MORNING)
            ->get();
        $this->assertCount(2, $records);
        $this->assertSame(['present', 'present'], $records->pluck('status')->all());
        Mail::assertSentTimes(FeedingDayNotice::class, 1);
    }

    public function test_manual_present_after_clear_updates_the_same_record_and_sends_notice(): void
    {
        $schoolYear = $this->createActiveSchoolYear();
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 2,
            'advisory_section' => 'A',
        ]);
        $student = $this->createApprovedStudent($schoolYear, '136542100044');
        $participant = $student->enrollments()->first()->sbfpParticipant;
        MealPlan::create([
            'meal_date' => Carbon::today()->toDateString(),
            'meal_name' => 'Arroz caldo',
            'meal_period' => MealPlan::PERIOD_MORNING,
            'recorded_by_user_id' => $encoder->id,
        ]);
        StudentAttendanceRecord::create([
            'sbfp_participant_id' => $participant->id,
            'recorded_by_user_id' => $encoder->id,
            'attendance_date' => Carbon::today()->toDateString(),
            'meal_period' => MealPlan::PERIOD_MORNING,
            'status' => 'unmarked',
        ]);
        Mail::fake();

        $this->actingAs($encoder)->post(route('encoder.attendance.update'), [
            'sbfp_participant_id' => $participant->id,
            'date' => Carbon::today()->toDateString(),
            'meal_period' => MealPlan::PERIOD_MORNING,
            'status' => 'present',
        ])->assertRedirect();

        $records = StudentAttendanceRecord::query()
            ->where('sbfp_participant_id', $participant->id)
            ->whereDate('attendance_date', Carbon::today()->toDateString())
            ->where('meal_period', MealPlan::PERIOD_MORNING)
            ->get();
        $this->assertCount(1, $records);
        $this->assertSame('present', $records->first()->status);
        Mail::assertSentTimes(FeedingDayNotice::class, 1);
    }

    public function test_cleared_past_attendance_is_not_recreated_as_absent(): void
    {
        $schoolYear = $this->createActiveSchoolYear();
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 2,
            'advisory_section' => 'A',
        ]);
        $student = $this->createApprovedStudent($schoolYear, '136542100042');
        $participant = $student->enrollments()->first()->sbfpParticipant;
        $date = Carbon::yesterday()->toDateString();
        MealPlan::create([
            'meal_date' => $date,
            'meal_name' => 'Arroz caldo',
            'meal_period' => MealPlan::PERIOD_MORNING,
            'recorded_by_user_id' => $encoder->id,
        ]);
        StudentAttendanceRecord::create([
            'sbfp_participant_id' => $participant->id,
            'recorded_by_user_id' => $encoder->id,
            'attendance_date' => $date,
            'meal_period' => MealPlan::PERIOD_MORNING,
            'status' => 'present',
        ]);

        $this->actingAs($encoder)
            ->from(route('encoder.attendance.index', ['date' => $date]))
            ->post(route('encoder.attendance.update'), [
                'sbfp_participant_id' => $participant->id,
                'date' => $date,
                'meal_period' => MealPlan::PERIOD_MORNING,
                'status' => 'unmarked',
            ])
            ->assertRedirect();

        $response = $this->actingAs($encoder)->get(route('encoder.attendance.index', [
            'date' => $date,
            'meal_period' => MealPlan::PERIOD_MORNING,
        ]));
        $response->assertOk()->assertSee('Unmarked');

        $attendanceRecords = StudentAttendanceRecord::query()
            ->where('sbfp_participant_id', $participant->id)
            ->whereDate('attendance_date', $date)
            ->where('meal_period', MealPlan::PERIOD_MORNING)
            ->get();
        $this->assertCount(1, $attendanceRecords);
        $this->assertSame('unmarked', $attendanceRecords->first()->status);
    }

    private function createActiveSchoolYear(): SchoolYear
    {
        return SchoolYear::create([
            'year' => '2026-2027',
            'start_date' => '2026-06-01',
            'end_date' => '2027-03-31',
            'is_active' => true,
        ]);
    }

    private function createApprovedStudent(SchoolYear $schoolYear, string $lrn): Student
    {
        $student = Student::create([
            'lrn' => $lrn,
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
            'grade_level' => 2,
            'section' => 'A',
            'status' => Enrollment::STATUS_ENROLLED,
        ]);
        SbfpParticipant::create([
            'enrollment_id' => $enrollment->id,
            'parent_consent' => 'approved',
        ]);

        return $student;
    }
}
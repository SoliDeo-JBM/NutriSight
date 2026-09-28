<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\AuditLog;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentWithdrawalTest extends TestCase
{
    use RefreshDatabase;

    public function test_encoder_can_withdraw_and_restore_an_owned_student_without_deleting_records(): void
    {
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 1,
            'advisory_section' => 'Mabini',
        ]);
        $schoolYear = $this->createActiveSchoolYear();
        $student = $this->createStudent();
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'grade_level' => 1,
            'section' => 'Mabini',
            'status' => Enrollment::STATUS_ENROLLED,
        ]);

        $response = $this->actingAs($encoder)->delete(route('encoder.students.destroy', $student));

        $response->assertRedirect();
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $encoder->id,
            'action' => 'Removed',
            'module' => 'Students',
        ]);
        $this->assertDatabaseHas('students', ['id' => $student->id]);
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => Enrollment::STATUS_WITHDRAWN,
        ]);
        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id, 'status' => Enrollment::STATUS_ENROLLED]);

        $this->actingAs($encoder)->get(route('encoder.students.index'))->assertDontSee($student->first_name);

        $this->actingAs($encoder)->post(route('encoder.students.restore', $student))->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => Enrollment::STATUS_ENROLLED,
        ]);
    }

    public function test_encoder_cannot_withdraw_a_student_outside_their_advisory_scope(): void
    {
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 1,
            'advisory_section' => 'Mabini',
        ]);
        $schoolYear = $this->createActiveSchoolYear();
        $student = $this->createStudent('136542100013');
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'grade_level' => 2,
            'section' => 'Rizal',
            'status' => Enrollment::STATUS_ENROLLED,
        ]);

        $this->actingAs($encoder)->delete(route('encoder.students.destroy', $student))->assertNotFound();
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => Enrollment::STATUS_ENROLLED,
        ]);
    }

    public function test_removed_action_is_available_in_the_audit_log_filter(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index'))
            ->assertOk()
            ->assertSee('<option value="Removed"', false);
    }

    public function test_case_variants_share_one_updated_action_filter(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        foreach (['Updated', 'updated'] as $action) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => $action,
                'module' => 'Students',
                'description' => $action . ' action fixture',
            ]);
        }

        $this->actingAs($admin)->get(route('admin.audit-logs.index'))
            ->assertOk()
            ->assertSee('<option value="Updated"', false)
            ->assertDontSee('<option value="updated"', false);

        $this->actingAs($admin)->get(route('admin.audit-logs.index', ['action' => 'Updated']))
            ->assertOk()
            ->assertViewHas('auditLogs', fn($logs) => $logs->total() === 2);
    }

    private function createActiveSchoolYear(): SchoolYear
    {
        return SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);
    }

    private function createStudent(string $lrn = '136542100012'): Student
    {
        return Student::create([
            'lrn' => $lrn,
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'sex' => 'Male',
            'birth_date' => '2015-01-01',
            'guardian_name' => 'Maria Dela Cruz',
            'guardian_contact' => '09171234567',
            'guardian_email' => 'guardian@example.com',
            'address' => '1 Main Street',
        ]);
    }
}

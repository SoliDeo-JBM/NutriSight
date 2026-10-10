<?php

namespace Tests\Feature;

use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InactiveSchoolYearWriteProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_encoder_cannot_create_students_in_an_inactive_school_year(): void
    {
        [$active, $inactive] = $this->createSchoolYears();
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 1,
            'advisory_section' => 'Mabini',
        ]);

        $this->actingAs($encoder)
            ->post(route('school-years.switch'), ['school_year_id' => $inactive->id])
            ->assertRedirect();

        $this->actingAs($encoder)
            ->post(route('encoder.students.store'), [])
            ->assertForbidden();
    }

    public function test_admin_cannot_change_meal_plans_in_an_inactive_school_year(): void
    {
        [, $inactive] = $this->createSchoolYears();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('school-years.switch'), ['school_year_id' => $inactive->id])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.meal-plans.store'), [])
            ->assertForbidden();
    }

    public function test_active_school_year_reaches_normal_write_validation(): void
    {
        [$active] = $this->createSchoolYears();
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 1,
            'advisory_section' => 'Mabini',
        ]);

        $this->actingAs($encoder)
            ->post(route('school-years.switch'), ['school_year_id' => $active->id])
            ->assertRedirect();

        $this->actingAs($encoder)
            ->post(route('encoder.students.store'), [])
            ->assertSessionHasErrors();
    }

    public function test_inactive_school_year_assessment_report_remains_read_only(): void
    {
        [, $inactive] = $this->createSchoolYears();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('school-years.switch'), ['school_year_id' => $inactive->id])
            ->assertRedirect();

        $this->actingAs($admin)
            ->get(route('admin.reports.sbfp.assessment'))
            ->assertOk()
            ->assertSee('Read-only school year: 2024-2025')
            ->assertSee('Edits and other changes are invalid and forbidden.');
    }

    private function createSchoolYears(): array
    {
        $active = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);
        $inactive = SchoolYear::create([
            'year' => '2024-2025',
            'start_date' => '2024-06-01',
            'end_date' => '2025-03-31',
            'is_active' => false,
        ]);

        return [$active, $inactive];
    }
}
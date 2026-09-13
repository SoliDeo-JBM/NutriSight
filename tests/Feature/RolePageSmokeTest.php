<?php

namespace Tests\Feature;

use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePageSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_pages_render(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $this->createActiveSchoolYear($user);

        $this->actingAs($user)->get('/super-admin/accounts')->assertOk();
        $this->actingAs($user)->get('/super-admin/attendance')->assertOk();
    }

    public function test_admin_pages_render(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->createActiveSchoolYear($user);

        $this->actingAs($user)->get('/admin/accounts')->assertOk();
        $this->actingAs($user)->get('/admin/students/sbfp')->assertOk();
    }

    public function test_encoder_sbfp_page_and_dashboard_render(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 1,
            'advisory_section' => 'A',
        ]);
        $this->createActiveSchoolYear($user);

        $this->actingAs($user)->get('/encoder/students/sbfp')->assertOk();
        $this->actingAs($user)->get('/encoder/dashboard')->assertOk();
    }

    private function createActiveSchoolYear(User $user): void
    {
        $schoolYear = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);

        $user->syncSchoolYearUserRecord($schoolYear->id);
    }
}
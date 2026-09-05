<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolYearScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_school_years_page(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->get('/super-admin/school-years');

        $response->assertOk();
    }

    public function test_super_admin_can_create_new_school_year(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->post('/super-admin/school-years', [
                'school_year' => '2026-2027',
                'start_date' => '2026-06-01',
                'end_date' => '2027-03-31',
            ]);

        $response->assertRedirect('/super-admin/school-years');
        $this->assertDatabaseHas('programs', ['school_year' => '2026-2027']);
    }

    public function test_super_admin_can_activate_school_year(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $sy1 = Program::create([
            'school_year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);

        $sy2 = Program::create([
            'school_year' => '2026-2027',
            'start_date' => '2026-06-01',
            'end_date' => '2027-03-31',
            'is_active' => false,
        ]);

        $response = $this
            ->actingAs($superAdmin)
            ->post('/super-admin/school-years/' . $sy2->id . '/activate');

        $response->assertRedirect();
        
        $sy1->refresh();
        $sy2->refresh();

        $this->assertFalse($sy1->is_active);
        $this->assertTrue($sy2->is_active);
    }

    public function test_users_can_switch_school_year_context(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_ENCODER,
        ]);

        $sy = Program::create([
            'school_year' => '2026-2027',
            'start_date' => '2026-06-01',
            'end_date' => '2027-03-31',
            'is_active' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/school-years/switch', [
                'school_year_id' => $sy->id,
            ]);

        $response->assertRedirect();
        $this->assertEquals($sy->id, session('active_school_year_id'));
    }
}

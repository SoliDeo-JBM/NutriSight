<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SchoolYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_edit_encoder_profile_and_advisory_assignment(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'email' => 'encoder@example.com',
        ]);
        $schoolYear = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.accounts.update', $encoder), [
            'name' => 'Updated Encoder',
            'sex' => 'Female',
            'birthdate' => '1990-01-02',
            'email' => 'updated.encoder@example.com',
            'deped_id' => 'ENC-002',
            'position' => 'Teacher II',
            'advisory_grade_level' => 4,
            'advisory_section' => 'Mabini',
        ]);

        $response->assertRedirect(route('admin.accounts.index'));
        $this->assertDatabaseHas('users', [
            'id' => $encoder->id,
            'name' => 'Updated Encoder',
            'email' => 'updated.encoder@example.com',
            'deped_id' => 'ENC-002',
            'advisory_grade_level' => 4,
            'advisory_section' => 'Mabini',
        ]);
        $this->assertDatabaseHas('school_year_user_records', [
            'school_year_id' => $schoolYear->id,
            'user_id' => $encoder->id,
            'advisory_grade_level' => '4',
            'advisory_section' => 'Mabini',
        ]);
    }

    public function test_super_admin_can_edit_admin_profile(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'email' => 'admin@example.com',
        ]);
        SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->patch(route('super-admin.accounts.update', $admin), [
            'name' => 'Updated Admin',
            'sex' => 'Male',
            'birthdate' => '1985-03-04',
            'email' => 'updated.admin@example.com',
            'deped_id' => 'ADM-002',
            'position' => 'Master Teacher I',
        ]);

        $response->assertRedirect(route('super-admin.accounts.index'));
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Updated Admin',
            'email' => 'updated.admin@example.com',
            'deped_id' => 'ADM-002',
            'position' => 'Master Teacher I',
        ]);
    }

    public function test_managed_account_update_rejects_wrong_target_role(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $encoder = User::factory()->create(['role' => User::ROLE_ENCODER]);

        $response = $this->actingAs($superAdmin)->patch(route('super-admin.accounts.update', $encoder), [
            'name' => 'Should Not Update',
            'sex' => 'Male',
            'birthdate' => '1990-01-02',
            'email' => $encoder->email,
            'deped_id' => $encoder->deped_id,
            'position' => $encoder->position,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('users', ['name' => 'Should Not Update']);
    }

    public function test_new_school_year_gets_one_record_for_every_existing_user(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $encoder = User::factory()->create(['role' => User::ROLE_ENCODER]);
        $firstYear = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);
        $superAdmin->syncSchoolYearUserRecord($firstYear->id);
        $encoder->syncSchoolYearUserRecord($firstYear->id);
        $this->actingAs($superAdmin)->post(route('super-admin.school-years.store'), [
            'school_year' => '2027-2028',
            'start_date' => '2027-06-01',
            'end_date' => '2028-03-31',
        ]);
        $secondYear = SchoolYear::where('year', '2027-2028')->firstOrFail();

        $this->assertDatabaseHas('school_year_user_records', [
            'school_year_id' => $firstYear->id,
            'user_id' => $encoder->id,
        ]);
        $this->assertDatabaseHas('school_year_user_records', [
            'school_year_id' => $secondYear->id,
            'user_id' => $superAdmin->id,
        ]);
        $this->assertDatabaseCount('school_year_user_records', 4);
    }
}

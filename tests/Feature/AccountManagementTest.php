<?php

namespace Tests\Feature;

use App\Models\User;
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
    }

    public function test_super_admin_can_edit_admin_profile(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'email' => 'admin@example.com',
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
}

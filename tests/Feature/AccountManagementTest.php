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

        $response->assertRedirect(route('super-admin.accounts.index', ['role' => 'admin']));
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Updated Admin',
            'email' => 'updated.admin@example.com',
            'deped_id' => 'ADM-002',
            'position' => 'Master Teacher I',
        ]);
    }

    public function test_account_edit_rejects_underage_birthdates_and_shows_the_error(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $schoolYear = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'email' => 'encoder-age-edit@example.com',
            'birthdate' => '1990-01-02',
        ]);
        $managedAdmin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'email' => 'admin-age-edit@example.com',
            'birthdate' => '1990-01-02',
        ]);
        $encoder->syncSchoolYearUserRecord($schoolYear->id);
        $managedAdmin->syncSchoolYearUserRecord($schoolYear->id);

        $cases = [
            [
                'actor' => $admin,
                'target' => $encoder,
                'update_route' => 'admin.accounts.update',
                'index_route' => 'admin.accounts.index',
                'index_parameters' => [],
            ],
            [
                'actor' => $superAdmin,
                'target' => $managedAdmin,
                'update_route' => 'super-admin.accounts.update',
                'index_route' => 'super-admin.accounts.index',
                'index_parameters' => ['role' => User::ROLE_ADMIN],
            ],
        ];
        $tooYoungBirthdate = today()->subYears(18)->addDay()->toDateString();

        foreach ($cases as $case) {
            $editUrl = route($case['update_route'], $case['target']);
            $indexUrl = route($case['index_route'], $case['index_parameters']);

            $this->followingRedirects()
                ->actingAs($case['actor'])
                ->from($indexUrl)
                ->patch($editUrl, [
                    '_edit_action' => $editUrl,
                    'name' => $case['target']->name,
                    'email' => $case['target']->email,
                    'birthdate' => $tooYoungBirthdate,
                ])
                ->assertOk()
                ->assertSee('The account holder must be at least 18 years old.')
                ->assertSee('max="' . today()->subYears(18)->toDateString() . '"', false);

            $this->assertDatabaseHas('users', [
                'id' => $case['target']->id,
                'birthdate' => '1990-01-02',
            ]);
        }
    }

    public function test_account_edit_accepts_birthdate_on_the_18th_birthday(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'email' => 'admin-age-boundary@example.com',
            'birthdate' => '1990-01-02',
        ]);
        $schoolYear = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);
        $admin->syncSchoolYearUserRecord($schoolYear->id);
        $minimumBirthdate = today()->subYears(18)->toDateString();

        $this->actingAs($superAdmin)
            ->patch(route('super-admin.accounts.update', $admin), [
                'name' => $admin->name,
                'sex' => 'Female',
                'email' => $admin->email,
                'birthdate' => $minimumBirthdate,
                'position' => 'Master Teacher I',
            ])
            ->assertRedirect(route('super-admin.accounts.index', ['role' => User::ROLE_ADMIN]));

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'birthdate' => $minimumBirthdate,
        ]);
    }

    public function test_super_admin_can_view_encoder_accounts(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $schoolYear = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Managed Admin']);
        $encoder = User::factory()->create(['role' => User::ROLE_ENCODER, 'name' => 'Managed Encoder']);
        $admin->syncSchoolYearUserRecord($schoolYear->id);
        $encoder->syncSchoolYearUserRecord($schoolYear->id);

        $response = $this->actingAs($superAdmin)->get(route('super-admin.accounts.index', ['role' => 'encoder']));

        $response->assertOk()
            ->assertSee('Encoder Accounts')
            ->assertSee('Managed Encoder')
            ->assertDontSee('Managed Admin')
            ->assertSee(route('super-admin.accounts.create', ['role' => 'encoder']))
            ->assertSee('Confirm Archive')
            ->assertDontSee('Confirm Removal');
    }

    public function test_super_admin_can_create_encoder_with_advisory_assignment(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $schoolYear = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin)->get(route('super-admin.accounts.create', ['role' => 'encoder']))
            ->assertOk()
            ->assertSee('Add New Encoder Account')
            ->assertSee('Account holders must be at least 18 years old.')
            ->assertSee('max="' . today()->subYears(18)->toDateString() . '"', false)
            ->assertSee('name="advisory_grade_level"', false)
            ->assertSee('name="advisory_section"', false);

        $response = $this->actingAs($superAdmin)->post(route('super-admin.accounts.store'), [
            'target_role' => User::ROLE_ENCODER,
            'deped_id' => 'ENC-100',
            'first_name' => 'Encoder',
            'last_name' => 'Created',
            'email' => 'created.encoder@example.com',
            'password' => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
            'sex' => 'Female',
            'birthdate' => '1990-01-01',
            'position' => 'Teacher I',
            'advisory_grade_level' => 3,
            'advisory_section' => 'Mabini',
        ]);

        $response->assertRedirect(route('super-admin.accounts.index', ['role' => 'encoder']));
        $encoder = User::where('email', 'created.encoder@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_ENCODER, $encoder->role);
        $this->assertDatabaseHas('school_year_user_records', [
            'school_year_id' => $schoolYear->id,
            'user_id' => $encoder->id,
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => '3',
            'advisory_section' => 'Mabini',
        ]);
    }

    public function test_account_creation_rejects_birthdates_under_18_for_admins_and_encoders(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $tooYoungBirthdate = today()->subYears(18)->addDay()->toDateString();

        foreach ([User::ROLE_ADMIN, User::ROLE_ENCODER] as $index => $role) {
            $email = "underage-{$index}@example.com";
            $this->actingAs($superAdmin)
                ->post(route('super-admin.accounts.store'), [
                    'target_role' => $role,
                    'deped_id' => "AGE-{$index}",
                    'first_name' => 'Young',
                    'last_name' => 'Account',
                    'email' => $email,
                    'password' => 'SecurePass1!',
                    'password_confirmation' => 'SecurePass1!',
                    'sex' => 'Female',
                    'birthdate' => $tooYoungBirthdate,
                    'position' => 'Teacher I',
                    'advisory_grade_level' => 3,
                    'advisory_section' => 'Mabini',
                ])
                ->assertSessionHasErrors(['birthdate' => 'The account holder must be at least 18 years old.']);

            $this->assertDatabaseMissing('users', ['email' => $email]);
        }
    }

    public function test_account_creation_accepts_birthdates_on_the_18th_birthday_for_admins_and_encoders(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $minimumBirthdate = today()->subYears(18)->toDateString();

        foreach ([User::ROLE_ADMIN, User::ROLE_ENCODER] as $index => $role) {
            $email = "adult-{$index}@example.com";
            $this->actingAs($superAdmin)
                ->post(route('super-admin.accounts.store'), [
                    'target_role' => $role,
                    'deped_id' => "ADULT-{$index}",
                    'first_name' => 'Adult',
                    'last_name' => 'Account',
                    'email' => $email,
                    'password' => 'SecurePass1!',
                    'password_confirmation' => 'SecurePass1!',
                    'sex' => 'Female',
                    'birthdate' => $minimumBirthdate,
                    'position' => 'Teacher I',
                    'advisory_grade_level' => 3,
                    'advisory_section' => 'Mabini',
                ]);

            $this->assertDatabaseHas('users', [
                'email' => $email,
                'role' => $role,
                'birthdate' => $minimumBirthdate,
            ]);
        }
    }

    public function test_admin_cannot_create_admin_by_tampering_with_target_role(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.accounts.store'), [
            'target_role' => User::ROLE_ADMIN,
            'deped_id' => 'ENC-101',
            'first_name' => 'Encoder',
            'last_name' => 'Protected',
            'email' => 'protected.encoder@example.com',
            'password' => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
            'sex' => 'Female',
            'birthdate' => '1990-01-01',
            'position' => 'Teacher I',
            'advisory_grade_level' => 3,
            'advisory_section' => 'Mabini',
        ]);

        $response->assertRedirect(route('admin.accounts.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'protected.encoder@example.com',
            'role' => User::ROLE_ENCODER,
        ]);
        $this->assertDatabaseMissing('users', [
            'email' => 'protected.encoder@example.com',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_super_admin_cannot_select_unmanaged_roles(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $this->actingAs($superAdmin)
            ->get(route('super-admin.accounts.index', ['role' => User::ROLE_SUPER_ADMIN]))
            ->assertForbidden();

        $this->post(route('super-admin.accounts.store'), [
            'target_role' => User::ROLE_SUPER_ADMIN,
        ])->assertForbidden();
    }

    public function test_super_admin_can_edit_encoder_profile_and_advisory_assignment(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $schoolYear = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'email' => 'encoder@example.com',
            'advisory_grade_level' => 2,
            'advisory_section' => 'A',
        ]);
        $encoder->syncSchoolYearUserRecord($schoolYear->id);

        $response = $this->actingAs($superAdmin)->patch(route('super-admin.accounts.update', $encoder), [
            'name' => 'Updated Encoder',
            'sex' => 'Female',
            'birthdate' => '1990-01-02',
            'email' => 'updated.encoder@example.com',
            'deped_id' => 'ENC-102',
            'position' => 'Teacher II',
            'advisory_grade_level' => 4,
            'advisory_section' => 'Rizal',
        ]);

        $response->assertRedirect(route('super-admin.accounts.index', ['role' => 'encoder']));
        $this->assertDatabaseHas('users', [
            'id' => $encoder->id,
            'role' => User::ROLE_ENCODER,
            'email' => 'updated.encoder@example.com',
            'advisory_grade_level' => 4,
            'advisory_section' => 'Rizal',
        ]);
        $this->assertDatabaseHas('school_year_user_records', [
            'school_year_id' => $schoolYear->id,
            'user_id' => $encoder->id,
            'advisory_grade_level' => '4',
            'advisory_section' => 'Rizal',
        ]);
    }

    public function test_admin_cannot_update_encoder_to_an_existing_email(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $encoder = User::factory()->create(['role' => User::ROLE_ENCODER, 'email' => 'encoder@example.com']);
        $existingUser = User::factory()->create(['email' => 'already-used@example.com']);

        $response = $this->actingAs($admin)->from(route('admin.accounts.index'))->patch(
            route('admin.accounts.update', $encoder),
            [
                'first_name' => 'Updated',
                'last_name' => 'Encoder',
                'email' => $existingUser->email,
                '_edit_action' => route('admin.accounts.update', $encoder),
            ]
        );

        $response->assertRedirect(route('admin.accounts.index'));
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseHas('users', [
            'id' => $encoder->id,
            'email' => 'encoder@example.com',
        ]);
    }

    public function test_admin_cannot_update_admin_and_super_admin_cannot_update_super_admin(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $otherAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin)->patch(route('admin.accounts.update', $otherAdmin), [
            'name' => 'Should Not Update',
            'email' => $otherAdmin->email,
        ])->assertForbidden();

        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $otherSuperAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($superAdmin)->patch(route('super-admin.accounts.update', $otherSuperAdmin), [
            'name' => 'Should Not Update',
            'sex' => 'Male',
            'birthdate' => '1990-01-02',
            'email' => $otherSuperAdmin->email,
            'deped_id' => $otherSuperAdmin->deped_id,
            'position' => $otherSuperAdmin->position,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('users', ['name' => 'Should Not Update']);
    }

    public function test_admin_can_remove_and_restore_encoder_without_deleting_record(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $encoder = User::factory()->create(['role' => User::ROLE_ENCODER, 'is_active' => true]);

        $response = $this->actingAs($admin)->delete(route('accounts.destroy', $encoder), [
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.accounts.index'));
        $this->assertDatabaseHas('users', [
            'id' => $encoder->id,
            'is_active' => false,
            'deleted_at' => null,
        ]);

        $this->actingAs($admin)->patch(route('admin.accounts.update', $encoder), [
            'first_name' => 'Should',
            'last_name' => 'Not Update',
            'email' => $encoder->email,
        ])->assertSessionHasErrors('account');

        $this->assertDatabaseHas('users', [
            'id' => $encoder->id,
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('accounts.restore', $encoder), [
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.accounts.index'));
        $this->assertDatabaseHas('users', [
            'id' => $encoder->id,
            'is_active' => true,
            'deleted_at' => null,
        ]);
    }

    public function test_super_admin_can_archive_and_restore_encoder(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $encoder = User::factory()->create(['role' => User::ROLE_ENCODER, 'is_active' => true]);

        $this->actingAs($superAdmin)->delete(route('accounts.destroy', $encoder), [
            'password' => 'password',
        ])->assertRedirect(route('super-admin.accounts.index', ['role' => 'encoder']));

        $this->assertDatabaseHas('users', [
            'id' => $encoder->id,
            'is_active' => false,
            'deleted_at' => null,
        ]);

        $this->actingAs($superAdmin)->post(route('accounts.restore', $encoder), [
            'password' => 'password',
        ])->assertRedirect(route('super-admin.accounts.index', ['role' => 'encoder']));

        $this->assertDatabaseHas('users', [
            'id' => $encoder->id,
            'is_active' => true,
            'deleted_at' => null,
        ]);
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

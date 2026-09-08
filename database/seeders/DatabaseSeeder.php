<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'encoder@nutrisight.test'],
            [
                'name' => 'Encoder User',
                'password' => Hash::make('password'),
                'sex' => 'Female',
                'role' => User::ROLE_ENCODER,
                'birthdate' => '1995-01-01',
                'position' => 'Teacher I',
                'advisory_grade_level' => 1,
                'advisory_section' => 'A',
                'deped_id' => 100001,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@nutrisight.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'sex' => 'Male',
                'role' => User::ROLE_ADMIN,
                'birthdate' => '1990-01-01',
                'position' => 'Master Teacher I',
                'advisory_grade_level' => 1,
                'advisory_section' => 'B',
                'deped_id' => 100002,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'superadmin@nutrisight.test'],
            [
                'name' => 'Super Admin User',
                'password' => Hash::make('password'),
                'sex' => 'Male',
                'role' => User::ROLE_SUPER_ADMIN,
                'birthdate' => '1985-01-01',
                'position' => 'Master Teacher II',
                'advisory_grade_level' => 1,
                'advisory_section' => 'C',
                'deped_id' => 100003,
                'is_active' => true,
            ]
        );
    }
}
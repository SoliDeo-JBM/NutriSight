<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Encoder Account
        User::create([
            'name' => 'Encoder User',
            'email' => 'encoder@nutrisight.test',
            'password' => Hash::make('password'),
            'sex' => 'Female',
            'role' => User::ROLE_ENCODER,
            'birthdate' => '1995-01-01',
            'position' => 'Teacher I',
            'advisory_grade_level' => 1,
            'advisory_section' => 'A',
            'deped_id' => 100001,
            'is_active' => true,
        ]);

        // 2. Admin Account
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@nutrisight.test',
            'password' => Hash::make('password'),
            'sex' => 'Male',
            'role' => User::ROLE_ADMIN,
            'birthdate' => '1990-01-01',
            'position' => 'Master Teacher I',
            'advisory_grade_level' => 1,
            'advisory_section' => 'B',
            'deped_id' => 100002,
            'is_active' => true,
        ]);

        // 3. Super Admin Account
        User::create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@nutrisight.test',
            'password' => Hash::make('password'),
            'sex' => 'Male',
            'role' => User::ROLE_SUPER_ADMIN,
            'birthdate' => '1985-01-01',
            'position' => 'Master Teacher II',
            'advisory_grade_level' => 1,
            'advisory_section' => 'C',
            'deped_id' => 100003,
            'is_active' => true,
        ]);
    }
}

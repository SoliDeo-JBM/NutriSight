<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@nutrisight.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@nutrisight.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Encoder User',
            'email' => 'encoder@nutrisight.test',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ENCODER,
        ]);
    }
}

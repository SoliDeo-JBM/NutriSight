<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\Student;
use App\Models\NutritionalRecord;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name' => 'Adviser Teacher',
            'email' => 'adviser@nutrisight.com',
            'password' => bcrypt('password'),
            'role' => 'encoder'
        ]);

        $section = Section::first() ?? Section::create([
            'name' => 'Diamond',
            'grade_level' => 'Grade 1',
            'adviser_id' => $user->id
        ]);

        // Clean slate: No placeholder students seeded so user can encode their own data cleanly.
        // Sections and users remain intact.
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_year_user_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role');
            $table->bigInteger('deped_id')->nullable();
            $table->string('position')->nullable();
            $table->string('advisory_grade_level')->nullable();
            $table->string('advisory_section')->nullable();
            $table->timestamps();

            $table->unique(['school_year_id', 'user_id']);
            $table->index(['school_year_id', 'role']);
        });

        $schoolYear = DB::table('school_years')->where('is_active', true)->first()
            ?? DB::table('school_years')->orderByDesc('id')->first();

        if ($schoolYear) {
            $now = now();
            $records = DB::table('users')->select([
                'id as user_id',
                'role',
                'deped_id',
                'position',
                'advisory_grade_level',
                'advisory_section',
            ])->get()->map(fn ($user) => [
                'school_year_id' => $schoolYear->id,
                'user_id' => $user->user_id,
                'role' => $user->role,
                'deped_id' => $user->deped_id,
                'position' => $user->position,
                'advisory_grade_level' => $user->advisory_grade_level,
                'advisory_section' => $user->advisory_section,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if ($records) {
                DB::table('school_year_user_records')->insert($records);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('school_year_user_records');
    }
};
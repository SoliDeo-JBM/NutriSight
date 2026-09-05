<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ensure programs table has initial active school year if empty
        if (Schema::hasTable('programs') && DB::table('programs')->count() === 0) {
            DB::table('programs')->insert([
                'school_year' => '2025-2026',
                'start_date' => '2025-06-02',
                'end_date' => '2026-03-31',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $activeProgramId = DB::table('programs')->where('is_active', true)->value('id') 
            ?? DB::table('programs')->insertGetId([
                'school_year' => '2025-2026',
                'start_date' => '2025-06-02',
                'end_date' => '2026-03-31',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        // 2. Add school_year_id to sections
        if (Schema::hasTable('sections') && !Schema::hasColumn('sections', 'school_year_id')) {
            Schema::table('sections', function (Blueprint $table) use ($activeProgramId) {
                $table->foreignId('school_year_id')->nullable()->after('id')->constrained('programs')->cascadeOnDelete();
            });
            DB::table('sections')->update(['school_year_id' => $activeProgramId]);
        }

        // 3. Add school_year_id to students
        if (Schema::hasTable('students') && !Schema::hasColumn('students', 'school_year_id')) {
            Schema::table('students', function (Blueprint $table) use ($activeProgramId) {
                $table->foreignId('school_year_id')->nullable()->after('id')->constrained('programs')->cascadeOnDelete();
            });
            DB::table('students')->update(['school_year_id' => $activeProgramId]);
        }

        // 4. Add school_year_id to student_assessments
        if (Schema::hasTable('student_assessments') && !Schema::hasColumn('student_assessments', 'school_year_id')) {
            Schema::table('student_assessments', function (Blueprint $table) use ($activeProgramId) {
                $table->foreignId('school_year_id')->nullable()->after('id')->constrained('programs')->cascadeOnDelete();
            });
            DB::table('student_assessments')->update(['school_year_id' => $activeProgramId]);
        }

        // 4b. Add school_year_id to nutritional_records
        if (Schema::hasTable('nutritional_records') && !Schema::hasColumn('nutritional_records', 'school_year_id')) {
            Schema::table('nutritional_records', function (Blueprint $table) use ($activeProgramId) {
                $table->foreignId('school_year_id')->nullable()->after('id')->constrained('programs')->cascadeOnDelete();
            });
            DB::table('nutritional_records')->update(['school_year_id' => $activeProgramId]);
        }

        // 5. Add school_year_id to attendance_records
        if (Schema::hasTable('attendance_records') && !Schema::hasColumn('attendance_records', 'school_year_id')) {
            Schema::table('attendance_records', function (Blueprint $table) use ($activeProgramId) {
                $table->foreignId('school_year_id')->nullable()->after('id')->constrained('programs')->cascadeOnDelete();
            });
            DB::table('attendance_records')->update(['school_year_id' => $activeProgramId]);
        }

        // 6. Add school_year_id to feeding_records
        if (Schema::hasTable('feeding_records') && !Schema::hasColumn('feeding_records', 'school_year_id')) {
            Schema::table('feeding_records', function (Blueprint $table) use ($activeProgramId) {
                $table->foreignId('school_year_id')->nullable()->after('id')->constrained('programs')->cascadeOnDelete();
            });
            DB::table('feeding_records')->update(['school_year_id' => $activeProgramId]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('feeding_records', 'school_year_id')) {
            Schema::table('feeding_records', function (Blueprint $table) {
                $table->dropForeign(['school_year_id']);
                $table->dropColumn('school_year_id');
            });
        }
        if (Schema::hasColumn('attendance_records', 'school_year_id')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->dropForeign(['school_year_id']);
                $table->dropColumn('school_year_id');
            });
        }
        if (Schema::hasColumn('student_assessments', 'school_year_id')) {
            Schema::table('student_assessments', function (Blueprint $table) {
                $table->dropForeign(['school_year_id']);
                $table->dropColumn('school_year_id');
            });
        }
        if (Schema::hasColumn('students', 'school_year_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropForeign(['school_year_id']);
                $table->dropColumn('school_year_id');
            });
        }
        if (Schema::hasColumn('sections', 'school_year_id')) {
            Schema::table('sections', function (Blueprint $table) {
                $table->dropForeign(['school_year_id']);
                $table->dropColumn('school_year_id');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_attendance_records', function (Blueprint $table) {
            $table->index(['attendance_date', 'status']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->index(['school_year_id', 'grade_level']);
        });
    }

    public function down(): void
    {
        Schema::table('student_attendance_records', function (Blueprint $table) {
            $table->dropIndex('student_attendance_records_attendance_date_status_index');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropIndex('enrollments_school_year_id_grade_level_index');
        });
    }
};
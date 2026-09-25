<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meal_plans', function (Blueprint $table) {
            $table->string('meal_period')->default('morning')->after('meal_name');
            $table->index(['meal_date', 'meal_period']);
        });

        Schema::table('student_attendance_records', function (Blueprint $table) {
            $table->string('meal_period')->default('morning')->after('attendance_date');
            $table->index(['attendance_date', 'meal_period']);
        });
    }

    public function down(): void
    {
        Schema::table('student_attendance_records', function (Blueprint $table) {
            $table->dropIndex(['attendance_date', 'meal_period']);
            $table->dropColumn('meal_period');
        });

        Schema::table('meal_plans', function (Blueprint $table) {
            $table->dropIndex(['meal_date', 'meal_period']);
            $table->dropColumn('meal_period');
        });
    }
};
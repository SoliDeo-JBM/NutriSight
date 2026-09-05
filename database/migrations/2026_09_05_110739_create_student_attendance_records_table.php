<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sbfp_participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by_user_id')->constrained('users')->cascadeOnDelete();
            
            $table->date('attendance_date');
            $table->string('status'); // e.g., 'present', 'absent'
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendance_records');
    }
};
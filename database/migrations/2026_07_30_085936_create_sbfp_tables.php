<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('grade_level');
            $table->foreignId('adviser_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->unique(); // LRN
            $table->string('last_name');
            $table->string('first_name');
            $table->string('name_extension')->nullable();
            $table->string('middle_name')->nullable();
            $table->date('birth_date');
            $table->string('gender');
            $table->string('grade_level');
            $table->string('section');
            $table->string('guardian_name');
            $table->string('guardian_contact');
            $table->string('guardian_email')->nullable();
            $table->string('address');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_permitted')->default(false);
            $table->enum('parent_approval_status', ['approved', 'disapproved'])->nullable();
            $table->enum('disapproval_reason', ['unwilling', 'medical_condition'])->nullable();
            $table->text('medical_condition_notes')->nullable();
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('nutritional_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('type', ['baseline', 'mid', 'end']);
            $table->float('weight');
            $table->float('height');
            $table->float('bmi')->nullable();
            $table->string('bmi_category')->nullable();
            $table->string('height_for_age')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('date');
            $table->string('status')->default('present');
            $table->timestamps();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('school_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('nutritional_records');
        Schema::dropIfExists('students');
        Schema::dropIfExists('sections');
    }
};

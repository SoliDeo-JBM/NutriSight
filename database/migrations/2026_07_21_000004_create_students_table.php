<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('name_extension')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('gender', 20);
            $table->date('birth_date');
            $table->string('grade_level');
            $table->string('section', 10);
            $table->string('guardian_name');
            $table->string('guardian_contact', 20);
            $table->string('guardian_email')->nullable();
            $table->string('address');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_permitted')->default(false);
            $table->enum('parent_approval_status', ['approved', 'disapproved'])->nullable();
            $table->enum('disapproval_reason', ['unwilling', 'medical_condition'])->nullable();
            $table->text('medical_condition_notes')->nullable();
            // $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->foreignId('section_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
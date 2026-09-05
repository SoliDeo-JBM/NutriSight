<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('assessment_date');
            $table->decimal('weight_kg', 5, 2);
            $table->decimal('height_m', 4, 2);
            $table->decimal('bmi', 5, 2);
            $table->string('nutritional_status');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_assessments');
    }
};
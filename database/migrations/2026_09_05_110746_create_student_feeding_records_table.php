<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_feeding_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sbfp_participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by_user_id')->constrained('users')->cascadeOnDelete();
            
            $table->date('feeding_date');
            $table->string('meal_type');
            $table->string('meal_served');
            $table->string('photo')->nullable(); // Nullable in case they forget to upload!
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_feeding_records');
    }
};
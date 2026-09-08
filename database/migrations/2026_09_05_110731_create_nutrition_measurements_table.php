<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutrition_measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sbfp_participant_id')->constrained()->cascadeOnDelete();
            
            $table->string('height');
            $table->string('weight');
            $table->decimal('bmi', 8, 2);
            $table->string('bmi_category');
            $table->string('hfa'); // Height-for-Age
            $table->string('measurement_period'); // e.g., 'baseline', 'mid', 'end'
            $table->string('remarks')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrition_measurements');
    }
};
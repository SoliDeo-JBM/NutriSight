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
        Schema::create('sbfp_participants', function (Blueprint $table) {
            $table->id();
            
            // Links directly to the enrollment record
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            
            $table->string('parent_consent'); // e.g., 'approved', 'disapproved', 'pending'
            $table->text('disapproval_reason')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sbfp_participants');
    }
};
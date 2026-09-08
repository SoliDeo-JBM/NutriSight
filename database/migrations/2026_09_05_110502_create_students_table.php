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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lrn')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('name_extension')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('sex');
            $table->date('birth_date');
            $table->string('guardian_name');
            $table->string('guardian_email');
            $table->string('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
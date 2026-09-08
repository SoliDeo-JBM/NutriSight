<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('report_periods', function (Blueprint $table) {
      $table->id();
      $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
      $table->string('name');
      $table->unsignedTinyInteger('month')->nullable();
      $table->timestamps();
      $table->unique(['school_year_id', 'name']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('report_periods');
  }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('attendance_report_months', function (Blueprint $table) {
      $table->id();
      $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
      $table->unsignedTinyInteger('month');
      $table->timestamps();
      $table->unique(['school_year_id', 'month']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('attendance_report_months');
  }
};
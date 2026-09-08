<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('attendance_report_sections', function (Blueprint $table) {
      $table->id();
      $table->foreignId('attendance_report_month_id')->constrained()->cascadeOnDelete();
      $table->unsignedTinyInteger('grade_level');
      $table->string('section');
      $table->timestamps();
      $table->unique(['attendance_report_month_id', 'grade_level', 'section']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('attendance_report_sections');
  }
};
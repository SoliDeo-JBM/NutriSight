<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('report_period_rows', function (Blueprint $table) {
      $table->id();
      $table->foreignId('report_period_id')->constrained()->cascadeOnDelete();
      $table->unsignedTinyInteger('grade_level');
      $table->string('sex', 1);
      $table->unsignedInteger('enrollment')->default(0);
      $table->unsignedInteger('pupils_weighed')->default(0);
      $table->unsignedInteger('bmi_severely_wasted')->default(0);
      $table->unsignedInteger('bmi_wasted')->default(0);
      $table->unsignedInteger('bmi_normal')->default(0);
      $table->unsignedInteger('bmi_overweight')->default(0);
      $table->unsignedInteger('bmi_obese')->default(0);
      $table->unsignedInteger('hfa_severely_stunted')->default(0);
      $table->unsignedInteger('hfa_stunted')->default(0);
      $table->unsignedInteger('hfa_normal')->default(0);
      $table->unsignedInteger('hfa_tall')->default(0);
      $table->timestamps();
      $table->unique(['report_period_id', 'grade_level', 'sex']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('report_period_rows');
  }
};
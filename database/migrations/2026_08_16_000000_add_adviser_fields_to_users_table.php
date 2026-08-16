<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->string('deped_id')->nullable()->after('role')->unique();
      $table->enum('sex', ['Male', 'Female'])->nullable()->after('deped_id');
      $table->date('birthdate')->nullable()->after('sex');
      $table->string('position')->nullable()->after('birthdate');
      $table->string('advisory_grade_level')->nullable()->after('position');
      $table->string('advisory_section')->nullable()->after('advisory_grade_level');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropColumn([
        'deped_id',
        'sex',
        'birthdate',
        'position',
        'advisory_grade_level',
        'advisory_section',
      ]);
    });
  }
};

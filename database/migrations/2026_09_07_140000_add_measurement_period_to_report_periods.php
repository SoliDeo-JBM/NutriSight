<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('report_periods', function (Blueprint $table) {
      $table->string('measurement_period', 20)->default('baseline')->after('month');
    });
  }

  public function down(): void
  {
    Schema::table('report_periods', function (Blueprint $table) {
      $table->dropColumn('measurement_period');
    });
  }
};
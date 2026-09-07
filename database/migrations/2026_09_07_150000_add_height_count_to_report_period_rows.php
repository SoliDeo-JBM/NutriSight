<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('report_period_rows', function (Blueprint $table) {
      $table->unsignedInteger('pupils_height_taken')->default(0)->after('pupils_weighed');
    });
  }

  public function down(): void
  {
    Schema::table('report_period_rows', function (Blueprint $table) {
      $table->dropColumn('pupils_height_taken');
    });
  }
};
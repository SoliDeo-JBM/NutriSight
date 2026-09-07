<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    $periods = DB::table('report_periods')->orderBy('school_year_id')->orderByDesc('id')->get();
    $keptTerms = [];
    $keptMonths = [];
    $deleteIds = [];

    foreach ($periods as $period) {
      $termKey = $period->school_year_id . ':' . $period->measurement_period;
      $monthKey = $period->school_year_id . ':' . $period->month;
      if (isset($keptTerms[$termKey]) || isset($keptMonths[$monthKey])) {
        $deleteIds[] = $period->id;
        continue;
      }
      $keptTerms[$termKey] = true;
      $keptMonths[$monthKey] = true;
    }

    if ($deleteIds) {
      DB::table('report_periods')->whereIn('id', $deleteIds)->delete();
    }

    Schema::table('report_periods', function (Blueprint $table) {
      $table->dropUnique(['school_year_id', 'measurement_period', 'month']);
      $table->unique(['school_year_id', 'measurement_period']);
      $table->unique(['school_year_id', 'month']);
    });
  }

  public function down(): void
  {
    Schema::table('report_periods', function (Blueprint $table) {
      $table->dropUnique(['school_year_id', 'measurement_period']);
      $table->dropUnique(['school_year_id', 'month']);
      $table->unique(['school_year_id', 'measurement_period', 'month']);
    });
  }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_periods', function (Blueprint $table) {
            $table->dropUnique(['school_year_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::table('report_periods', function (Blueprint $table) {
            $table->unique(['school_year_id', 'month']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_year_report_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_year_id')->unique()->constrained('school_years')->cascadeOnDelete();
            $table->string('project_development_officer_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_year_report_settings');
    }
};

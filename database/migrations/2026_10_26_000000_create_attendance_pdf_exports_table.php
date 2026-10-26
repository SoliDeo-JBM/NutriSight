<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_pdf_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_report_month_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('grade_level')->nullable();
            $table->string('section')->nullable();
            $table->boolean('scope_is_encoder')->default(false);
            $table->string('status', 20)->index();
            $table->string('file_path')->nullable();
            $table->string('failure_message')->nullable();
            $table->unsignedInteger('data_preparation_ms')->nullable();
            $table->unsignedInteger('pdf_render_ms')->nullable();
            $table->unsignedBigInteger('peak_memory_bytes')->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_pdf_exports');
    }
};
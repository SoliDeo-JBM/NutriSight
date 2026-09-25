<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('father_name')->nullable()->after('birth_date');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('guardian_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['father_name', 'mother_name']);
            $table->string('guardian_name')->nullable(false)->change();
        });
    }
};
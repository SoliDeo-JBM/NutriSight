<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('staff_role')->nullable()->after('role');
        });

        Schema::table('school_year_user_records', function (Blueprint $table): void {
            $table->string('staff_role')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('school_year_user_records', function (Blueprint $table): void {
            $table->dropColumn('staff_role');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('staff_role');
        });
    }
};

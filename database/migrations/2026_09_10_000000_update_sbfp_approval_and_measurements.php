<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('sbfp_participants', 'parent_consent')) {
            Schema::table('sbfp_participants', function (Blueprint $table) {
                $table->string('parent_consent')->nullable()->change();
            });
        }

        if (Schema::hasColumn('nutrition_measurements', 'remarks')) {
            Schema::table('nutrition_measurements', function (Blueprint $table) {
                $table->dropColumn('remarks');
            });
        }
    }

    public function down(): void
    {
        Schema::table('nutrition_measurements', function (Blueprint $table) {
            $table->string('remarks')->nullable();
        });

        Schema::table('sbfp_participants', function (Blueprint $table) {
            $table->string('parent_consent')->nullable(false)->change();
        });
    }
};
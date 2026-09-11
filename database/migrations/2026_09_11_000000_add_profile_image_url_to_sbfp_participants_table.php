<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sbfp_participants', 'profile_image_url')) {
            Schema::table('sbfp_participants', function (Blueprint $table) {
                $table->string('profile_image_url')->nullable()->after('disapproval_reason');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sbfp_participants', 'profile_image_url')) {
            Schema::table('sbfp_participants', function (Blueprint $table) {
                $table->dropColumn('profile_image_url');
            });
        }
    }
};

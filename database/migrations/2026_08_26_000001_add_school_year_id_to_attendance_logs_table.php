<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $activeProgramId = DB::table('programs')->where('is_active', true)->value('id') 
            ?? DB::table('programs')->value('id')
            ?? 1;

        if (Schema::hasTable('attendance_logs') && !Schema::hasColumn('attendance_logs', 'school_year_id')) {
            Schema::table('attendance_logs', function (Blueprint $table) use ($activeProgramId) {
                $table->foreignId('school_year_id')->nullable()->after('id')->constrained('programs')->cascadeOnDelete();
            });
            DB::table('attendance_logs')->update(['school_year_id' => $activeProgramId]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('attendance_logs', 'school_year_id')) {
            Schema::table('attendance_logs', function (Blueprint $table) {
                $table->dropForeign(['school_year_id']);
                $table->dropColumn('school_year_id');
            });
        }
    }
};

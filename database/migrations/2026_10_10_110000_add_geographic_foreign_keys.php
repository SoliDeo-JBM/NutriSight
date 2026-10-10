<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ph_municipalities', function (Blueprint $table) {
            $table->string('province_code', 12)->nullable()->change();
        });

        Schema::table('ph_barangays', function (Blueprint $table) {
            $table->string('province_code', 12)->nullable()->change();
        });

        DB::table('ph_municipalities')
            ->where('province_code', 'false')
            ->update(['province_code' => null]);

        DB::table('ph_barangays')
            ->where('province_code', 'false')
            ->update(['province_code' => null]);

        $this->assertNoOrphanedCodes('ph_municipalities', 'province_code', 'ph_provinces', 'code');
        $this->assertNoOrphanedCodes('ph_barangays', 'province_code', 'ph_provinces', 'code');
        $this->assertNoOrphanedCodes('students', 'province_code', 'ph_provinces', 'code');
        $this->assertNoOrphanedCodes('students', 'municipality_code', 'ph_municipalities', 'code');
        $this->assertNoOrphanedCodes('students', 'barangay_code', 'ph_barangays', 'code');

        Schema::table('ph_municipalities', function (Blueprint $table) {
            $table->foreign('province_code', 'ph_municipalities_province_code_foreign')
                ->references('code')->on('ph_provinces')->restrictOnDelete();
        });

        Schema::table('ph_barangays', function (Blueprint $table) {
            $table->foreign('province_code', 'ph_barangays_province_code_foreign')
                ->references('code')->on('ph_provinces')->restrictOnDelete();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE "ph_barangays" ADD CONSTRAINT "ph_barangays_municipality_code_foreign" FOREIGN KEY ("municipality_code") REFERENCES "ph_municipalities" ("code") ON DELETE RESTRICT NOT VALID');
        } else {
            Schema::table('ph_barangays', function (Blueprint $table) {
                $table->foreign('municipality_code', 'ph_barangays_municipality_code_foreign')
                    ->references('code')->on('ph_municipalities')->restrictOnDelete();
            });
        }

        Schema::table('students', function (Blueprint $table) {
            $table->foreign('province_code', 'students_province_code_foreign')
                ->references('code')->on('ph_provinces')->restrictOnDelete();
            $table->foreign('municipality_code', 'students_municipality_code_foreign')
                ->references('code')->on('ph_municipalities')->restrictOnDelete();
            $table->foreign('barangay_code', 'students_barangay_code_foreign')
                ->references('code')->on('ph_barangays')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign('students_province_code_foreign');
            $table->dropForeign('students_municipality_code_foreign');
            $table->dropForeign('students_barangay_code_foreign');
        });

        Schema::table('ph_barangays', function (Blueprint $table) {
            $table->dropForeign('ph_barangays_municipality_code_foreign');
            $table->dropForeign('ph_barangays_province_code_foreign');
        });

        Schema::table('ph_municipalities', function (Blueprint $table) {
            $table->dropForeign('ph_municipalities_province_code_foreign');
        });
    }

    private function assertNoOrphanedCodes(string $childTable, string $childColumn, string $parentTable, string $parentColumn): void
    {
        $orphanCount = DB::table($childTable)
            ->whereNotNull($childColumn)
            ->whereNotExists(function ($query) use ($childTable, $childColumn, $parentTable, $parentColumn) {
                $query->select(DB::raw(1))
                    ->from($parentTable)
                    ->whereColumn("{$parentTable}.{$parentColumn}", "{$childTable}.{$childColumn}");
            })
            ->count();

        if ($orphanCount > 0) {
            throw new RuntimeException("Cannot add {$childTable}.{$childColumn} foreign key: {$orphanCount} orphaned code(s) found.");
        }
    }
};
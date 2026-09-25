<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ph_provinces', function (Blueprint $table) {
            $table->string('code', 12)->primary();
            $table->string('name');
            $table->string('region_code', 12)->nullable()->index();
            $table->timestamps();
        });

        Schema::create('ph_municipalities', function (Blueprint $table) {
            $table->string('code', 12)->primary();
            $table->string('name');
            $table->string('province_code', 12)->index();
            $table->string('region_code', 12)->nullable()->index();
            $table->timestamps();
        });

        Schema::create('ph_barangays', function (Blueprint $table) {
            $table->string('code', 12)->primary();
            $table->string('name');
            $table->string('municipality_code', 12)->index();
            $table->string('province_code', 12)->index();
            $table->timestamps();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('house_number')->nullable()->after('address');
            $table->string('street')->nullable()->after('house_number');
            $table->string('purok')->nullable()->after('street');
            $table->string('province_code', 12)->nullable()->after('purok')->index();
            $table->string('municipality_code', 12)->nullable()->after('province_code')->index();
            $table->string('barangay_code', 12)->nullable()->after('municipality_code')->index();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'house_number',
                'street',
                'purok',
                'province_code',
                'municipality_code',
                'barangay_code',
            ]);
        });

        Schema::dropIfExists('ph_barangays');
        Schema::dropIfExists('ph_municipalities');
        Schema::dropIfExists('ph_provinces');
    }
};
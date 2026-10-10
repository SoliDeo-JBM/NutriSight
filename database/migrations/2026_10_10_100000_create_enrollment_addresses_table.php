<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollment_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->unique()->constrained()->restrictOnDelete();
            $table->string('house_number')->nullable();
            $table->string('street')->nullable();
            $table->string('purok')->nullable();
            $table->string('province_code', 12)->nullable()->index();
            $table->string('municipality_code', 12)->nullable()->index();
            $table->string('barangay_code', 12)->nullable()->index();
            $table->timestamps();

            $table->foreign('province_code')->references('code')->on('ph_provinces')->restrictOnDelete();
            $table->foreign('municipality_code')->references('code')->on('ph_municipalities')->restrictOnDelete();
            $table->foreign('barangay_code')->references('code')->on('ph_barangays')->restrictOnDelete();
        });

        DB::table('enrollments')
            ->join('students', 'students.id', '=', 'enrollments.student_id')
            ->select([
                'enrollments.id as enrollment_id',
                'students.house_number',
                'students.street',
                'students.purok',
                'students.province_code',
                'students.municipality_code',
                'students.barangay_code',
            ])
            ->orderBy('enrollments.id')
            ->chunk(500, function ($rows): void {
                DB::table('enrollment_addresses')->insert($rows->map(fn($row) => [
                    'enrollment_id' => $row->enrollment_id,
                    'house_number' => $row->house_number,
                    'street' => $row->street,
                    'purok' => $row->purok,
                    'province_code' => $row->province_code,
                    'municipality_code' => $row->municipality_code,
                    'barangay_code' => $row->barangay_code,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all());
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollment_addresses');
    }
};
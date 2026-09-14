<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_name');
            $table->string('name_extension')->nullable()->after('last_name');
        });

        DB::table('users')->select('id', 'name')->orderBy('id')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $parts = preg_split('/\s+/', trim((string) $user->name), -1, PREG_SPLIT_NO_EMPTY);
                if (count($parts) < 2) {
                    continue;
                }

                $extension = null;
                $knownExtensions = ['JR', 'JR.', 'SR', 'SR.', 'II', 'III', 'IV', 'V'];
                if (in_array(strtoupper(end($parts)), $knownExtensions, true)) {
                    $extension = array_pop($parts);
                }

                $lastName = array_pop($parts);
                $firstName = array_shift($parts);
                $middleName = $parts ? implode(' ', $parts) : null;

                DB::table('users')->where('id', $user->id)->update([
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'name_extension' => $extension,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'last_name', 'name_extension']);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->select('id', 'first_name', 'middle_name', 'last_name', 'name_extension')
            ->whereNotNull('first_name')
            ->whereNotNull('last_name')
            ->orderBy('id')
            ->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    $middleInitial = $user->middle_name
                        ? strtoupper(substr(trim($user->middle_name), 0, 1)) . '.'
                        : null;

                    DB::table('users')->where('id', $user->id)->update([
                        'name' => trim(implode(' ', array_filter([
                            $user->first_name,
                            $middleInitial,
                            $user->last_name,
                            $user->name_extension,
                        ]))),
                    ]);
                }
            });
    }

    public function down(): void
    {
        // The original legacy names cannot be reconstructed after synchronization.
    }
};
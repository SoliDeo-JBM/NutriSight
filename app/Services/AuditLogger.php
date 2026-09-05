<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    public static function log(string $action, string $module, string $description): void
    {
        try {
            \App\Jobs\LogAuditEntry::dispatch([
                'user_id' => auth()->id(),
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'ip_address' => Request::ip(),
            ]);
        } catch (\Exception $e) {
            // Fail silently so logging never blocks core application execution
        }
    }
}

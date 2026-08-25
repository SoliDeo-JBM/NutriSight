<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $query = AuditLog::with('user')->latest();

        // If user is Admin (not Super Admin), restrict to encoder actions only
        if ($currentUser && $currentUser->isAdmin()) {
            $query->whereHas('user', function ($q) {
                $q->where('role', 'encoder');
            });
        }

        // Search description, module, action, or user name
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('module', 'like', '%' . $search . '%')
                  ->orWhere('action', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($sub) use ($search) {
                      $sub->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->input('module'));
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        $auditLogs = $query->paginate(20)->withQueryString();

        $baseLogQuery = AuditLog::query();
        if ($currentUser && $currentUser->isAdmin()) {
            $baseLogQuery->whereHas('user', function ($q) {
                $q->where('role', 'encoder');
            });
        }

        $modules = (clone $baseLogQuery)->distinct()->pluck('module');
        $actions = (clone $baseLogQuery)->distinct()->pluck('action');

        $rolePrefix = auth()->user()->isSuperAdmin() ? 'super-admin' : 'admin';

        return view('admin.audit-logs.index', compact('auditLogs', 'modules', 'actions', 'rolePrefix'));
    }
}

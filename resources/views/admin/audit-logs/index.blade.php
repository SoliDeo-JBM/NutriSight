@extends('layouts.dashboard')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">System Audit Logs</h1>
            <p class="text-sm text-gray-500 mt-1">Track and monitor all user activities, system actions, and security events.</p>
        </div>
    </div>

    <!-- Filters & Search Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route($rolePrefix . '.audit-logs.index') }}" class="space-y-4" x-data>
            <!-- Search Bar -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Search Activity / User</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description, user name..." @input.debounce.350ms="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>

            <!-- Filters Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Module Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Module</label>
                    <select name="module" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Modules</option>
                        @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Action Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Action Type</label>
                    <select name="action" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Actions</option>
                        @foreach($actions as $act)
                        <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $act }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-1">
                <a href="{{ route($rolePrefix . '.audit-logs.index') }}" class="text-sm text-gray-600 hover:text-blue-600 inline-flex items-center gap-2">
                    <i class="fas fa-rotate-left"></i> Reset filters
                </a>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 border">No.</th>
                        <th class="px-4 py-3 border">Timestamp</th>
                        <th class="px-4 py-3 border">User</th>
                        <th class="px-4 py-3 border">Module</th>
                        <th class="px-4 py-3 border">Action</th>
                        <th class="px-4 py-3 border">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($auditLogs as $index => $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border">{{ $auditLogs->firstItem() + $index }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap text-xs text-gray-600">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap font-semibold text-slate-800">{{ $log->user->name ?? 'System / Deleted' }} <span class="text-xs text-gray-400 font-normal">({{ ucfirst($log->user->role ?? 'N/A') }})</span></td>
                        <td class="px-4 py-3 border whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800">
                                {{ $log->module }}
                            </span>
                        </td>
                        <td class="px-4 py-3 border whitespace-nowrap">
                            <span class="px-2 py-0.5 rounded text-xs font-bold 
                                    @if(in_array($log->action, ['Created', 'Approved'])) bg-green-100 text-green-800
                                    @elseif(in_array($log->action, ['Deleted', 'Archived', 'Disapproved'])) bg-red-100 text-red-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-4 py-3 border">{{ $log->description }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 border text-center text-gray-500">No audit logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($auditLogs->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <x-pagination :paginator="$auditLogs" />
        </div>
        @endif
    </div>
</div>
@endsection
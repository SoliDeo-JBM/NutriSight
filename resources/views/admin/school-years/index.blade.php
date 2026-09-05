@extends('layouts.dashboard')

@section('content')
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">School Year & Program Management</h1>
                <p class="text-sm text-gray-500 mt-1">Manage academic school years, set active operating year, and archive past records.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Add School Year Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-plus text-blue-600"></i> Add New School Year
                </h2>
                <form method="POST" action="{{ route('super-admin.school-years.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">School Year Name <span class="text-red-500">*</span></label>
                        <input type="text" name="school_year" value="{{ old('school_year') }}" placeholder="e.g. 2026-2027" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">End Date <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg text-sm hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-1"></i> Create School Year
                    </button>
                </form>
            </div>

            <!-- School Years List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 lg:col-span-2">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-blue-600"></i> Academic Years List
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 border">School Year</th>
                                <th class="px-4 py-3 border">Start Date</th>
                                <th class="px-4 py-3 border">End Date</th>
                                <th class="px-4 py-3 border text-center">Status</th>
                                <th class="px-4 py-3 border text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($schoolYears as $sy)
                            <tr class="hover:bg-gray-50 {{ $sy->is_active ? 'bg-emerald-50/50' : '' }}">
                                <td class="px-4 py-3 border font-bold text-gray-900">{{ $sy->school_year }}</td>
                                <td class="px-4 py-3 border whitespace-nowrap text-gray-600">{{ \Carbon\Carbon::parse($sy->start_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 border whitespace-nowrap text-gray-600">{{ \Carbon\Carbon::parse($sy->end_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 border text-center whitespace-nowrap">
                                    @if($sy->is_active)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-check-circle mr-1"></i> Active Operating Year
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                            Archived / Past
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 border text-center whitespace-nowrap">
                                    @if(!$sy->is_active)
                                    <form method="POST" action="{{ route('super-admin.school-years.activate', $sy->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded text-xs font-bold transition">
                                            <i class="fas fa-power-off mr-1"></i> Activate
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-xs text-emerald-600 font-semibold italic">Current Active</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 border text-center text-gray-500">No school years found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

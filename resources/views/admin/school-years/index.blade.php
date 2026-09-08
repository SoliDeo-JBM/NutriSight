@extends('layouts.dashboard')

@section('content')
    <style>
        .school-year-page input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); outline: none; }
        .school-year-page .compact-action { transition: transform .15s ease, box-shadow .15s ease; }
        .school-year-page .compact-action:hover { transform: translateY(-1px); box-shadow: 0 5px 12px rgba(15, 23, 42, .12); }
    </style>
    <div class="school-year-page flex flex-col gap-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ Auth::user()->role === 'admin' ? route('admin.reports.sbfp.annual') : route('super-admin.dashboard') }}" class="mb-3 inline-flex items-center gap-2 rounded-lg border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100"><i class="fas fa-arrow-left"></i> Back</a>
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
                <form method="POST" action="{{ Auth::user()->role === 'admin' ? route('admin.school-years.store') : route('super-admin.school-years.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">School Year <span class="text-red-500">*</span></label>
                            <input type="text" name="school_year" value="{{ old('school_year') }}" placeholder="2026-2027" pattern="[0-9]{4}-[0-9]{4}" maxlength="9" inputmode="numeric" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm">
                            <p class="mt-1 text-xs text-gray-500">Format: four digits, a hyphen, four digits.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">End Date <span class="font-normal text-gray-400">(optional)</span></label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm">
                    </div>
                    <button type="submit" class="compact-action w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg text-sm hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-1"></i> Create School Year
                    </button>
                </form>
            </div>

            <!-- School Years List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 lg:col-span-2">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-blue-600"></i> Academic Years List
                </h2>
                <div class="mb-4 flex flex-col gap-2 sm:flex-row">
                    <input id="schoolYearSearch" type="search" placeholder="Search school year..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm sm:max-w-xs">
                    <select id="schoolYearSort" class="rounded-lg border border-gray-300 px-3 py-2 text-sm sm:w-48"><option value="desc">Newest first</option><option value="asc">Oldest first</option></select>
                </div>
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
                            <tr data-school-year="{{ $sy->year }}" class="school-year-row hover:bg-gray-50 {{ $sy->is_active ? 'bg-emerald-50/50' : '' }}">
                                 <td class="px-4 py-3 border font-bold text-gray-900">{{ $sy->year }}</td>
                                <td class="px-4 py-3 border whitespace-nowrap text-gray-600">{{ \Carbon\Carbon::parse($sy->start_date)->format('M d, Y') }}</td>
                                <td class="px-4 py-3 border whitespace-nowrap text-gray-600">{{ $sy->end_date ? \Carbon\Carbon::parse($sy->end_date)->format('M d, Y') : 'To be determined' }}</td>
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
                                    <form method="POST" action="{{ Auth::user()->role === 'admin' ? route('admin.school-years.activate', $sy->id) : route('super-admin.school-years.activate', $sy->id) }}" class="inline">
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
    <script>
        const schoolYearSearch = document.getElementById('schoolYearSearch');
        const schoolYearSort = document.getElementById('schoolYearSort');
        const schoolYearTable = document.querySelector('tbody');
        const filterSchoolYears = () => {
            const query = schoolYearSearch.value.trim().toLowerCase();
            [...schoolYearTable.querySelectorAll('.school-year-row')].forEach((row) => {
                row.hidden = !row.dataset.schoolYear.toLowerCase().includes(query);
            });
        };
        const sortSchoolYears = () => {
            const rows = [...schoolYearTable.querySelectorAll('.school-year-row')];
            rows.sort((first, second) => schoolYearSort.value === 'asc'
                ? first.dataset.schoolYear.localeCompare(second.dataset.schoolYear)
                : second.dataset.schoolYear.localeCompare(first.dataset.schoolYear));
            rows.forEach((row) => schoolYearTable.appendChild(row));
        };
        schoolYearSearch?.addEventListener('input', filterSchoolYears);
        schoolYearSort?.addEventListener('change', sortSchoolYears);
    </script>
@endsection

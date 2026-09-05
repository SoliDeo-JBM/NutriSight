@extends('layouts.dashboard')

@section('content')
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Advisory Student List</h1>
                <p class="text-sm text-gray-500 mt-1">Master list of advisory students with WHO nutritional metrics.</p>
            </div>
            <a href="{{ route('encoder.students.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 inline-flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus"></i> Add Advisory Student
            </a>
        </div>

        <!-- Filters & Search Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('encoder.students.index') }}" class="space-y-4">
                <!-- Search Bar -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search by Name or LRN / ID</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter student name or LRN..." class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Sex Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sex</label>
                        <select name="sex" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All</option>
                            @foreach($sexes as $sex)
                                <option value="{{ $sex }}" {{ request('sex') == $sex ? 'selected' : '' }}>{{ $sex }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- BMI Category Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">BMI Category</label>
                        <select name="bmi_category" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            @foreach($bmiCategories as $cat)
                                <option value="{{ $cat }}" {{ request('bmi_category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sort By</label>
                        <select name="sort" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @foreach($sortOptions as $key => $label)
                                <option value="{{ $key }}" {{ request('sort', 'latest') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded text-sm font-semibold hover:bg-blue-700">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('encoder.students.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded text-sm font-semibold hover:bg-gray-300">
                        <i class="fas fa-redo mr-2"></i> Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 border">No.</th>
                            <th class="px-4 py-3 border">LRN / ID</th>
                            <th class="px-4 py-3 border">Learner's Name (Last, First, Ext, Middle)</th>
                            <th class="px-4 py-3 border">Birthdate</th>
                            <th class="px-4 py-3 border">Sex</th>
                            <th class="px-4 py-3 border">Weight (kg)</th>
                            <th class="px-4 py-3 border">Height (cm)</th>
                            <th class="px-4 py-3 border">BMI</th>
                            <th class="px-4 py-3 border">BMI Category</th>
                            <th class="px-4 py-3 border">Height for Age</th>
                            <th class="px-4 py-3 border">Remarks</th>
                            <th class="px-4 py-3 border">Grade & Section</th>
                            <th class="px-4 py-3 border">Guardian's Email</th>
                            <th class="px-4 py-3 border">Guardian's Phone Number</th>
                            <th class="px-4 py-3 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($students ?? [] as $index => $student)
                        @php $latestRecord = $student->nutritionalRecords()->latest()->first(); @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 border">{{ $students->firstItem() + $index }}</td>
                            <td class="px-4 py-3 border font-semibold text-slate-800">{{ $student->student_number }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->name_extension }} {{ $student->middle_name }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->birth_date }}</td>
                            <td class="px-4 py-3 border">{{ $student->gender }}</td>
                            <td class="px-4 py-3 border">{{ $latestRecord->weight ?? '-' }}</td>
                            <td class="px-4 py-3 border">{{ $latestRecord->height ?? '-' }}</td>
                            <td class="px-4 py-3 border">{{ $latestRecord->bmi ?? '-' }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">
                                @if($latestRecord)
                                    <span class="px-2 py-0.5 rounded text-xs font-bold 
                                        @if($latestRecord->bmi_category == 'Normal') bg-green-100 text-green-800 
                                        @elseif(in_array($latestRecord->bmi_category, ['Wasted', 'Severely Wasted'])) bg-red-100 text-red-800 
                                        @else bg-amber-100 text-amber-800 @endif">
                                        {{ $latestRecord->bmi_category }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border">{{ $latestRecord->height_for_age ?? 'Normal' }}</td>
                            <td class="px-4 py-3 border">{{ $latestRecord->remarks ?? '-' }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->grade_level }} - {{ $student->section }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->guardian_email ?? '-' }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->guardian_contact ?? '-' }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">
                                 <form action="{{ route('encoder.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to archive this student?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline text-xs font-medium">Archive</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15" class="px-4 py-8 border text-center text-gray-500">No students found matching your criteria. Click "Add Advisory Student" to begin.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($students->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $students->render() }}
            </div>
            @endif
        </div>
    </div>

    <script>
        let searchTimeout;
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.form.submit();
                }, 300);
            });
        }
    </script>
@endsection

@extends('layouts.dashboard')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Advisory Learner List</h1>
            <p class="text-sm text-gray-500 mt-1">Master list of advisory learners with WHO nutritional metrics.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="document.getElementById('change-section-modal').classList.remove('hidden')" class="bg-amber-500 text-white px-4 py-2 rounded text-sm hover:bg-amber-600 inline-flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-pen"></i> Change Section Name
            </button>
            <a href="{{ route('encoder.students.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 inline-flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus"></i> Add Advisory Learner
            </a>
        </div>
    </div>

    <div id="change-section-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/40 p-4" onclick="if(event.target===this)this.classList.add('hidden')">
        <form method="POST" action="{{ route('encoder.students.change-section') }}" class="w-full max-w-md rounded-xl bg-white p-6 shadow-2xl" onclick="event.stopPropagation()">
            @csrf
            @method('PATCH')
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-gray-900">Change Section Name</h2>
                    <p class="mt-1 text-xs text-gray-500">This updates all {{ $user->advisory_section }} learners in the active grade and school year.</p>
                </div>
                <button type="button" onclick="document.getElementById('change-section-modal').classList.add('hidden')" aria-label="Close"><i class="fas fa-times text-gray-400"></i></button>
            </div>
            <label for="new-section" class="mb-1 block text-sm font-semibold text-gray-700">New section name</label>
            <input id="new-section" name="section" value="{{ old('section', $user->advisory_section) }}" required maxlength="255" class="w-full rounded border border-gray-300 p-2 text-sm">
            @error('section')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            <button type="submit" class="mt-5 w-full rounded bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600">Update Whole Class</button>
        </form>
    </div>

    <!-- Filters & Search Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('encoder.students.index') }}" class="space-y-4" x-data>
            <!-- Search Bar -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Search by Name or LRN / ID</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter learner name or LRN..." @input.debounce.350ms="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>

            <!-- Filters Row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Sex Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sex</label>
                    <select name="sex" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All</option>
                        @foreach($sexes as $sex)
                        <option value="{{ $sex }}" {{ request('sex') == $sex ? 'selected' : '' }}>{{ $sex }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- BMI Category Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">BMI Category</label>
                    <select name="bmi_category" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @foreach($bmiCategories as $cat)
                        <option value="{{ $cat }}" {{ request('bmi_category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort By -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sort By</label>
                    <select name="sort" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @foreach($sortOptions as $key => $label)
                        <option value="{{ $key }}" {{ request('sort', 'latest') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-1">
                <a href="{{ route('encoder.students.index') }}" class="text-sm text-gray-600 hover:text-blue-600 inline-flex items-center gap-2">
                    <i class="fas fa-rotate-left"></i> Reset filters
                </a>
            </div>

        </form>
    </div>

    <!-- Learners Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 border">No.</th>
                        <th class="px-4 py-3 border">LRN</th>
                        <th class="px-4 py-3 border">Learner's Name (Last, First, Ext, Middle)</th>
                        <th class="px-4 py-3 border">Birthdate</th>
                        <th class="px-4 py-3 border">Age</th>
                        <th class="px-4 py-3 border">Sex</th>
                        <th class="px-4 py-3 border">Weight (kg)</th>
                        <th class="px-4 py-3 border">Height (cm)</th>
                        <th class="px-4 py-3 border">BMI</th>
                        <th class="px-4 py-3 border">BMI Category</th>
                        <th class="px-4 py-3 border">Height for Age</th>
                        <th class="px-4 py-3 border">Parent/Guardian's Email</th>
                        <th class="px-4 py-3 border">Parent/Guardian's Contact Number</th>
                        <th class="px-4 py-3 border">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($students ?? [] as $index => $student)
                    @php
                    $enrollment = $student->enrollments->first();
                    $baselineRecord = $student->nutritionalRecords()
                    ->where('measurement_period', 'baseline')
                    ->latest()
                    ->first();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border">{{ $students->firstItem() + $index }}</td>
                        <td class="px-4 py-3 border font-semibold text-slate-800">{{ $student->student_number }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->name_extension }} {{ $student->middle_name }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->birth_date?->format('Y-m-d') ?? '-' }}</td>
                        <td class="px-4 py-3 border">{{ $student->birth_date?->age ?? '-' }}</td>
                        <td class="px-4 py-3 border">{{ $student->sex ?? '-' }}</td>
                        <td class="px-4 py-3 border">{{ $baselineRecord->weight ?? '-' }}</td>
                        <td class="px-4 py-3 border">{{ $baselineRecord->height ?? '-' }}</td>
                        <td class="px-4 py-3 border">{{ $baselineRecord->bmi ?? '-' }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">
                            @if($baselineRecord)
                            <span class="px-2 py-0.5 rounded text-xs font-bold 
                                        @if($baselineRecord->bmi_category == 'Normal') bg-green-100 text-green-800
                                        @elseif(in_array($baselineRecord->bmi_category, ['Wasted', 'Severely Wasted'])) bg-red-100 text-red-800
                                        @else bg-amber-100 text-amber-800 @endif">
                                {{ $baselineRecord->bmi_category }}
                            </span>
                            @else
                            <span class="text-gray-400 italic">N/A</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 border">{{ $baselineRecord->height_for_age ?? 'Normal' }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->guardian_email ?? '-' }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->guardian_contact ?? '-' }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">
                            <a href="{{ route('encoder.students.edit', $student->id) }}" class="text-blue-600 hover:underline text-xs font-medium">Edit</a>
                            <button type="button" onclick="openWithdrawModal('{{ route('encoder.students.destroy', $student->id) }}', @js($student->first_name . ' ' . $student->last_name))" class="ml-3 text-rose-600 hover:underline text-xs font-medium">Remove</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" class="px-4 py-8 border text-center text-gray-500">No learners found matching your criteria. Click "Add Advisory Learner" to begin.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($students->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <x-pagination :paginator="$students" />
        </div>
        @endif
    </div>
</div>

<div id="withdraw-student-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/40 p-4" onclick="if(event.target===this)closeWithdrawModal()">
    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="withdraw-student-title" onclick="event.stopPropagation()">
        <div class="mb-4 flex items-start justify-between gap-4">
            <div>
                <h2 id="withdraw-student-title" class="font-bold text-gray-900">Remove Learner</h2>
                <p class="mt-1 text-sm text-gray-600">Are you sure you want to mark <span id="withdraw-student-name" class="font-semibold text-gray-900"></span> as withdrawn?</p>
            </div>
            <button type="button" onclick="closeWithdrawModal()" aria-label="Close" class="text-gray-400 hover:text-gray-700"><i class="fas fa-times"></i></button>
        </div>
        <p class="rounded-lg bg-amber-50 p-3 text-xs leading-5 text-amber-800">The learner’s existing measurements, attendance, feeding, and approval records will be preserved.</p>
        <form id="withdraw-student-form" method="POST" class="mt-5 flex justify-end gap-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeWithdrawModal()" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Cancel</button>
            <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Confirm Remove</button>
        </form>
    </div>
</div>

<script>
    function openWithdrawModal(action, studentName) {
        document.getElementById('withdraw-student-form').action = action;
        document.getElementById('withdraw-student-name').textContent = studentName;
        document.getElementById('withdraw-student-modal').classList.remove('hidden');
        document.getElementById('withdraw-student-modal').classList.add('flex');
    }

    function closeWithdrawModal() {
        document.getElementById('withdraw-student-modal').classList.add('hidden');
        document.getElementById('withdraw-student-modal').classList.remove('flex');
    }

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
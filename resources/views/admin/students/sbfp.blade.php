@extends('layouts.dashboard')

@section('content')
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Complete SBFP List</h1>
                <p class="text-sm text-gray-500 mt-1">School-Based Feeding Program participants, term progress, and approval tracking.</p>
            </div>
            <a href="{{ route('students.print-batch') }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 whitespace-nowrap inline-flex items-center gap-2">
                <i class="fas fa-print"></i> Print Portrait ID QR Sheet
            </a>
        </div>

        <!-- Filters & Search Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('admin.students.sbfp') }}" class="space-y-4">
                <!-- Search Bar -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search by Name or LRN / ID</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter student name or LRN..." class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    <!-- Grade Level Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Grade Level</label>
                        <select name="grade_level" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All Grades</option>
                            @foreach($gradeLevels as $grade)
                                <option value="{{ $grade }}" {{ request('grade_level') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Section Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Section</label>
                        <select name="section" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All Sections</option>
                            @foreach($sections as $sec)
                                <option value="{{ $sec }}" {{ request('section') == $sec ? 'selected' : '' }}>{{ $sec }}</option>
                            @endforeach
                        </select>
                    </div>

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

                    <!-- Approval Status Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Parent Approval</label>
                        <select name="approval_status" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            @foreach($approvalStatuses as $key => $label)
                                <option value="{{ $key }}" {{ request('approval_status') == $key ? 'selected' : '' }}>{{ $label }}</option>
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
                    <a href="{{ route('admin.students.sbfp') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded text-sm font-semibold hover:bg-gray-300">
                        <i class="fas fa-redo mr-2"></i> Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- SBFP Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 border">No.</th>
                            <th class="px-4 py-3 border">LRN / ID</th>
                            <th class="px-4 py-3 border">Learner's Name</th>
                            <th class="px-4 py-3 border">Birthdate / Age / Sex</th>
                            <th class="px-4 py-3 border text-center" colspan="3">Term Progress</th>
                            <th class="px-4 py-3 border">Parent's Approval</th>
                            <th class="px-4 py-3 border text-center">Student QR Code</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="px-4 py-2 border"></th>
                            <th class="px-4 py-2 border text-center text-xs bg-blue-50">Term 1</th>
                            <th class="px-4 py-2 border text-center text-xs bg-blue-50">Term 2</th>
                            <th class="px-4 py-2 border text-center text-xs bg-blue-50">Term 3</th>
                            <th colspan="2" class="px-4 py-2 border"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($students ?? [] as $index => $student)
                        @php 
                            $latestRecord = $student->nutritionalRecords()->latest()->first();
                            $isWasted = $latestRecord && in_array($latestRecord->bmi_category, ['Wasted', 'Severely Wasted']);
                            $termData = [
                                'Term 1' => $student->assessments()->whereMonth('assessment_date', 1)->latest()->first(),
                                'Term 2' => $student->assessments()->whereMonth('assessment_date', 2)->latest()->first(),
                                'Term 3' => $student->assessments()->whereMonth('assessment_date', 3)->latest()->first(),
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 border">{{ $students->firstItem() + $index }}</td>
                            <td class="px-4 py-3 border font-semibold text-slate-800">{{ $student->student_number }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->name_extension }} {{ $student->middle_name }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->birth_date }} ({{ $student->gender }})</td>
                            
                            <!-- Term Data Columns -->
                            @foreach(['Term 1', 'Term 2', 'Term 3'] as $term)
                            <td class="px-4 py-3 border text-center">
                                @if($termData[$term])
                                    @php $data = $termData[$term]; @endphp
                                    <div class="text-xs space-y-1 bg-gray-50 p-2 rounded">
                                        <div><strong>W:</strong> {{ $data->weight_kg }}kg</div>
                                        <div><strong>H:</strong> {{ $data->height_m * 100 }}cm</div>
                                        <div><strong>BMI:</strong> {{ $data->bmi }}</div>
                                        <div class="text-xs font-semibold
                                            @if($data->nutritional_status == 'Normal') text-green-700
                                            @elseif(in_array($data->nutritional_status, ['Wasted', 'Severely Wasted'])) text-red-700
                                            @else text-yellow-700 @endif">
                                            {{ $data->nutritional_status }}
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">No data</span>
                                @endif
                            </td>
                            @endforeach

                            <td class="px-4 py-3 border min-w-[180px]">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($student->parent_approval_status === 'approved' || $isWasted) bg-green-100 text-green-800
                                    @elseif($student->parent_approval_status === 'disapproved') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($student->parent_approval_status ?? ($isWasted ? 'Approved (Auto)' : 'Pending')) }}
                                </span>
                                @if($student->disapproval_reason)
                                    <div class="text-xs text-gray-500 mt-1">Reason: {{ ucfirst(str_replace('_', ' ', $student->disapproval_reason)) }}</div>
                                @endif
                            </td>

                            <td class="px-4 py-3 border text-center whitespace-nowrap">
                                @if($student->is_permitted || $isWasted)
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-1 bg-white border inline-block shadow-sm rounded">
                                            {!! QrCode::size(60)->generate($student->student_number) !!}
                                        </div>
                                        <a href="{{ route('students.id-card', $student->id) }}" target="_blank" class="text-[11px] text-blue-600 hover:underline mt-1">Print ID</a>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">Requires Approval</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 border text-center text-gray-500">No SBFP records found matching your criteria.</td>
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

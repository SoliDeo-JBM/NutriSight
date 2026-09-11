@extends('layouts.dashboard')

@section('content')
<div class="flex flex-col gap-6" x-data="{}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Complete SBFP List</h1>
            <p class="text-sm text-gray-500 mt-1">School-Based Feeding Program participants, period progress, and approval tracking.</p>
        </div>
        <a href="{{ route('students.print-batch') }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 whitespace-nowrap inline-flex items-center gap-2">
            <i class="fas fa-print"></i> Print Portrait ID QR Sheet
        </a>
    </div>

    <!-- Filters & Search Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route($routePrefix . '.students.sbfp') }}" class="space-y-4" x-data>
            <!-- Search Bar -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Search by Name or LRN / ID</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter student name or LRN..." @input.debounce.350ms="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>

            <!-- Filters Row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- Grade Level Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Grade Level</label>
                    <select name="grade_level" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Grades</option>
                        @foreach($gradeLevels as $grade)
                        <option value="{{ $grade }}" {{ request('grade_level') == $grade ? 'selected' : '' }}>{{ $grade == 0 ? 'Kinder' : 'Grade ' . $grade }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Section Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Section</label>
                    <select name="section" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Sections</option>
                        @foreach($sections as $sec)
                        <option value="{{ $sec }}" {{ request('section') == $sec ? 'selected' : '' }}>{{ $sec }}</option>
                        @endforeach
                    </select>
                </div>

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
                <a href="{{ route($routePrefix . '.students.sbfp') }}" class="text-sm text-gray-600 hover:text-blue-600 inline-flex items-center gap-2">
                    <i class="fas fa-rotate-left"></i> Reset filters
                </a>
            </div>

        </form>
    </div>

    @if($routePrefix !== 'super-admin')
    @php
        $profileImageParticipants = collect(isset($students) ? $students->items() : [])->map(function ($student) {
            $participant = $student->enrollments->first()?->sbfpParticipant;
            return $participant ? [
                'id' => $participant->id,
                'name' => trim($student->last_name . ', ' . $student->first_name),
                'image' => $participant->profile_image_url,
            ] : null;
        })->filter()->values();
    @endphp
    <div>
        <x-sbfp-profile-image-modal :participants="$profileImageParticipants" :action="route($routePrefix . '.students.sbfp.profile-images')" />
    </div>
    @endif

    <!-- SBFP Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 border">No.</th>
                        <th class="px-4 py-3 border">LRN / ID</th>
                        <th class="px-4 py-3 border">Learner's Name</th>
                        <th class="px-4 py-3 border">Grade</th>
                        <th class="px-4 py-3 border">Section</th>
                        <th class="px-4 py-3 border">Birthdate</th>
                        <th class="px-4 py-3 border">Age</th>
                        <th class="px-4 py-3 border">Sex</th>
                        <th class="px-4 py-3 border text-center" colspan="3">Period Progress</th>
                        <th class="px-4 py-3 border">Parent's Approval</th>
                        <th class="px-4 py-3 border text-center">Student QR Code</th>
                    </tr>
                    <tr>
                        <th colspan="8" class="px-4 py-2 border"></th>
                        <th class="px-4 py-2 border text-center text-xs bg-blue-50">Baseline</th>
                        <th class="px-4 py-2 border text-center text-xs bg-blue-50">Midline</th>
                        <th class="px-4 py-2 border text-center text-xs bg-blue-50">Endline</th>
                        <th colspan="2" class="px-4 py-2 border"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($students ?? [] as $index => $student)
                    @php
                    $enrollment = $student->enrollments->first();
                    $latestRecord = $student->nutritionalRecords()->latest()->first();
                    $isWasted = $latestRecord && in_array($latestRecord->bmi_category, ['Wasted', 'Severely Wasted']);
                    $periodData = [
                    'Baseline' => $student->periodProgress['Baseline'][0] ?? null,
                    'Midline' => $student->periodProgress['Midline'][0] ?? null,
                    'Endline' => $student->periodProgress['Endline'][0] ?? null,
                    ];
                    $profileData = [
                    'name' => trim($student->last_name . ', ' . $student->first_name . ' ' . $student->name_extension . ' ' . $student->middle_name),
                    'lrn' => $student->student_number,
                    'profile_image_url' => $enrollment?->sbfpParticipant?->profile_image_url,
                    'birthdate' => optional($student->birth_date)->format('Y-m-d') ?? '-',
                    'age' => $student->birth_date?->age ?? '-',
                    'sex' => $student->sex ?? '-',
                    'grade' => $enrollment?->grade_level == 0 ? 'Kinder' : 'Grade ' . ($enrollment?->grade_level ?? '-'),
                    'section' => $enrollment?->section ?? '-',
                    'guardian' => $student->guardian_name ?? '-',
                    'guardian_contact' => $student->guardian_contact ?? '-',
                    'guardian_email' => $student->guardian_email ?? '-',
                    'approval' => ucfirst($student->parent_approval_status ?? ($isWasted ? 'Approved (Auto)' : 'Pending')),
                    'reason' => $student->disapproval_reason ? ucfirst(str_replace('_', ' ', $student->disapproval_reason)) : '-',
                    'periods' => collect($periodData)->map(fn ($measurement) => $measurement ? [
                    'weight' => $measurement->weight,
                    'height' => $measurement->height,
                    'bmi' => $measurement->bmi,
                    ] : null)->all(),
                    ];
                    @endphp
                    <tr class="hover:bg-gray-50 cursor-pointer" data-profile="{{ base64_encode(json_encode($profileData)) }}" @click="$dispatch('student-profile-open', JSON.parse(atob($el.dataset.profile)))">
                        <td class="px-4 py-3 border">{{ $students->firstItem() + $index }}</td>
                        <td class="px-4 py-3 border font-semibold text-slate-800">{{ $student->student_number }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->name_extension }} {{ $student->middle_name }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $enrollment?->grade_level == 0 ? 'Kinder' : 'Grade ' . ($enrollment?->grade_level ?? '-') }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $enrollment?->section ?? '-' }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->birth_date ?? '-' }}</td>
                        <td class="px-4 py-3 border">{{ $student->birth_date?->age ?? '-' }}</td>
                        <td class="px-4 py-3 border">{{ $student->sex ?? '-' }}</td>

                        <!-- Period Data Columns -->
                        @foreach(['Baseline', 'Midline', 'Endline'] as $period)
                        <td class="px-4 py-3 border text-center">
                            @if($periodData[$period])
                            @php $data = $periodData[$period]; @endphp
                            <div class="text-xs space-y-1 bg-gray-50 p-2 rounded">
                                <div><strong>W:</strong> {{ $data->weight }}kg</div>
                                <div><strong>H:</strong> {{ $data->height }}cm</div>
                                <div><strong>BMI:</strong> {{ $data->bmi }}</div>
                            </div>
                            @else
                            <span class="text-gray-400 text-xs italic">No data</span>
                            @endif
                        </td>
                        @endforeach

                        <td class="px-4 py-3 border min-w-[180px]">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($student->parent_approval_status === 'disapproved') bg-red-100 text-red-800
                                    @elseif($student->parent_approval_status === 'approved' || $isWasted) bg-green-100 text-green-800
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
                                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(60)->generate($student->student_number) !!}
                                </div>
                                <a href="{{ route('students.id-card', $student->id) }}" target="_blank" @click.stop class="text-[11px] text-blue-600 hover:underline mt-1">Print ID</a>
                            </div>
                            @else
                            <span class="text-gray-400 text-xs italic">Requires Approval</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="px-4 py-8 border text-center text-gray-500">No SBFP records found matching your criteria.</td>
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

    <x-student-profile-modal />
</div>

@endsection
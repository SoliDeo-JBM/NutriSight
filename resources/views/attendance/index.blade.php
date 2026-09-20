@extends('layouts.dashboard')

@section('content')
@if(in_array($routePrefix, ['encoder', 'admin', 'super-admin'], true))
<div class="space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $routePrefix === 'encoder' ? 'Attendance List' : 'Complete Attendance List' }}</h1>
            <p class="mt-1 text-sm text-slate-500">Record feeding attendance for {{ $routePrefix === 'encoder' ? 'your advisory class' : 'all approved SBFP students' }}.</p>
        </div>
        <form method="GET" action="{{ route($routePrefix . '.attendance.index') }}" class="flex items-end gap-2">
            <div>
                <label for="attendance-date" class="mb-1 block text-xs font-semibold text-slate-600">Feeding date</label>
                <input id="attendance-date" type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm">
            </div>
            @if($routePrefix !== 'encoder')
            <div>
                <label for="attendance-grade" class="mb-1 block text-xs font-semibold text-slate-600">Grade</label>
                <select id="attendance-grade" name="grade_level" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm">
                    <option value="">All grades</option>
                    @foreach($gradeLevels as $grade)
                    <option value="{{ $grade }}" @selected(request('grade_level') == $grade)>{{ $grade == 0 ? 'Kinder' : 'Grade ' . $grade }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="attendance-section" class="mb-1 block text-xs font-semibold text-slate-600">Section</label>
                <select id="attendance-section" name="section" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm">
                    <option value="">All sections</option>
                    @foreach($sections as $section)
                    <option value="{{ $section }}" @selected(request('section') === $section)>{{ $section }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </form>
    </div>

    <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm lg:flex-row lg:items-center">
        <div class="relative flex-1">
            <label for="attendance-search" class="sr-only">Search student name</label>
            <i class="fas fa-search pointer-events-none absolute left-3 top-3 text-slate-400"></i>
            <input id="attendance-search" type="search" placeholder="Search student name..." class="w-full rounded-lg border border-slate-300 py-2 pl-9 pr-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <select id="attendance-status-filter" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
            <option value="all">All statuses</option>
            <option value="present">Present</option>
            <option value="absent">Absent</option>
            <option value="unmarked">Unmarked</option>
        </select>
        <select id="attendance-sort" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
            <option value="name-asc">Name A-Z</option>
            <option value="name-desc">Name Z-A</option>
            <option value="grade-asc">Grade low-high</option>
            <option value="section-asc">Section A-Z</option>
        </select>
    </div>

    @if(!$hasMeal)
    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        No meal is scheduled for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}. Add a meal before marking a student present or absent.
    </div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table id="encoder-attendance-table" class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-100 text-left text-xs font-bold uppercase tracking-wide text-slate-600">
                <tr><th class="px-4 py-3">No.</th><th class="px-4 py-3">Name of pupil</th><th class="px-4 py-3">Grade level</th><th class="px-4 py-3">Section</th><th class="px-4 py-3">Status</th><th class="min-w-[220px] px-4 py-3 text-center">Action</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
            @forelse($sbfpStudents as $student)
                @php
                    $enrollment = $student->enrollments->first();
                    $participantId = $enrollment?->sbfpParticipant?->id;
                    $status = $participantId ? ($attendanceLogs[$participantId]?->status ?? 'unmarked') : 'unmarked';
                    $grade = (int) ($enrollment?->grade_level ?? 0);
                @endphp
                <tr data-name="{{ strtolower($student->last_name . ' ' . $student->first_name) }}" data-grade="{{ $grade }}" data-section="{{ strtolower($enrollment?->section ?? '') }}" data-status="{{ $status }}" class="attendance-row hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-500"></td>
                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $student->last_name }}, {{ $student->first_name }}<div class="text-xs font-normal text-slate-500">{{ $student->student_number }}</div></td>
                    <td class="px-4 py-3">{{ $grade === 0 ? 'Kinder' : 'Grade ' . $grade }}</td>
                    <td class="px-4 py-3">{{ $enrollment?->section }}</td>
                    <td class="px-4 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $status === 'present' ? 'bg-emerald-100 text-emerald-800' : ($status === 'absent' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-600') }}">{{ ucfirst($status) }}</span></td>
                    <td class="min-w-[220px] px-2 py-3"><form action="{{ route($routePrefix . '.attendance.update') }}" method="POST" class="mx-auto grid max-w-[220px] grid-cols-[1fr_1fr_32px] items-center gap-1">@csrf<input type="hidden" name="sbfp_participant_id" value="{{ $participantId }}"><input type="hidden" name="date" value="{{ $date }}"><button type="submit" name="status" value="present" {{ !$hasMeal ? 'disabled' : '' }} class="h-8 w-full rounded-md px-2 py-1.5 text-xs font-semibold {{ $status === 'present' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-emerald-100' }} disabled:cursor-not-allowed disabled:opacity-50">Present</button><button type="submit" name="status" value="absent" {{ !$hasMeal ? 'disabled' : '' }} class="h-8 w-full rounded-md px-2 py-1.5 text-xs font-semibold {{ $status === 'absent' ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-700 hover:bg-rose-100' }} disabled:cursor-not-allowed disabled:opacity-50">Absent</button><button type="submit" name="status" value="unmarked" title="Clear attendance status" aria-label="Clear attendance status" class="h-8 w-8 rounded-md bg-slate-200 px-2 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-300">X</button></form></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">No approved SBFP students found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex justify-center">
        {{ $sbfpStudents->links() }}
    </div>
</div>
<script>
    (() => {
        const table = document.getElementById('encoder-attendance-table');
        const search = document.getElementById('attendance-search');
        const filter = document.getElementById('attendance-status-filter');
        const sort = document.getElementById('attendance-sort');
        const rows = () => [...table.querySelectorAll('.attendance-row')];
        const render = () => {
            const query = search.value.trim().toLowerCase();
            const status = filter.value;
            const visible = rows().filter((row) => row.dataset.name.includes(query) && (status === 'all' || row.dataset.status === status));
            rows().forEach((row) => { row.hidden = !visible.includes(row); });
            visible.forEach((row, index) => { row.querySelector('td').textContent = index + 1; });
        };
        sort.addEventListener('change', () => {
            const [field, direction] = sort.value.split('-');
            const body = table.querySelector('tbody');
            rows().sort((a, b) => { const result = field === 'grade' ? Number(a.dataset.grade) - Number(b.dataset.grade) : a.dataset[field].localeCompare(b.dataset[field], undefined, { numeric: true }); return direction === 'desc' ? -result : result; }).forEach((row) => body.appendChild(row));
            render();
        });
        search.addEventListener('input', render);
        filter.addEventListener('change', render);
        render();
    })();
</script>
@else
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">{{ $routePrefix === 'encoder' ? 'Attendance Dashboard & Calendar' : 'Complete Attendance List' }}</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Side: Interactive Calendar with Month/Year Navigation & Dropdowns -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 lg:col-span-2">
        @php
        $currentCarbon = \Carbon\Carbon::parse($date);
        @endphp

        <div class="flex justify-center mb-6">
            @php
            $previousMonth = $currentCarbon->copy()->subMonth()->startOfMonth();
            $nextMonth = $currentCarbon->copy()->addMonth()->startOfMonth();
            @endphp
            <div class="flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                <a href="{{ route($routePrefix . '.attendance.index', ['date' => $previousMonth->toDateString()] + request()->only(['grade_level', 'section'])) }}" class="flex h-10 w-10 items-center justify-center rounded-md border border-gray-300 bg-white text-lg font-bold text-gray-700 hover:bg-gray-100" aria-label="Previous month">&lt;</a>
                <form method="GET" action="{{ route($routePrefix . '.attendance.index') }}" class="flex items-center gap-1">
                    @if($routePrefix !== 'encoder')
                    <input type="hidden" name="grade_level" value="{{ request('grade_level') }}">
                    <input type="hidden" name="section" value="{{ request('section') }}">
                    @endif
                    <label class="sr-only" for="attendance-month">Select month</label>
                    <select id="attendance-month" name="date" onchange="this.form.submit()" class="h-10 appearance-none border-0 bg-transparent px-2 text-center text-lg font-bold text-gray-800 focus:outline-none focus:ring-0">
                        @for($m = 1; $m <= 12; $m++)
                            @php $monthDate = $currentCarbon->copy()->month($m)->startOfMonth(); @endphp
                            <option value="{{ $monthDate->toDateString() }}" @selected($currentCarbon->month === $m)>{{ $monthDate->format('F') }}</option>
                        @endfor
                    </select>
                    <span aria-label="Calendar year" class="inline-flex h-10 w-20 items-center justify-center text-lg font-bold text-gray-800">{{ $currentCarbon->year }}</span>
                </form>
                <a href="{{ route($routePrefix . '.attendance.index', ['date' => $nextMonth->toDateString()] + request()->only(['grade_level', 'section'])) }}" class="flex h-10 w-10 items-center justify-center rounded-md border border-gray-300 bg-white text-lg font-bold text-gray-700 hover:bg-gray-100" aria-label="Next month">&gt;</a>
            </div>
        </div>
        @if($routePrefix !== 'encoder')
        <form method="GET" action="{{ route($routePrefix . '.attendance.index') }}" class="mb-6 flex flex-wrap items-center justify-center gap-2">
            <input type="hidden" name="date" value="{{ $date }}">
                <select name="grade_level" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm bg-white font-semibold">
                    <option value="">All Grades</option>
                    @foreach($gradeLevels as $grade)
                    <option value="{{ $grade }}" @selected(request('grade_level')==$grade)>{{ $grade == 0 ? 'Kinder' : 'Grade ' . $grade }}</option>
                    @endforeach
                </select>
                <select name="section" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm bg-white font-semibold">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                    <option value="{{ $section }}" @selected(request('section')===$section)>{{ $section }}</option>
                    @endforeach
                </select>
        </form>
        @endif

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-2 text-center">
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
            <div class="font-semibold text-gray-600 text-sm py-2">{{ $day }}</div>
            @endforeach

            @php
            $startOfMonth = $currentCarbon->copy()->startOfMonth();
            $daysInMonth = $startOfMonth->daysInMonth;
            $startDayOfWeek = $startOfMonth->dayOfWeek;
            @endphp

            <!-- Padding for start of month -->
            @for($i = 0; $i < $startDayOfWeek; $i++)
                <div>
        </div>
        @endfor

        @for($d = 1; $d <= $daysInMonth; $d++)
            @php
            $loopDate=$startOfMonth->copy()->day($d)->toDateString();
            $hasLogs = in_array($loopDate, $loggedDates);
            $isSelected = $loopDate === $date;
            $isToday = $loopDate === \Carbon\Carbon::today()->toDateString();
            @endphp
            <a href="{{ route($routePrefix . '.attendance.index', ['date' => $loopDate] + request()->only(['grade_level', 'section'])) }}"
                class="p-4 rounded border text-sm font-semibold transition relative
                       @if($isSelected) ring-2 ring-blue-500 bg-blue-50 @endif
                       @if($hasLogs) bg-emerald-100 text-emerald-800 border-emerald-300 hover:bg-emerald-200 
                       @else bg-gray-50 text-gray-700 hover:bg-gray-100 @endif">
                {{ $d }}
                @if($isToday)
                <span class="absolute top-1 right-1 flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                @endif
            </a>
            @endfor
    </div>
</div>

<!-- Right Side: Daily Roster -->
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex flex-col h-[550px]">
    <h2 class="text-lg font-bold mb-4">Attendance for {{ $date }}</h2>

    <div class="flex-1 overflow-y-auto space-y-3 pr-1">
        @forelse($sbfpStudents as $student)
        @php
        $participantId = $student->enrollments->first()?->sbfpParticipant?->id;
        $log = $participantId ? ($attendanceLogs[$participantId] ?? null) : null;
        $status = $log?->status;
        @endphp
        <div class="flex flex-col p-3 border rounded-lg bg-gray-50 gap-2">
            <div>
                <div class="font-semibold text-sm leading-snug">{{ $student->last_name }}, {{ $student->first_name }}</div>
                <div class="text-xs text-gray-500">{{ $student->student_number }}</div>
            </div>

            <form action="{{ route($routePrefix . '.attendance.update') }}" method="POST" class="grid grid-cols-2 gap-1.5 w-full">
                @csrf
                <input type="hidden" name="sbfp_participant_id" value="{{ $participantId }}">
                <input type="hidden" name="date" value="{{ $date }}">

                <button type="submit" name="status" value="present"
                    class="py-1 text-xs rounded font-medium text-center {{ $status === 'present' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Present
                </button>
                <button type="submit" name="status" value="absent"
                    class="py-1 text-xs rounded font-medium text-center {{ $status === 'absent' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Absent
                </button>
            </form>
        </div>
        @empty
        <p class="text-gray-500 text-center py-6 text-sm">No approved SBFP students found.</p>
        @endforelse
    </div>
</div>
</div>
@endif
@endsection
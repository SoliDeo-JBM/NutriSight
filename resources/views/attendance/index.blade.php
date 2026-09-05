@extends('layouts.dashboard')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Attendance Dashboard & Calendar</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Side: Interactive Calendar with Month/Year Navigation & Dropdowns -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 lg:col-span-2">
            @php
                $currentCarbon = \Carbon\Carbon::parse($date);
                $prevMonth = $currentCarbon->copy()->subMonth()->toDateString();
                $nextMonth = $currentCarbon->copy()->addMonth()->toDateString();
            @endphp

            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('encoder.attendance.index', ['date' => $prevMonth]) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-sm font-semibold text-gray-700">
                        <i class="fas fa-chevron-left mr-1"></i> Prev
                    </a>
                    <a href="{{ route('encoder.attendance.index', ['date' => \Carbon\Carbon::today()->toDateString()]) }}" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded text-sm font-semibold">
                        Today
                    </a>
                    <a href="{{ route('encoder.attendance.index', ['date' => $nextMonth]) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded text-sm font-semibold text-gray-700">
                        Next <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                </div>

                <!-- Month & Year Dropdown Selectors -->
                <form method="GET" action="{{ route('encoder.attendance.index') }}" class="flex items-center gap-2">
                    <select name="month" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm bg-white font-semibold">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $currentCarbon->month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                            </option>
                        @endfor
                    </select>

                    <select name="year" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm bg-white font-semibold">
                        @for($y = \Carbon\Carbon::now()->year - 2; $y <= \Carbon\Carbon::now()->year + 2; $y++)
                            <option value="{{ $y }}" {{ $currentCarbon->year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </form>
            </div>

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
                    <div></div>
                @endfor

                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php
                        $loopDate = $startOfMonth->copy()->day($d)->toDateString();
                        $hasLogs = in_array($loopDate, $loggedDates);
                        $isSelected = $loopDate === $date;
                        $isToday = $loopDate === \Carbon\Carbon::today()->toDateString();
                    @endphp
                    <a href="{{ route('encoder.attendance.index', ['date' => $loopDate]) }}" 
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
                        $log = $attendanceLogs[$student->id] ?? null;
                        $status = $log ? $log->status : 'absent';
                    @endphp
                    <div class="flex items-center justify-between p-3 border rounded-lg bg-gray-50">
                        <div>
                            <div class="font-semibold text-sm">{{ $student->last_name }}, {{ $student->first_name }}</div>
                            <div class="text-xs text-gray-500">{{ $student->student_number }}</div>
                        </div>

                        <form action="{{ route('encoder.attendance.update') }}" method="POST" class="flex gap-1">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                            <input type="hidden" name="date" value="{{ $date }}">
                            
                            <button type="submit" name="status" value="present" 
                                class="px-2.5 py-1 text-xs rounded font-medium {{ $status === 'present' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Present
                            </button>
                            <button type="submit" name="status" value="absent" 
                                class="px-2.5 py-1 text-xs rounded font-medium {{ $status === 'absent' ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Absent
                            </button>
                            <button type="submit" name="status" value="tardy" 
                                class="px-2.5 py-1 text-xs rounded font-medium {{ $status === 'tardy' ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                Tardy
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-6 text-sm">No approved SBFP students found.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

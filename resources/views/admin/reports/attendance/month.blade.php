@extends('layouts.dashboard')

@section('content')
<style>
    .attendance-sheet { font-family: Arial, sans-serif; border: 1px solid #cbd5e1; border-radius: .75rem; box-shadow: 0 10px 30px rgba(15, 23, 42, .05); }
    .attendance-grid { border-collapse: collapse; table-layout: fixed; min-width: 1250px; font-size: 10px; }
    .attendance-grid th, .attendance-grid td { height: 2rem; padding: 0; text-align: center; }
    .attendance-grid th { color: #fff; background: #1e293b; border: 1px solid #475569; font-weight: 700; }
    .attendance-grid td { border: 1px solid #e2e8f0; background: #fff; }
    .attendance-grid .number-column { width: 2.75rem; }
    .attendance-grid .name-column { width: 14rem; text-align: left; border-right: 2px solid #172033; }
    .attendance-grid .grade-column { width: 5.5rem; }
    .attendance-grid .section-column { width: 5rem; }
    .attendance-grid .day-column { width: 2.65rem; }
    .attendance-grid .day-head, .attendance-grid .title-head { height: 2.25rem; border: 1px solid #475569; background: #1e293b; color: #fff; }
    .attendance-grid .present { color: #15803d; font-weight: 800; }
    .attendance-grid .absent { color: #dc2626; font-weight: 700; }
    .attendance-grid .total-row td { border-top: 2px solid #172033; background: #dbeafe; font-weight: 700; }
    @media print { .sidebar, .sidebar-backdrop, .top-header, .no-print { display: none !important; } .main-content { margin-left: 0 !important; width: 100% !important; } .content-body { padding: 0 !important; } .attendance-grid { min-width: 0; width: 100%; font-size: 8px; } @page { size: landscape; margin: 8mm; } }
</style>

<div class="flex flex-col gap-5">
    <div class="no-print flex items-center justify-between gap-3">
        <div><a href="{{ route('admin.reports.sbfp.attendance') }}" class="inline-flex items-center gap-2 rounded-lg border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700"><i class="fas fa-arrow-left"></i> Back</a><h1 class="mt-3 text-xl font-bold text-slate-900">{{ date('F', mktime(0, 0, 0, $month, 1)) }} Attendance · SY {{ $schoolYear->year }}</h1></div>
        <div class="flex flex-wrap gap-2"><a href="{{ route('admin.reports.sbfp.attendance.month.excel', $reportMonth) }}" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white">Excel</a><a href="{{ route('admin.reports.sbfp.attendance.month.docx', $reportMonth) }}" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white">Word</a><a href="{{ route('admin.reports.sbfp.attendance.month.pdf', $reportMonth) }}" class="rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white">PDF</a><a href="{{ route('admin.reports.sbfp.attendance.month.sql', $reportMonth) }}" class="rounded-lg bg-slate-700 px-3 py-2 text-xs font-semibold text-white">SQL</a><button onclick="window.print()" class="rounded-lg bg-slate-700 px-3 py-2 text-xs font-semibold text-white"><i class="fas fa-print mr-1"></i> Print</button></div>
    </div>

    <form method="GET" class="no-print flex flex-wrap items-end gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div><label class="mb-1 block text-xs font-semibold text-slate-700">Grade Level</label><select name="grade" onchange="this.form.submit()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm"><option value="">All grades</option>@foreach($grades as $grade)<option value="{{ $grade }}" {{ (int) $selectedGrade === (int) $grade ? 'selected' : '' }}>{{ $grade === 0 ? 'Kinder' : ($grade === 7 ? 'SPED' : 'Grade ' . $grade) }}</option>@endforeach</select></div>
        <div><label class="mb-1 block text-xs font-semibold text-slate-700">Section</label><select name="section" onchange="this.form.submit()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm"><option value="">All sections</option>@foreach($sections as $sectionOption)<option value="{{ $sectionOption }}" {{ $selectedSection === $sectionOption ? 'selected' : '' }}>{{ $sectionOption }}</option>@endforeach</select></div>
        <div><label class="mb-1 block text-xs font-semibold text-slate-700">Sort</label><select id="attendanceSort" class="rounded-lg border border-slate-200 px-3 py-2 text-sm"><option value="name">Name A-Z</option><option value="grade">Grade</option><option value="section">Section</option></select></div>
    </form>

    <section class="attendance-sheet overflow-x-auto rounded-xl border border-slate-300 bg-white p-3 shadow-sm">
        <table id="attendanceTable" class="attendance-grid w-full">
            <thead><tr><th rowspan="2" class="title-head number-column">No.</th><th rowspan="2" class="title-head name-column">NAME OF PUPIL</th><th rowspan="2" class="title-head grade-column">GRADE LEVEL</th><th rowspan="2" class="title-head section-column">SECTION</th><th colspan="20" class="title-head">ACTUAL FEEDING</th></tr><tr>@foreach(range(1, 20) as $day)<th class="day-column day-head">{{ $day }}</th>@endforeach</tr></thead>
            <tbody>
                @forelse($students as $student)
                    <tr data-name="{{ strtolower($student['name']) }}" data-grade="{{ $student['grade_level'] }}" data-section="{{ strtolower($student['section']) }}"><td class="number-column">{{ $student['number'] }}</td><td class="name-column font-semibold text-slate-800">{{ $student['name'] }}</td><td class="grade-column">{{ $student['grade_level'] === 0 ? 'Kinder' : ($student['grade_level'] === 7 ? 'SPED' : 'Grade ' . $student['grade_level']) }}</td><td class="section-column">{{ $student['section'] }}</td>@foreach($student['days'] as $status)@php($present = in_array(strtolower((string) $status), ['present', 'p', 'served']))<td class="day-column {{ $present ? 'present' : (in_array(strtolower((string) $status), ['absent', 'a']) ? 'absent' : '') }}">{{ $present ? '✓' : (in_array(strtolower((string) $status), ['absent', 'a']) ? 'A' : '') }}</td>@endforeach</tr>
                @empty
                    <tr><td colspan="24" class="py-10 text-center text-sm text-slate-500">No enrolled pupils found.</td></tr>
                @endforelse
                <tr class="total-row"><td colspan="4" class="name-column text-right">TOTAL:</td>@foreach(range(1, 20) as $day)<td>{{ $students->filter(fn ($student) => in_array(strtolower((string) ($student['days'][$day] ?? '')), ['present', 'p', 'served']))->count() ?: '' }}</td>@endforeach</tr>
            </tbody>
        </table>
    </section>
</div>
<script>
    document.getElementById('attendanceSort')?.addEventListener('change', (event) => { const body = document.querySelector('#attendanceTable tbody'); const total = body.querySelector('.total-row'); const rows = [...body.querySelectorAll('tr[data-name]')]; const key = event.target.value; rows.sort((first, second) => first.dataset[key].localeCompare(second.dataset[key], undefined, { numeric: key === 'grade' })); rows.forEach((row) => body.insertBefore(row, total)); });
</script>
@endsection

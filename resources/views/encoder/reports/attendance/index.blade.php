@extends('layouts.dashboard')

@section('content')
<div class="report-hierarchy-shell flex flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <a href="{{ route('encoder.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700"><i class="fas fa-arrow-left"></i><span>Back</span></a>
            <h1 class="mt-3 text-2xl font-bold text-slate-900">SBFP Attendance Report</h1>
            <p class="mt-1 text-sm text-slate-500">Daily feeding attendance for your advisory class.</p>
        </div>
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center gap-3 border-b border-slate-200 bg-gradient-to-r from-white to-blue-50 px-5 py-4">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700"><i class="fas fa-calendar-days"></i></span>
            <div>
                <h2 class="font-bold text-slate-900">SY {{ $schoolYear?->year ?? 'No active school year' }}</h2>
                <p class="mt-1 text-xs font-semibold text-slate-500">{{ Auth::user()->advisory_grade_level !== null && Auth::user()->advisory_section ? 'Grade ' . Auth::user()->advisory_grade_level . ' - Section ' . Auth::user()->advisory_section : 'Advisory assignment not yet configured' }}</p>
            </div>
        </div>
        <div class="space-y-2 p-4">
            @forelse($schoolYear?->attendanceReportMonths ?? [] as $month)
            <a href="{{ route('encoder.reports.sbfp.attendance.month', $month) }}" class="group flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3 text-sm transition hover:border-blue-300 hover:bg-blue-50">
                <span class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-700"><i class="fas fa-calendar-day"></i></span><strong>{{ date('F', mktime(0, 0, 0, $month->month, 1)) }}</strong></span>
                <i class="fas fa-arrow-right text-slate-400 transition group-hover:translate-x-1 group-hover:text-blue-600"></i>
            </a>
            @empty
            <div class="rounded-lg border border-dashed border-slate-200 px-5 py-10 text-center"><i class="fas fa-calendar-xmark text-3xl text-blue-200"></i><p class="mt-3 text-sm font-semibold text-slate-700">No attendance months available</p><p class="mt-1 text-xs text-slate-500">Ask an administrator to create the attendance report month.</p></div>
            @endforelse
        </div>
    </section>
</div>
@endsection

@extends('layouts.dashboard')

@section('content')
<div class="mx-auto max-w-5xl rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
    <div class="flex items-start gap-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600"><i class="fas fa-calendar-check"></i></div>
        <div><h1 class="text-xl font-bold text-slate-900">Attendance Report</h1><p class="mt-1 text-sm text-slate-500">Review SBFP feeding attendance records by school year and reporting period.</p></div>
    </div>
    <div class="mt-8 rounded-lg border border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center"><p class="text-sm text-slate-500">Attendance reporting is ready for the feeding attendance workflow.</p><a href="{{ route('admin.reports.sbfp.index') }}" class="mt-4 inline-block text-sm font-semibold text-blue-600 hover:text-blue-700">Back to SBFP Reports</a></div>
</div>
@endsection

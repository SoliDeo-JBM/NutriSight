@extends('layouts.dashboard')

@section('content')
<div class="mx-auto max-w-5xl rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
    <div class="flex items-start gap-4">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600"><i class="fas fa-chart-bar"></i></div>
        <div><h1 class="text-xl font-bold text-slate-900">SBFP Assessment Report</h1><p class="mt-1 text-sm text-slate-500">Review nutritional progress and program impact across the selected school year.</p></div>
    </div>
    <div class="mt-8 rounded-lg border border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-center"><p class="text-sm text-slate-500">Assessment reporting is ready for the nutritional measurement workflow.</p><a href="{{ route('admin.reports.sbfp.index') }}" class="mt-4 inline-block text-sm font-semibold text-violet-600 hover:text-violet-700">Back to SBFP Reports</a></div>
</div>
@endsection

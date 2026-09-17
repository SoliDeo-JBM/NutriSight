@extends('layouts.dashboard')

@section('content')
<style>
    .report-back-link { display: inline-flex; align-items: center; gap: .45rem; border: 1px solid #dbeafe; border-radius: .5rem; padding: .5rem .75rem; color: #2563eb; background: #eff6ff; font-size: .75rem; font-weight: 600; transition: background .15s ease, border-color .15s ease, transform .15s ease; }
    .report-back-link:hover { border-color: #93c5fd; background: #dbeafe; transform: translateX(-1px); }
    .no-print a[href*="/sql"], .no-print button[onclick="window.print()"] { display: none !important; }
    @media print {.sidebar,.sidebar-backdrop,.top-header,.no-print{display:none!important}.main-content{margin-left:0!important;width:100%!important}.content-body{padding:0!important}@page{size:landscape;margin:8mm}}
</style>
<div class="flex flex-col gap-5">
    <div class="no-print flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div><a href="{{ route('admin.reports.sbfp.index') }}" class="report-back-link"><i class="fas fa-arrow-left"></i><span>Back</span></a><h1 class="mt-3 text-xl font-bold text-gray-900">SBFP Assessment Report - SY {{ $assessment['school_year'] }}</h1><p class="text-xs text-gray-500">PROGRAM IMPACT | ATTENDANCE AND NUTRITION PROGRESS</p></div>
        <div class="flex flex-wrap gap-2"><a href="{{ request()->fullUrl() }}" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-600 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-700"><i class="fas fa-arrows-rotate"></i> Refresh</a><a href="{{ route('admin.reports.sbfp.assessment.excel') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700"><i class="fas fa-file-excel"></i> Excel</a><a href="{{ route('admin.reports.sbfp.assessment.docx') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700"><i class="fas fa-file-word"></i> Word</a><a href="{{ route('admin.reports.sbfp.assessment.pdf') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700"><i class="fas fa-file-pdf"></i> PDF</a></div>
    </div>
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"><div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-bold text-slate-900">Student Attendance Summary</h2><p class="mt-1 text-xs text-slate-500">Students with recorded SBFP attendance</p></div><div class="text-right"><div class="text-2xl font-bold text-emerald-600">{{ $assessment['complete_attendance_rate'] }}%</div><div class="text-xs text-slate-500">complete</div></div></div><div class="mt-6 h-3 overflow-hidden rounded-full bg-rose-100"><div class="h-full bg-emerald-500" style="width: {{ $assessment['complete_attendance_rate'] }}%"></div></div><div class="mt-6 grid grid-cols-2 gap-4"><div class="border-l-4 border-emerald-500 pl-3"><div class="text-2xl font-bold text-slate-900">{{ $assessment['complete_attendance'] }}</div><div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Complete attendance</div><div class="text-sm text-emerald-700">{{ $assessment['complete_attendance_rate'] }}%</div></div><div class="border-l-4 border-rose-500 pl-3"><div class="text-2xl font-bold text-slate-900">{{ $assessment['with_absences'] }}</div><div class="text-xs font-semibold uppercase tracking-wide text-slate-500">With absences</div><div class="text-sm text-rose-700">{{ $assessment['with_absences_rate'] }}%</div></div></div></section>
        <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"><div class="flex items-start justify-between gap-4"><div><h2 class="text-lg font-bold text-slate-900">Endline Nutritional Assessment</h2><p class="mt-1 text-xs text-slate-500">Baseline Wasted or Severely Wasted cohort assessed against endline reports</p></div><div class="text-right"><div class="text-2xl font-bold text-emerald-600">{{ $assessment['recovered_rate'] }}%</div><div class="text-xs text-slate-500">recovered at endline</div></div></div><div class="mt-6 h-3 overflow-hidden rounded-full bg-amber-100"><div class="h-full bg-emerald-500" style="width: {{ $assessment['recovered_rate'] }}%"></div></div><div class="mt-6 grid grid-cols-2 gap-4"><div class="border-l-4 border-emerald-500 pl-3"><div class="text-2xl font-bold text-slate-900">{{ $assessment['recovered'] }}</div><div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Recovered to Normal</div><div class="text-sm text-emerald-700">{{ $assessment['recovered_rate'] }}%</div></div><div class="border-l-4 border-amber-500 pl-3"><div class="text-2xl font-bold text-slate-900">{{ $assessment['still_needing_support'] }}</div><div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Still needing support</div><div class="text-sm text-amber-700">{{ $assessment['still_needing_support_rate'] }}%</div></div></div></section>
    </div>
    <section class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm"><table class="min-w-full border-collapse text-sm"><thead class="bg-slate-800 text-white"><tr><th class="border border-slate-600 px-4 py-3 text-left">Assessment Measure</th><th class="border border-slate-600 px-4 py-3 text-right">Count</th><th class="border border-slate-600 px-4 py-3 text-right">Percentage</th></tr></thead><tbody><tr><td class="border border-slate-200 px-4 py-3 font-semibold">Students with attendance records</td><td class="border border-slate-200 px-4 py-3 text-right">{{ $assessment['attendance_students'] }}</td><td class="border border-slate-200 px-4 py-3 text-right">100%</td></tr><tr><td class="border border-slate-200 px-4 py-3">Complete attendance</td><td class="border border-slate-200 px-4 py-3 text-right">{{ $assessment['complete_attendance'] }}</td><td class="border border-slate-200 px-4 py-3 text-right">{{ $assessment['complete_attendance_rate'] }}%</td></tr><tr><td class="border border-slate-200 px-4 py-3">With absences</td><td class="border border-slate-200 px-4 py-3 text-right">{{ $assessment['with_absences'] }}</td><td class="border border-slate-200 px-4 py-3 text-right">{{ $assessment['with_absences_rate'] }}%</td></tr><tr><td class="border border-slate-200 px-4 py-3 font-semibold">Baseline at-risk nutrition cohort</td><td class="border border-slate-200 px-4 py-3 text-right">{{ $assessment['malnourished'] }}</td><td class="border border-slate-200 px-4 py-3 text-right">100%</td></tr><tr><td class="border border-slate-200 px-4 py-3">Recovered to Normal</td><td class="border border-slate-200 px-4 py-3 text-right">{{ $assessment['recovered'] }}</td><td class="border border-slate-200 px-4 py-3 text-right">{{ $assessment['recovered_rate'] }}%</td></tr><tr><td class="border border-slate-200 px-4 py-3">Still needing support</td><td class="border border-slate-200 px-4 py-3 text-right">{{ $assessment['still_needing_support'] }}</td><td class="border border-slate-200 px-4 py-3 text-right">{{ $assessment['still_needing_support_rate'] }}%</td></tr></tbody></table></section>
    @php
        $renderDemographicRows = function (array $rows, string $type) {
            $total = array_sum(array_column($rows, 'count'));
            $totalComplete = array_sum(array_column($rows, 'complete'));
            $totalAbsences = array_sum(array_column($rows, 'with_absences'));
            $totalRecovered = array_sum(array_column($rows, 'recovered'));
            $totalSupport = array_sum(array_column($rows, 'still_needing_support'));
            foreach (collect($rows)->groupBy('sex') as $sex => $sexRows) {
                foreach ($sexRows as $index => $row) {
                    $rowTotal = $row['count'];
                    $value = $rowTotal . ' (' . ($total ? round($rowTotal / $total * 100, 1) : 0) . '%)';
                    if ($type === 'attendance') {
                        $value = $row['complete'] . ' (' . ($totalComplete ? round($row['complete'] / $totalComplete * 100, 1) : 0) . '%) complete / ' . $row['with_absences'] . ' (' . ($totalAbsences ? round($row['with_absences'] / $totalAbsences * 100, 1) : 0) . '%) with absences';
                    } elseif ($type === 'recovery') {
                        $value = $row['recovered'] . ' (' . ($totalRecovered + $totalSupport ? round($row['recovered'] / ($totalRecovered + $totalSupport) * 100, 1) : 0) . '%) recovered / ' . $row['still_needing_support'] . ' (' . ($totalRecovered + $totalSupport ? round($row['still_needing_support'] / ($totalRecovered + $totalSupport) * 100, 1) : 0) . '%) support';
                    }
                    echo '<tr>';
                    if ($index === 0) {
                        echo '<td rowspan="' . $sexRows->count() . '" class="border border-slate-200 bg-slate-50 px-3 py-2 align-top font-semibold">' . e($sex) . '</td>';
                    }
                    echo '<td class="border border-slate-200 px-3 py-2">' . e($row['age']) . '</td><td class="border border-slate-200 px-3 py-2 text-right">' . e($value) . '</td></tr>';
                }
            }
            if ($type === 'attendance') {
                echo '<tr class="bg-slate-50 font-semibold"><td colspan="2" class="border border-slate-200 px-3 py-2">Total</td><td class="border border-slate-200 px-3 py-2 text-right">' . e($totalComplete . ' (' . ($total ? round($totalComplete / $total * 100, 1) : 0) . '%) complete / ' . $totalAbsences . ' (' . ($total ? round($totalAbsences / $total * 100, 1) : 0) . '%) with absences') . '</td></tr>';
            } elseif ($type === 'recovery') {
                echo '<tr class="bg-slate-50 font-semibold"><td colspan="2" class="border border-slate-200 px-3 py-2">Total</td><td class="border border-slate-200 px-3 py-2 text-right">' . e($totalRecovered . ' (' . ($total ? round($totalRecovered / $total * 100, 1) : 0) . '%) recovered / ' . $totalSupport . ' (' . ($total ? round($totalSupport / $total * 100, 1) : 0) . '%) support') . '</td></tr>';
            } else {
                echo '<tr class="bg-slate-50 font-semibold"><td colspan="2" class="border border-slate-200 px-3 py-2">Total</td><td class="border border-slate-200 px-3 py-2 text-right">' . e($total . ' (100%)') . '</td></tr>';
            }
        };
        $periodTotal = count($assessment['period_demographics']);
        $attendanceBySex = collect($assessment['attendance_demographics'])->groupBy('sex');
        $attendanceSummary = $assessment['attendance_summary'];
        $participantsBySex = collect($assessment['participant_demographics'])->groupBy('sex');
        $participantRows = collect($assessment['participant_demographics']);
        $participantTotal = $participantRows->sum('count');
        $participantAgeSummary = $participantRows->groupBy('age')->map(fn ($rows, $age) => ['age' => $age, 'count' => $rows->sum('count')])->sortByDesc('count')->first();
        $participantSexSummary = $participantRows->groupBy('sex')->map(fn ($rows, $sex) => ['sex' => $sex, 'count' => $rows->sum('count')])->sortByDesc('count')->first();
        $recoveryBySex = collect($assessment['recovery_demographics'])->groupBy('sex');
        $recoveryRows = collect($assessment['recovery_demographics']);
        $recoveryAgeSummary = $recoveryRows->groupBy('age')->map(function ($rows, $age) { $total = $rows->sum('count'); $recovered = $rows->sum('recovered'); return ['age' => $age, 'rate' => $total ? round($recovered / $total * 100, 1) : 0, 'count' => $total]; })->sort(function ($a, $b) { return $b['rate'] <=> $a['rate'] ?: $b['count'] <=> $a['count']; })->first();
        $recoverySexSummary = $recoveryRows->groupBy('sex')->map(function ($rows, $sex) { $total = $rows->sum('count'); $recovered = $rows->sum('recovered'); return ['sex' => $sex, 'rate' => $total ? round($recovered / $total * 100, 1) : 0, 'count' => $total]; })->sort(function ($a, $b) { return $b['rate'] <=> $a['rate'] ?: $b['count'] <=> $a['count']; })->first();
        $transitionBySex = collect($assessment['period_demographics'])->groupBy('sex');
        $transitionRows = collect($assessment['period_demographics']);
        $transitionAgeSummary = $transitionRows->groupBy('age')->map(fn ($rows, $age) => ['age' => $age, 'count' => $rows->count()])->sortByDesc('count')->first();
        $transitionSexSummary = $transitionRows->groupBy('sex')->map(fn ($rows, $sex) => ['sex' => $sex, 'count' => $rows->count()])->sortByDesc('count')->first();
        $transitionPeriods = collect($assessment['period_summary']);
        $endlineNormal = data_get($transitionPeriods->get('endline', []), 'Normal', 0);
        $baselineNormal = data_get($transitionPeriods->get('baseline', []), 'Normal', 0);
        $midlineNormal = data_get($transitionPeriods->get('midline', []), 'Normal', 0);
        $endlineSupport = ($transitionPeriods->get('endline', [])['Wasted'] ?? 0) + ($transitionPeriods->get('endline', [])['Severely Wasted'] ?? 0);
        $transitionCategories = [
            'Normal' => ['bar' => '#10b981', 'soft' => '#d1fae5'],
            'Wasted' => ['bar' => '#f59e0b', 'soft' => '#fef3c7'],
            'Severely Wasted' => ['bar' => '#ef4444', 'soft' => '#fee2e2'],
            'Overweight' => ['bar' => '#6366f1', 'soft' => '#e0e7ff'],
            'Obese' => ['bar' => '#a855f7', 'soft' => '#f3e8ff'],
        ];
    @endphp
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-1">
            <h2 class="text-lg font-bold text-slate-900">Demographic Breakdown of Attendance in the SBFP</h2>
            <p class="text-xs text-slate-500">Attendance records separated by sex and age. Percentages are based on each age or sex total.</p>
        </div>
        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-2">
            @foreach(['Male', 'Female'] as $sex)
                @php $sexRows = $attendanceBySex->get($sex, collect()); $sexTotal = $sexRows->sum('count'); $sexComplete = $sexRows->sum('complete'); $sexAbsences = $sexRows->sum('with_absences'); @endphp
                <div class="overflow-hidden rounded-xl border border-slate-200">
                    <div class="border-b border-slate-600 bg-slate-800 px-4 py-3 text-center"><h3 class="font-bold text-white">{{ $sex }}</h3></div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse text-sm">
                            <thead class="bg-slate-800 text-white"><tr><th class="border border-slate-600 px-3 py-2 text-left">Age</th><th class="border border-slate-600 px-3 py-2 text-right">Total Count</th><th class="border border-slate-600 px-3 py-2 text-right">Complete Attendance</th><th class="border border-slate-600 px-3 py-2 text-right">With Absences</th></tr></thead>
                            <tbody>
                                @forelse($sexRows as $row)
                                    <tr><td class="border border-slate-200 px-3 py-2">{{ $row['age'] }}</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $row['count'] }}</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $row['complete'] }} ({{ $row['attendance_rate'] }}%)</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $row['with_absences'] }} ({{ $row['absence_rate'] }}%)</td></tr>
                                @empty
                                    <tr><td colspan="4" class="border border-slate-200 px-3 py-4 text-center text-slate-500">No attendance records.</td></tr>
                                @endforelse
                                <tr class="bg-slate-50 font-semibold"><td class="border border-slate-200 px-3 py-2">Total</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $sexTotal }}</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $sexComplete }} ({{ $sexTotal ? round($sexComplete / $sexTotal * 100, 1) : 0 }}%)</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $sexAbsences }} ({{ $sexTotal ? round($sexAbsences / $sexTotal * 100, 1) : 0 }}%)</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-5 grid grid-cols-1 gap-3 text-sm md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3"><div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Highest Attendance Age Group</div><div class="mt-1 font-bold text-slate-900">{{ $attendanceSummary['age']['highest']['age'] ?? 'No data' }}{{ isset($attendanceSummary['age']['highest']['age']) ? ' years' : '' }}</div><div class="text-xs text-slate-600">{{ $attendanceSummary['age']['highest']['attendance_rate'] ?? 0 }}% complete ({{ $attendanceSummary['age']['highest']['count'] ?? 0 }} students)</div></div>
            <div class="rounded-lg border border-rose-200 bg-rose-50 p-3"><div class="text-xs font-semibold uppercase tracking-wide text-rose-700">Lowest Attendance Age Group</div><div class="mt-1 font-bold text-slate-900">{{ $attendanceSummary['age']['lowest']['age'] ?? 'No data' }}{{ isset($attendanceSummary['age']['lowest']['age']) ? ' years' : '' }}</div><div class="text-xs text-slate-600">{{ $attendanceSummary['age']['lowest']['attendance_rate'] ?? 0 }}% complete ({{ $attendanceSummary['age']['lowest']['count'] ?? 0 }} students)</div></div>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3"><div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Highest Attendance Sex</div><div class="mt-1 font-bold text-slate-900">{{ $attendanceSummary['sex']['highest']['sex'] ?? 'No data' }}</div><div class="text-xs text-slate-600">{{ $attendanceSummary['sex']['highest']['attendance_rate'] ?? 0 }}% complete ({{ $attendanceSummary['sex']['highest']['count'] ?? 0 }} students)</div></div>
            <div class="rounded-lg border border-rose-200 bg-rose-50 p-3"><div class="text-xs font-semibold uppercase tracking-wide text-rose-700">Lowest Attendance Sex</div><div class="mt-1 font-bold text-slate-900">{{ $attendanceSummary['sex']['lowest']['sex'] ?? 'No data' }}</div><div class="text-xs text-slate-600">{{ $attendanceSummary['sex']['lowest']['attendance_rate'] ?? 0 }}% complete ({{ $attendanceSummary['sex']['lowest']['count'] ?? 0 }} students)</div></div>
        </div>
    </section>
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-bold text-slate-900">Demographic Breakdown of SBFP Participants</h2><p class="text-xs text-slate-500">All approved SBFP participants separated by sex and whole age.</p><div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-2">@foreach(['Male', 'Female'] as $sex) @php $sexRows=$participantsBySex->get($sex, collect()); $sexTotal=$sexRows->sum('count'); @endphp<div class="overflow-hidden rounded-xl border border-slate-200"><div class="border-b border-slate-600 bg-slate-800 px-4 py-3 text-center font-bold text-white">{{ $sex }}</div><table class="min-w-full border-collapse text-sm"><thead class="bg-slate-800 text-white"><tr><th class="border border-slate-600 px-3 py-2 text-left">Age</th><th class="border border-slate-600 px-3 py-2 text-right">Total Count</th><th class="border border-slate-600 px-3 py-2 text-right">Percentage</th></tr></thead><tbody>@forelse($sexRows as $row)<tr><td class="border border-slate-200 px-3 py-2">{{ $row['age'] }}</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $row['count'] }}</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $sexTotal ? round($row['count'] / $sexTotal * 100, 1) : 0 }}%</td></tr>@empty<tr><td colspan="3" class="border border-slate-200 px-3 py-4 text-center text-slate-500">No participants.</td></tr>@endforelse<tr class="bg-slate-50 font-semibold"><td class="border border-slate-200 px-3 py-2">Total</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $sexTotal }}</td><td class="border border-slate-200 px-3 py-2 text-right">100%</td></tr></tbody></table></div>@endforeach</div><div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2"><div class="rounded-lg border border-blue-200 bg-blue-50 p-3"><div class="text-xs font-semibold uppercase tracking-wide text-blue-700">Largest Participant Age Group</div><div class="mt-1 font-bold text-slate-900">{{ $participantAgeSummary['age'] ?? 'No data' }}{{ isset($participantAgeSummary['age']) ? ' years' : '' }}</div><div class="text-xs text-slate-600">{{ $participantAgeSummary['count'] ?? 0 }} participants</div></div><div class="rounded-lg border border-blue-200 bg-blue-50 p-3"><div class="text-xs font-semibold uppercase tracking-wide text-blue-700">Largest Participant Sex Group</div><div class="mt-1 font-bold text-slate-900">{{ $participantSexSummary['sex'] ?? 'No data' }}</div><div class="text-xs text-slate-600">{{ $participantSexSummary['count'] ?? 0 }} participants ({{ $participantTotal ? round(($participantSexSummary['count'] ?? 0) / $participantTotal * 100, 1) : 0 }}%)</div></div></div></section>
    <section class="mt-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="text-lg font-bold text-slate-900">Recovered and Still Needing Support</h2><p class="text-xs text-slate-500">Baseline at-risk participants assessed using endline results.</p><div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-2">@foreach(['Male', 'Female'] as $sex) @php $sexRows=$recoveryBySex->get($sex, collect()); $sexTotal=$sexRows->sum('count'); $sexRecovered=$sexRows->sum('recovered'); $sexSupport=$sexRows->sum('still_needing_support'); @endphp<div class="overflow-hidden rounded-xl border border-slate-200"><div class="border-b border-slate-600 bg-slate-800 px-4 py-3 text-center font-bold text-white">{{ $sex }}</div><table class="min-w-full border-collapse text-sm"><thead class="bg-slate-800 text-white"><tr><th class="border border-slate-600 px-3 py-2 text-left">Age</th><th class="border border-slate-600 px-3 py-2 text-right">Total Count</th><th class="border border-slate-600 px-3 py-2 text-right">Recovered</th><th class="border border-slate-600 px-3 py-2 text-right">Still Needing Support</th></tr></thead><tbody>@forelse($sexRows as $row)<tr><td class="border border-slate-200 px-3 py-2">{{ $row['age'] }}</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $row['count'] }}</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $row['recovered'] }} ({{ $row['count'] ? round($row['recovered'] / $row['count'] * 100, 1) : 0 }}%)</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $row['still_needing_support'] }} ({{ $row['count'] ? round($row['still_needing_support'] / $row['count'] * 100, 1) : 0 }}%)</td></tr>@empty<tr><td colspan="4" class="border border-slate-200 px-3 py-4 text-center text-slate-500">No at-risk participants.</td></tr>@endforelse<tr class="bg-slate-50 font-semibold"><td class="border border-slate-200 px-3 py-2">Total</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $sexTotal }}</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $sexRecovered }} ({{ $sexTotal ? round($sexRecovered / $sexTotal * 100, 1) : 0 }}%)</td><td class="border border-slate-200 px-3 py-2 text-right">{{ $sexSupport }} ({{ $sexTotal ? round($sexSupport / $sexTotal * 100, 1) : 0 }}%)</td></tr></tbody></table></div>@endforeach</div><div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2"><div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3"><div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Highest Recovery Age Group</div><div class="mt-1 font-bold text-slate-900">{{ $recoveryAgeSummary['age'] ?? 'No data' }}{{ isset($recoveryAgeSummary['age']) ? ' years' : '' }}</div><div class="text-xs text-slate-600">{{ $recoveryAgeSummary['rate'] ?? 0 }}% recovered</div></div><div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3"><div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Highest Recovery Sex</div><div class="mt-1 font-bold text-slate-900">{{ $recoverySexSummary['sex'] ?? 'No data' }}</div><div class="text-xs text-slate-600">{{ $recoverySexSummary['rate'] ?? 0 }}% recovered</div></div></div></section>
    <section class="mt-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900">Baseline vs Midline vs Endline Rehabilitation Transition</h2>
        <p class="text-xs text-slate-500">Each bar shows the nutrition-status distribution for the same SBFP cohort at each assessment period.</p>
        <div class="mx-auto mt-6 w-full max-w-5xl">
            <div class="flex h-5 w-full overflow-hidden rounded-full border border-slate-200 bg-slate-100" role="img" aria-label="Baseline, Midline, and Endline nutrition status distribution">
                @foreach(['baseline' => 'Baseline', 'midline' => 'Midline', 'endline' => 'Endline'] as $periodKey => $periodLabel)
                    @php $periodData = $transitionPeriods->get($periodKey, []); $periodTotalCount = array_sum($periodData); @endphp
                    <div class="flex min-w-0 flex-1 border-r border-white last:border-r-0" title="{{ $periodLabel }}: {{ $periodTotalCount }} participants">
                        @foreach($transitionCategories as $category => $colors)
                            @php $categoryCount = $periodData[$category] ?? 0; $categoryWidth = $periodTotalCount ? ($categoryCount / $periodTotalCount * 100) : 0; @endphp
                            @if($categoryCount > 0)<div class="h-full overflow-hidden" style="width: {{ $categoryWidth }}%; background-color: {{ $colors['bar'] }}" title="{{ $periodLabel }} - {{ $category }}: {{ $categoryCount }} ({{ round($categoryWidth, 1) }}%)"></div>@endif
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div class="mt-2 grid grid-cols-3 text-center text-xs font-bold uppercase tracking-wide text-slate-700"><span>Baseline</span><span>Midline</span><span>Endline</span></div>
        </div>
        <div class="mt-6 flex flex-wrap gap-x-5 gap-y-2 border-t border-slate-200 pt-4 text-xs text-slate-700">
            @foreach($transitionCategories as $category => $colors)<span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm" style="background-color: {{ $colors['bar'] }}"></span>{{ $category }}</span>@endforeach
        </div>
        <div class="mt-5 grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-3"><div class="text-xs font-semibold uppercase tracking-wide text-blue-700">Transition Cohort</div><div class="mt-1 font-bold text-slate-900">{{ $periodTotal }} participants</div><div class="text-xs text-slate-600">The same cohort is compared at all three periods.</div></div>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3"><div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Normal Status Progress</div><div class="mt-1 font-bold text-slate-900">{{ $baselineNormal }} → {{ $midlineNormal }} → {{ $endlineNormal }}</div><div class="text-xs text-slate-600">Normal participants from Baseline to Midline to Endline.</div></div>
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 md:col-span-2"><div class="text-xs font-semibold uppercase tracking-wide text-amber-700">Transition Interpretation</div><div class="mt-1 text-slate-700">The bar shows how the cohort shifted across BMI categories over time. By Endline, <strong>{{ $endlineNormal }}</strong> of <strong>{{ $periodTotal }}</strong> participants were Normal, while <strong>{{ $endlineSupport }}</strong> remained Wasted or Severely Wasted and may need continued support.</div></div>
        </div>
    </section>
</div>
@endsection

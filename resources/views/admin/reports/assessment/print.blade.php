<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #111827; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 13px; margin: 20px 0 4px; }
        h3 { background: #1e293b; border: 1px solid #334155; color: #fff; font-size: 11px; margin: 10px 0 0; padding: 5px; text-align: center; }
        .subtitle { text-align: center; color: #475569; }
        table { border-collapse: collapse; width: 100%; margin-top: 8px; }
        th, td { border: 1px solid #334155; padding: 5px; }
        th { background: #e2e8f0; text-align: left; }
        td.number { text-align: right; }
        .note { color: #475569; margin: 2px 0 6px; }
        h2, h3, table, .progress-bar, .legend { page-break-inside: avoid; }
        tr { page-break-inside: avoid; }
        .progress-bar { height: 12px; margin: 0 auto; max-width: 760px; border: 1px solid #cbd5e1; background: #f1f5f9; font-size: 0; white-space: nowrap; }
        .progress-period { display: inline-block; height: 12px; vertical-align: top; width: 33.333%; border-right: 1px solid #fff; }
        .progress-period:last-child { border-right: 0; }
        .progress-segment { display: inline-block; height: 12px; vertical-align: top; }
        .legend { margin: 8px 0 12px; }
        .legend-item { display: inline-block; margin-right: 14px; }
        .legend-color { display: inline-block; height: 9px; width: 9px; margin-right: 3px; }
    </style>
</head>
<body>
    <h1>SCHOOL-BASED FEEDING PROGRAM - ASSESSMENT REPORT</h1>
    <p class="subtitle">Marisol Bliss Elementary School | SY {{ $assessment['school_year'] }}</p>

    <h2>Student Attendance Summary</h2>
    <table>
        <thead><tr><th>Assessment Measure</th><th>Count</th><th>Percentage</th></tr></thead>
        <tbody>
            <tr><td>Students with attendance records</td><td class="number">{{ $assessment['attendance_students'] }}</td><td class="number">100%</td></tr>
            <tr><td>Complete attendance</td><td class="number">{{ $assessment['complete_attendance'] }}</td><td class="number">{{ $assessment['complete_attendance_rate'] }}%</td></tr>
            <tr><td>With absences</td><td class="number">{{ $assessment['with_absences'] }}</td><td class="number">{{ $assessment['with_absences_rate'] }}%</td></tr>
        </tbody>
    </table>

    <h2>Endline Nutritional Assessment</h2>
    <p class="note">Baseline Wasted or Severely Wasted cohort assessed against endline reports.</p>
    <table>
        <thead><tr><th>Assessment Measure</th><th>Count</th><th>Percentage</th></tr></thead>
        <tbody>
            <tr><td>Baseline at-risk nutrition cohort</td><td class="number">{{ $assessment['malnourished'] }}</td><td class="number">100%</td></tr>
            <tr><td>Recovered to Normal at endline</td><td class="number">{{ $assessment['recovered'] }}</td><td class="number">{{ $assessment['recovered_rate'] }}%</td></tr>
            <tr><td>Still needing support at endline</td><td class="number">{{ $assessment['still_needing_support'] }}</td><td class="number">{{ $assessment['still_needing_support_rate'] }}%</td></tr>
        </tbody>
    </table>

    @php
        $attendanceBySex = collect($assessment['attendance_demographics'])->groupBy('sex');
        $attendanceSummary = $assessment['attendance_summary'];
    @endphp
    <h2>Demographic Breakdown of Attendance in the SBFP</h2>
    <p class="note">Attendance records separated by sex and age.</p>
    @foreach(['Male', 'Female'] as $sex)
        @php $sexRows = $attendanceBySex->get($sex, collect()); $sexTotal = $sexRows->sum('count'); $sexComplete = $sexRows->sum('complete'); $sexAbsences = $sexRows->sum('with_absences'); @endphp
        <h3>{{ $sex }}</h3>
        <table>
            <thead><tr><th>Age</th><th>Total Count</th><th>Complete Attendance</th><th>With Absences</th></tr></thead>
            <tbody>
                @forelse($sexRows as $row)
                    <tr><td>{{ $row['age'] }}</td><td>{{ $row['count'] }}</td><td>{{ $row['complete'] }} ({{ $row['attendance_rate'] }}%)</td><td>{{ $row['with_absences'] }} ({{ $row['absence_rate'] }}%)</td></tr>
                @empty
                    <tr><td colspan="4">No attendance records.</td></tr>
                @endforelse
                <tr><td><strong>Total</strong></td><td><strong>{{ $sexTotal }}</strong></td><td><strong>{{ $sexComplete }} ({{ $sexTotal ? round($sexComplete / $sexTotal * 100, 1) : 0 }}%)</strong></td><td><strong>{{ $sexAbsences }} ({{ $sexTotal ? round($sexAbsences / $sexTotal * 100, 1) : 0 }}%)</strong></td></tr>
            </tbody>
        </table>
    @endforeach
    <p class="note"><strong>Attendance summary:</strong> Highest Attendance Age Group: {{ $attendanceSummary['age']['highest']['age'] ?? 'No data' }} ({{ $attendanceSummary['age']['highest']['attendance_rate'] ?? 0 }}%). Lowest Attendance Age Group: {{ $attendanceSummary['age']['lowest']['age'] ?? 'No data' }} ({{ $attendanceSummary['age']['lowest']['attendance_rate'] ?? 0 }}%). Highest Attendance Sex: {{ $attendanceSummary['sex']['highest']['sex'] ?? 'No data' }} ({{ $attendanceSummary['sex']['highest']['attendance_rate'] ?? 0 }}%). Lowest Attendance Sex: {{ $attendanceSummary['sex']['lowest']['sex'] ?? 'No data' }} ({{ $attendanceSummary['sex']['lowest']['attendance_rate'] ?? 0 }}%).</p>

    @foreach(['participant_demographics' => 'Demographic Breakdown of SBFP Participants', 'recovery_demographics' => 'Recovered and Still Needing Support'] as $key => $title)
        @php $rows = collect($assessment[$key]); $bySex = $rows->groupBy('sex'); $total = $rows->sum('count'); @endphp
        <h2>{{ $title }}</h2>
        @foreach(['Male', 'Female'] as $sex)
            @php $sexRows = $bySex->get($sex, collect()); $sexTotal = $sexRows->sum('count'); $recovered = $sexRows->sum('recovered'); $support = $sexRows->sum('still_needing_support'); @endphp
            <h3>{{ $sex }}</h3>
            <table><thead><tr><th>Age</th><th>Total Count</th><th>{{ $key === 'participant_demographics' ? 'Participants (%)' : 'Recovered (%)' }}</th><th>{{ $key === 'recovery_demographics' ? 'Still Needing Support (%)' : '' }}</th></tr></thead><tbody>
                @forelse($sexRows as $row)
                    <tr><td>{{ $row['age'] }}</td><td>{{ $row['count'] }}</td><td>{{ $key === 'participant_demographics' ? $row['count'] . ' (' . ($sexTotal ? round($row['count'] / $sexTotal * 100, 1) : 0) . '%)' : $row['recovered'] . ' (' . ($row['count'] ? round($row['recovered'] / $row['count'] * 100, 1) : 0) . '%)' }}</td>@if($key === 'recovery_demographics')<td>{{ $row['still_needing_support'] }} ({{ $row['count'] ? round($row['still_needing_support'] / $row['count'] * 100, 1) : 0 }}%)</td>@endif</tr>
                @empty
                    <tr><td colspan="4">No data.</td></tr>
                @endforelse
                <tr><td><strong>Total</strong></td><td><strong>{{ $sexTotal }}</strong></td><td><strong>{{ $key === 'participant_demographics' ? $sexTotal . ' (100%)' : $recovered . ' (' . ($sexTotal ? round($recovered / $sexTotal * 100, 1) : 0) . '%)' }}</strong></td>@if($key === 'recovery_demographics')<td><strong>{{ $support }} ({{ $sexTotal ? round($support / $sexTotal * 100, 1) : 0 }}%)</strong></td>@endif</tr>
            </tbody></table>
        @endforeach
        <p class="note"><strong>Summary:</strong> Total participants: {{ $total }}. {{ $key === 'participant_demographics' ? 'Largest participant group: ' . ($rows->groupBy('age')->map->sum('count')->sortDesc()->keys()->first() ?? 'No data') . ' years.' : 'Recovered: ' . $rows->sum('recovered') . ', still needing support: ' . $rows->sum('still_needing_support') . '.' }}</p>
    @endforeach

    <h2>Baseline vs Midline vs Endline Rehabilitation Transition</h2>
    <p class="note">Nutrition status progression for the same approved SBFP cohort across all assessment periods.</p>
    @php
        $transitionRows = collect($assessment['period_demographics']);
        $transitionPeriods = collect($assessment['period_summary']);
        $transitionTotal = $transitionRows->count();
        $transitionCategories = ['Normal' => '#10b981', 'Wasted' => '#f59e0b', 'Severely Wasted' => '#ef4444', 'Overweight' => '#6366f1', 'Obese' => '#a855f7'];
        $baselineNormal = data_get($transitionPeriods->get('baseline', []), 'Normal', 0);
        $midlineNormal = data_get($transitionPeriods->get('midline', []), 'Normal', 0);
        $endlineNormal = data_get($transitionPeriods->get('endline', []), 'Normal', 0);
        $endlineSupport = ($transitionPeriods->get('endline', [])['Wasted'] ?? 0) + ($transitionPeriods->get('endline', [])['Severely Wasted'] ?? 0);
    @endphp
    <div class="progress-bar">
        @foreach(['baseline' => 'Baseline', 'midline' => 'Midline', 'endline' => 'Endline'] as $periodKey => $periodLabel)
            @php $periodData = $transitionPeriods->get($periodKey, []); $periodTotalCount = array_sum($periodData); @endphp
            <div class="progress-period">
                @foreach($transitionCategories as $category => $color)
                    @php $categoryCount = $periodData[$category] ?? 0; $categoryWidth = $periodTotalCount ? $categoryCount / $periodTotalCount * 100 : 0; @endphp
                    @if($categoryCount > 0)<div class="progress-segment" style="width:{{ $categoryWidth }}%; background:{{ $color }};" title="{{ $periodLabel }} - {{ $category }}: {{ $categoryCount }} ({{ round($categoryWidth, 1) }}%)"></div>@endif
                @endforeach
            </div>
        @endforeach
    </div>
    <table style="margin-top:2px;"><tr><td style="border:0; text-align:center; font-weight:bold;">Baseline</td><td style="border:0; text-align:center; font-weight:bold;">Midline</td><td style="border:0; text-align:center; font-weight:bold;">Endline</td></tr><tr><td style="border:0; text-align:center;">{{ array_sum($transitionPeriods->get('baseline', [])) }} participants</td><td style="border:0; text-align:center;">{{ array_sum($transitionPeriods->get('midline', [])) }} participants</td><td style="border:0; text-align:center;">{{ array_sum($transitionPeriods->get('endline', [])) }} participants</td></tr></table>
    <div class="legend">@foreach($transitionCategories as $category => $color)<span class="legend-item"><span class="legend-color" style="background:{{ $color }};"></span>{{ $category }}</span>@endforeach</div>
    <p class="note"><strong>Transition interpretation:</strong> The cohort's Normal status changed from {{ $baselineNormal }} at Baseline to {{ $midlineNormal }} at Midline and {{ $endlineNormal }} at Endline. By Endline, {{ $endlineNormal }} of {{ $transitionTotal }} participants were Normal, while {{ $endlineSupport }} remained Wasted or Severely Wasted and may need continued support.</p>
</body>
</html>

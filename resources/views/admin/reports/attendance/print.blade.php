<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 20px; background: #f1f5f9; color: #111827; font-family: Arial, sans-serif; font-size: 10px; }
        h1 { margin: 0; text-align: center; font-size: 16px; }
        p { margin: 0; text-align: center; }
        .print-toolbar { display: flex; justify-content: space-between; gap: 12px; max-width: 1400px; margin: 0 auto 16px; }
        .print-toolbar a, .print-toolbar button { border: 0; border-radius: 6px; padding: 10px 14px; color: #fff; font-size: 13px; font-weight: 700; text-decoration: none; cursor: pointer; }
        .print-toolbar a { background: #475569; }
        .print-toolbar button { background: #c2410c; }
        .report-page { max-width: 1400px; margin: 0 auto; padding: 24px; overflow-x: auto; background: #fff; box-shadow: 0 1px 5px #0f172a20; }
        .export-header { position: relative; text-align: center; margin-bottom: 12px; }
        .export-logo { position: absolute; top: 0; width: 52px; height: 52px; object-fit: contain; }
        .export-logo.left { left: 35px; }
        .export-logo.right { right: 35px; }
        .export-heading { font-size: 11px; }
        .period { margin-top: 4px; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; min-width: 1100px; table-layout: fixed; }
        th, td { border: 1px solid #333; padding: 4px 2px; text-align: center; vertical-align: middle; }
        th { background: #d9e2f3; }
        .number-column { width: 28px; }
        .name-column { width: 170px; text-align: left; overflow-wrap: anywhere; word-wrap: break-word; }
        .grade-column { width: 55px; }
        .section-column { width: 75px; overflow-wrap: anywhere; word-wrap: break-word; }
        .day-column { width: 18px; padding-left: 0; padding-right: 0; }
        .present { color: #15803d; font-weight: bold; }
        .absent { color: #dc2626; font-weight: bold; }
        .total-row td { background: #dbeafe; font-weight: bold; }
        .signatures { margin-top: 20px; table-layout: auto; }
        .signatures td { width: 50%; border: 0; padding: 0 20px; text-align: left; }
        .signature-stack { width: 190px; }
        .signature-title { font-weight: bold; }
        .signature-gap { height: 22px; }
        .signature-line { height: 1px; border-bottom: 1px solid #333; }
        .signature-name { margin-top: 4px; font-weight: bold; text-align: center; }
        .signature-role { margin-top: 2px; text-align: center; }
        .generated-at { position: fixed; bottom: 0; right: 0; font-size: 8px; color: #555; }

        @page { size: landscape; margin: 8mm; }
        @media print {
            body { padding: 0; background: #fff; font-size: 8px; }
            .no-print { display: none !important; }
            .report-page { max-width: none; margin: 0; padding: 0; overflow: visible; box-shadow: none; }
            table { min-width: 0; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; page-break-inside: avoid; }
            th, .total-row td { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>

<body>
    @php($daysInMonth = (int) date('t', mktime(0, 0, 0, $month->month, 1, $calendarYear)))
    <div class="print-toolbar no-print">
        <a href="{{ route($attendanceRoutePrefix . '.attendance.month', array_merge(['month' => $month], array_filter(['grade' => $selectedGrade, 'section' => $selectedSection], fn ($value) => $value !== null && $value !== ''))) }}">Back to attendance</a>
        <button type="button" onclick="window.print()">Print / Save as PDF</button>
    </div>

    <main class="report-page">
        <div class="export-header">
            <img class="export-logo left" src="{{ $schoolLogoUrl }}" alt="School logo">
            <img class="export-logo right" src="{{ $depedLogoUrl }}" alt="Department of Education seal">
            <p class="export-heading">Department of Education</p>
            <p class="export-heading">Bureau of Learner Support Services</p>
            <h1>SCHOOL-BASED FEEDING PROGRAM - RECORD OF DAILY FEEDING</h1>
            <p class="period">{{ date('F', mktime(0, 0, 0, $month->month, 1)) }} Attendance · SY {{ $month->schoolYear?->year }}</p>
        </div>

        <table>
        <thead>
            <tr>
                <th rowspan="2" class="number-column">No.</th>
                <th rowspan="2" class="name-column">NAME OF PUPIL</th>
                <th rowspan="2" class="grade-column">GRADE LEVEL</th>
                <th rowspan="2" class="section-column">SECTION</th>
                <th colspan="{{ $daysInMonth }}">ACTUAL FEEDING</th>
            </tr>
            <tr>
                @foreach(range(1, $daysInMonth) as $day)<th class="day-column">{{ $day }}</th>@endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr>
                <td>{{ $student['number'] }}</td>
                <td class="name-column">{{ $student['name'] }}</td>
                <td>{{ $student['grade_level'] === 0 ? 'Kinder' : ($student['grade_level'] === 7 ? 'SPED' : 'Grade ' . $student['grade_level']) }}</td>
                <td>{{ $student['section'] }}</td>
                @foreach($student['days'] as $status)
                    @php($status = strtolower((string) $status))
                    <td class="day-column {{ in_array($status, ['present', 'p', 'served']) ? 'present' : (in_array($status, ['absent', 'a']) ? 'absent' : '') }}">{{ in_array($status, ['present', 'p', 'served']) ? '✓' : (in_array($status, ['absent', 'a']) ? 'A' : '') }}</td>
                @endforeach
            </tr>
            @empty
            <tr><td colspan="{{ 4 + $daysInMonth }}">No enrolled pupils found.</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" class="name-column">TOTAL:</td>
                @foreach(range(1, $daysInMonth) as $day)
                    <td class="day-column">{{ $dailyTotals[$day] ?: '' }}</td>
                @endforeach
            </tr>
        </tbody>
        </table>

        <table class="signatures">
        <tr>
            <td>
                <div class="signature-stack">
                    <div class="signature-title">Prepared by:</div>
                    <div class="signature-gap"></div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $adminName }}</div>
                    <div class="signature-role">Project Development Officer</div>
                </div>
            </td>
            <td>
                <div class="signature-stack">
                    <div class="signature-title">Noted by:</div>
                    <div class="signature-gap"></div>
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $superAdminName }}</div>
                    <div class="signature-role">School Head</div>
                </div>
            </td>
        </tr>
        </table>
        <div class="generated-at">Generated: {{ now()->format('M d, Y h:i A') }}</div>
    </main>
</body>

</html>

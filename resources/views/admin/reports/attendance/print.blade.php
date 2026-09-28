<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body { margin: 0; padding: 0; font-family: Arial, sans-serif; font-size: 7px; }
        h1 { margin: 0; text-align: center; font-size: 13px; }
        p { margin: 0; text-align: center; }
        .export-header { position: relative; text-align: center; margin-bottom: 8px; }
        .export-logo { position: absolute; top: 0; width: 46px; height: 46px; }
        .export-logo.left { left: 35px; }
        .export-logo.right { right: 35px; }
        .export-heading { font-size: 9px; }
        .period { margin-top: 3px; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; table-layout: fixed; }
        th, td { border: 1px solid #333; padding: 3px 2px; text-align: center; vertical-align: middle; }
        th { background: #d9e2f3; }
        .number-column { width: 28px; }
        .name-column { width: 125px; text-align: left; }
        .grade-column { width: 48px; }
        .section-column { width: 42px; }
        .present { color: #15803d; font-weight: bold; }
        .absent { color: #dc2626; font-weight: bold; }
        .total-row td { background: #dbeafe; font-weight: bold; }
        .signatures { margin-top: 16px; table-layout: auto; }
        .signatures td { width: 50%; border: 0; padding: 0 20px; text-align: left; }
        .signature-stack { width: 190px; }
        .signature-title { font-weight: bold; }
        .signature-gap { height: 22px; }
        .signature-line { height: 1px; border-bottom: 1px solid #333; }
        .signature-name { margin-top: 4px; font-weight: bold; text-align: center; }
        .signature-role { margin-top: 2px; text-align: center; }
    </style>
</head>

<body>
    @php($daysInMonth = (int) date('t', mktime(0, 0, 0, $month->month, 1, $calendarYear)))
    <div class="export-header">
        <img class="export-logo left" src="{{ \App\Services\SchoolLogoService::path($month->school_year_id) }}" alt="School logo">
        <img class="export-logo right" src="{{ \App\Services\SchoolLogoService::depedPath($month->school_year_id) }}" alt="Department of Education seal">
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
                @foreach(range(1, $daysInMonth) as $day)<th>{{ $day }}</th>@endforeach
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
                    <td class="{{ in_array($status, ['present', 'p', 'served']) ? 'present' : (in_array($status, ['absent', 'a']) ? 'absent' : '') }}">{{ in_array($status, ['present', 'p', 'served']) ? '✓' : (in_array($status, ['absent', 'a']) ? 'A' : '') }}</td>
                @endforeach
            </tr>
            @empty
            <tr><td colspan="{{ 4 + $daysInMonth }}">No enrolled pupils found.</td></tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" class="name-column">TOTAL:</td>
                @foreach(range(1, $daysInMonth) as $day)
                    <td>{{ $students->filter(fn ($student) => in_array(strtolower((string) ($student['days'][$day] ?? '')), ['present', 'p', 'served']))->count() ?: '' }}</td>
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
</body>

</html>

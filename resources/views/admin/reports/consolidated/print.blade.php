<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 8px;
        }

        h1 {
            margin: 0;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }

        p {
            margin: 0;
            text-align: center;
        }

        .export-heading {
            font-size: 10px;
        }

        .export-heading+.export-heading {
            margin-bottom: 5px;
        }

        .period {
            font-style: italic;
            margin-bottom: 7px;
        }

        .period-label,
        .period-month {
            color: #2563eb;
        }

        .school-year {
            font-weight: bold;
        }

        .export-header {
            position: relative;
            text-align: center;
            margin-bottom: 2px;
        }

        .export-logo {
            position: absolute;
            top: 0;
            width: 48px;
            height: 48px;
        }

        .export-logo.left {
            left: 50px;
        }

        .export-logo.right {
            right: 50px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 7px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background: #d9e2f3;
        }

        .signatures {
            width: 100%;
            margin-top: 16px;
            text-align: center;
            font-size: 9px;
        }

        .signatures td {
            width: 50%;
            border: 0;
            padding: 0 15px 0 50px;
            text-align: left;
        }

        .signature-stack {
            width: 190px;
        }

        .signature-title {
            text-align: left;
            font-weight: bold;
        }

        .signature-gap {
            height: 24px;
        }

        .signature-line {
            width: 190px;
            height: 1px;
            border-bottom: 1px solid #333;
        }

        .signature-name {
            margin-top: 4px;
            font-weight: bold;
            text-align: center;
        }

        .signature-role {
            margin-top: 2px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="export-header">
        <img class="export-logo left" src="{{ \App\Services\SchoolLogoService::path() }}" alt="School logo">
        <img class="export-logo right" src="{{ public_path('images/id/deped.png') }}" alt="Department of Education seal">
        <p class="export-heading">Department of Education</p>
        <p class="export-heading">Bureau of Learner Support Services</p>
        <h1>NUTRITIONAL STATUS REPORT OF MARISOL BLISS ELEMENTARY SCHOOL</h1>
        <p class="period"><span class="period-label">{{ ucfirst($period?->measurement_period ?? 'Summary') }}</span> <span class="period-month">({{ $period?->month ? date('F', mktime(0, 0, 0, $period->month, 1)) : 'Month' }})</span> SY <span class="school-year">{{ $schoolYear?->year }}</span></p>
    </div>
    <table>
        <thead>
            <tr>
                <th rowspan="3">Grade Levels</th>
                <th colspan="2" rowspan="3">Enrollment</th>
                <th colspan="2" rowspan="2">Pupils Weighed</th>
                <th colspan="10">BODY MASS INDEX (BMI)</th>
                <th colspan="8">HEIGHT-FOR-AGE (HFA)</th>
                <th colspan="2" rowspan="2">Pupils Taken Height</th>
            </tr>
            <tr>
                <th colspan="2">Severely Wasted</th>
                <th colspan="2">Wasted</th>
                <th colspan="2">Normal</th>
                <th colspan="2">Overweight</th>
                <th colspan="2">Obese</th>
                <th colspan="2">Severely Stunted</th>
                <th colspan="2">Stunted</th>
                <th colspan="2">Normal</th>
                <th colspan="2">Tall</th>
            </tr>
            <tr>
                <th>No.</th>
                <th>%</th>@foreach(['No.','%','No.','%','No.','%','No.','%','No.','%','No.','%','No.','%','No.','%','No.','%','No.','%'] as $heading)<th>{{ $heading }}</th>@endforeach
            </tr>
        </thead>
        <tbody>
            @foreach(collect($rows)->groupBy('grade_level') as $gradeRows)
            @foreach($gradeRows as $row)
            <tr>
                @if($loop->first)<td rowspan="3">{{ $row['grade_level'] === -1 ? 'GRAND TOTAL' : ($row['grade_level'] === 7 ? 'SPED' : ($row['grade_level'] === 0 ? 'Kinder' : 'Grade '.$row['grade_level'])) }}</td>@endif
                <td>{{ $row['sex'] }}</td>
                @foreach(\App\Exports\DepEdForm1Export::values($row) as $value)
                @if($loop->index > 1)<td>{{ $value }}</td>@endif
                @endforeach
            </tr>
            @endforeach
            @endforeach
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
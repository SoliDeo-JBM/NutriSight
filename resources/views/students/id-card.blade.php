@php
    $profileImage = $enrollment->sbfpParticipant?->profile_image_url ?: asset('images/anonymous-profile.svg');
    $gradeLabel = $enrollment->grade_level == 0 ? 'Kinder' : 'Grade ' . $enrollment->grade_level;
    $schoolYearLabel = $schoolYear?->year ?? '0000-0000';
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Student ID - {{ $student->first_name }} {{ $student->last_name }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/id/mbes-logo-1.svg') }}">
    <style>
        * { box-sizing: border-box; }
        @media print { .no-print { display: none !important; } body { margin: 0; background: white; } }
        body { margin: 0; min-height: 100vh; padding: 40px; background: #f1f5f9; font-family: Arial, sans-serif; text-align: center; }
        .id-card { position: relative; width: 324px; height: 204px; margin: 0 auto 24px; overflow: hidden; background: #fff5f5; border: 1px solid #000; border-radius: 5px; color: #111827; }
        .id-header { position: absolute; z-index: 4; top: 15px; left: 0; width: 100%; height: 30px; padding: 6px 8px 4px 70px; background: #1818ef; color: #fff; text-align: left; font-size: 9px; font-weight: 700; line-height: 10px; opacity: 1; }
        .id-logo { position: absolute; z-index: 5; top: 11px; left: 21px; width: 39px; height: 39px; }
        .id-background-logo { position: absolute; z-index: 0; top: 23px; left: 88px; width: 163px; height: 163px; pointer-events: none; }
        .id-profile { position: absolute; z-index: 2; display: block; top: 74px; left: 21px; width: 90px; height: 90px; border: 1px solid #000; background: #d9d9d9; object-fit: cover; }
        .id-meta { position: absolute; z-index: 2; top: 82px; left: 123px; width: 108px; text-align: left; font-size: 8px; line-height: 12px; }
        .id-meta strong { display: block; font-size: 10px; line-height: 12px; overflow-wrap: anywhere; }
        .id-sbfp { position: absolute; z-index: 2; top: 169px; left: 21px; width: 90px; text-align: center; font-size: 7px; line-height: 9px; }
        .id-year { position: absolute; z-index: 2; top: 47px; left: 0; width: 100%; font-size: 7px; font-weight: 700; color: #111827; text-align: center; }
        .id-qr { position: absolute; z-index: 2; top: 119px; left: 240px; width: 69px; height: 69px; padding: 2px; border: 1px solid #000; background: #d9d9d9; }
        .id-qr svg { display: block; width: 63px; height: 63px; }
        .id-attendance { position: absolute; z-index: 2; top: 132px; left: 151px; width: 82px; font-size: 7px; line-height: 10px; text-align: right; }
        .id-footer { position: absolute; z-index: 2; bottom: 4px; left: 116px; width: 120px; font-size: 6px; line-height: 7px; text-align: center; }
        .print-button { border: 0; border-radius: 4px; background: #2563eb; color: #fff; padding: 10px 20px; font-weight: 700; cursor: pointer; }
    </style>
</head>
<body>
    <div class="id-card">
        <img class="id-logo" src="{{ asset('images/id/mbes-logo-1.svg') }}" alt="MBES logo">
        <div class="id-header">MARISOL BLISS ELEMENTARY SCHOOL<br><span style="font-size: 7px; font-weight: 400;">SCHOOL-BASED FEEDING PROGRAM ID</span></div>
        <img class="id-background-logo" src="{{ asset('images/id/mbes-logo-2.svg') }}" alt="">
        <div class="id-year">S.Y {{ $schoolYearLabel }}</div>
        <img class="id-profile" src="{{ $profileImage }}" alt="Profile image of {{ $student->first_name }} {{ $student->last_name }}">
        <div class="id-meta">
            <strong>{{ $student->last_name }}, {{ $student->first_name }}</strong>
            <span>Section {{ $enrollment->section }} - {{ $gradeLabel }}</span>
        </div>
        <div class="id-qr">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(63)->margin(0)->generate($student->student_number) !!}</div>
        <div class="id-attendance">Scan For<br>Daily SBFP<br>Attendance</div>
        <div class="id-sbfp">SBFP ID NUMBER<br><strong>{{ $student->student_number }}</strong></div>
        <div class="id-footer">OFFICIAL SBFP PARTICIPANT</div>
    </div>
    <button class="no-print print-button" onclick="window.print()">Print Landscape ID</button>
</body>
</html>

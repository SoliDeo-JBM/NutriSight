<!DOCTYPE html>
<html>
<head>
    <title>Batch Landscape ID QR Code Printing (A4/Letter)</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/id/mbes-logo-1.svg') }}">
    <style>
        * { box-sizing: border-box; }
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; background: white; }
            .page { page-break-after: always; box-shadow: none !important; }
        }
        body { margin: 0; padding: 32px; background: #f1f5f9; font-family: Arial, sans-serif; }
        .toolbar { max-width: 1080px; margin: 0 auto 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .toolbar h1 { margin: 0; font-size: 18px; }
        .print-button { border: 0; border-radius: 4px; background: #2563eb; color: white; padding: 10px 16px; font-weight: 700; cursor: pointer; }
        .page { width: 8.5in; min-height: 11in; margin: 0 auto 24px; padding: .5in; display: grid; grid-template-columns: repeat(2, 324px); grid-auto-rows: 204px; align-content: start; justify-content: center; gap: .25in; background: white; box-shadow: 0 0 10px rgb(0 0 0 / 10%); }
        .id-landscape { position: relative; width: 324px; height: 204px; overflow: hidden; background: #fff5f5; border: 1px dashed #64748b; border-radius: 5px; color: #111827; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .id-header { position: absolute; z-index: 4; top: 15px; left: 0; width: 100%; height: 30px; background: #1818ef; color: white; text-align: left; padding: 6px 8px 4px 70px; font-size: 9px; font-weight: 700; line-height: 10px; opacity: 1; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .id-logo { position: absolute; z-index: 5; top: 11px; left: 21px; width: 39px; height: 39px; }
        .id-background-logo { position: absolute; z-index: 0; top: 23px; left: 88px; width: 163px; height: 163px; pointer-events: none; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .id-profile { position: absolute; z-index: 2; display: block; top: 74px; left: 21px; width: 90px; height: 90px; border: 1px solid #000; background: #d9d9d9; object-fit: cover; }
        .id-details { position: absolute; z-index: 2; top: 82px; left: 123px; width: 108px; text-align: left; font-size: 8px; line-height: 12px; }
        .id-details .name { display: block; font-size: 10px; font-weight: 700; line-height: 12px; overflow-wrap: anywhere; }
        .id-sbfp { position: absolute; z-index: 2; top: 169px; left: 21px; width: 90px; text-align: center; font-size: 7px; line-height: 9px; }
        .id-year { position: absolute; z-index: 2; top: 47px; left: 0; width: 100%; font-size: 7px; font-weight: 700; color: #111827; text-align: center; }
        .id-qr { position: absolute; z-index: 2; top: 119px; left: 240px; width: 69px; height: 69px; padding: 2px; border: 1px solid #000; background: #d9d9d9; }
        .id-qr svg { display: block; width: 63px; height: 63px; }
        .id-attendance { position: absolute; z-index: 2; top: 132px; left: 151px; width: 82px; font-size: 7px; line-height: 10px; text-align: right; }
        .id-footer { position: absolute; z-index: 2; bottom: 3px; left: 116px; width: 120px; font-size: 6px; line-height: 7px; text-align: center; }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <h1>SBFP Student Landscape IDs</h1>
        <button class="print-button" onclick="window.print()">Print All IDs</button>
    </div>

    @foreach($students->chunk(8) as $pageStudents)
    <div class="page">
        @foreach($pageStudents as $student)
        @php
            $enrollment = $student->enrollments->first();
            $participant = $enrollment?->sbfpParticipant;
            $gradeLabel = $enrollment?->grade_level == 0 ? 'Kinder' : ($enrollment?->grade_level == 7 ? 'SPED' : 'Grade ' . ($enrollment?->grade_level ?? '-'));
        @endphp
        <div class="id-landscape">
            <img class="id-logo" src="{{ asset('images/id/mbes-logo-1.svg') }}" alt="MBES logo">
            <div class="id-header">MARISOL BLISS ELEMENTARY SCHOOL<br><span style="font-size: 7px; font-weight: 400;">SCHOOL-BASED FEEDING PROGRAM ID</span></div>
            <img class="id-background-logo" src="{{ asset('images/id/mbes-logo-2.svg') }}" alt="">
            <div class="id-year">S.Y {{ $schoolYear?->year ?? '0000-0000' }}</div>
            <img class="id-profile" src="{{ $participant?->profile_image_url ?: asset('images/anonymous-profile.svg') }}" alt="Profile image of {{ $student->first_name }} {{ $student->last_name }}">
            <div class="id-details">
                <div class="name">{{ $student->last_name }}, {{ $student->first_name }}</div>
                <div>Section {{ $enrollment?->section ?? '-' }} - {{ $gradeLabel }}</div>
            </div>
            <div class="id-qr">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(63)->margin(0)->generate($student->student_number) !!}</div>
            <div class="id-attendance">Scan For<br>Daily SBFP<br>Attendance</div>
            <div class="id-sbfp">SBFP ID NUMBER<br><strong>{{ $student->student_number }}</strong></div>
            <div class="id-footer">OFFICIAL SBFP PARTICIPANT</div>
        </div>
        @endforeach
    </div>
    @endforeach
</body>
</html>

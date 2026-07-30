<!DOCTYPE html>
<html>
<head>
    <title>Batch Portrait ID QR Code Printing (A4/Letter)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 0; background: white; }
            .page { page-break-after: always; height: 100vh; }
        }
        .page {
            width: 8.5in;
            height: 11in;
            margin: 0 auto;
            background: white;
            padding: 0.5in;
            box-sizing: border-box;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 0.25in;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .id-portrait {
            width: 2.125in;
            height: 3.375in;
            border: 1px dashed #94a3b8;
            padding: 0.12in;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: white;
            box-sizing: border-box;
            margin: auto;
        }
    </style>
</head>
<body class="bg-gray-100 py-8">
    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print">
        <h1 class="text-xl font-bold">SBFP Student Portrait ID Cutouts (Letter / A4 Paginated)</h1>
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Print All Pages</button>
    </div>

    @foreach($students->chunk(9) as $pageStudents)
    <div class="page">
        @foreach($pageStudents as $student)
        <div class="id-portrait">
            <div>
                <div class="text-[9px] font-bold uppercase text-slate-800 leading-tight">Marisol Bliss Elementary</div>
                <div class="text-[8px] text-emerald-600 font-semibold">SBFP Program ID</div>
            </div>
            
            <div class="my-auto flex flex-col items-center">
                <div class="bg-white p-1 border shadow-sm rounded mb-1">
                    {!! QrCode::size(75)->generate($student->student_number) !!}
                </div>
                <div class="text-xs font-bold text-slate-900 truncate w-full text-center">{{ $student->last_name }}, {{ $student->first_name }}</div>
                <div class="text-[9px] text-slate-600">{{ $student->student_number }}</div>
                <div class="text-[9px] text-slate-600">{{ $student->grade_level }} - {{ $student->section }}</div>
            </div>

            <div class="text-center text-[7px] text-slate-400 border-t pt-0.5">
                Scan for Attendance
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
</body>
</html>

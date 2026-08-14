<!DOCTYPE html>
<html>
<head>
    <title>Student ID - {{ $student->first_name }} {{ $student->last_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; background: white; }
        }
        .id-portrait {
            width: 2.125in;
            height: 3.375in;
            border: 1px solid #cbd5e1;
            margin: 20px auto;
            padding: 0.15in;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: white;
            box-sizing: border-box;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
    </style>
</head>
<body class="bg-gray-100 py-10 text-center">
    <div class="id-portrait">
        <div>
            <div class="text-[11px] font-bold uppercase text-slate-800 leading-tight">Marisol Bliss Elementary School</div>
            <div class="text-[9px] text-emerald-600 font-semibold mt-0.5">School-Based Feeding Program ID</div>
        </div>
        
        <div class="my-auto flex flex-col items-center">
            <div class="bg-white p-2 border shadow-sm rounded mb-2">
                {!! QrCode::size(110)->generate($student->student_number) !!}
            </div>
            <div class="text-sm font-bold text-slate-900 leading-tight mt-1">{{ $student->last_name }}, {{ $student->first_name }}</div>
            <div class="text-[10px] text-slate-600">ID: {{ $student->student_number }}</div>
            <div class="text-[10px] text-slate-600">Grade & Sec: {{ $student->grade_level }} - {{ $student->section }}</div>
        </div>

        <div class="text-center text-[8px] text-slate-400 border-t pt-1">
            Official Participant - Scan for Daily Attendance
        </div>
    </div>

    <button class="no-print bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700 text-sm font-semibold" onclick="window.print()">Print Portrait ID</button>
</body>
</html>


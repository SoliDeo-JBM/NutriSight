@extends('layouts.dashboard')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold">Encoder Dashboard</h1>
        @if(Auth::user()->advisory_grade_level && Auth::user()->advisory_section)
            <div class="text-sm text-blue-800 bg-blue-50 border border-blue-200 rounded-lg px-4 py-2.5 inline-flex items-center gap-2 shadow-sm">
                <i class="fas fa-chalkboard-teacher text-blue-600"></i>
                <span>Advisory: <strong class="font-bold">Grade {{ Auth::user()->advisory_grade_level }} - Section {{ Auth::user()->advisory_section }}</strong></span>
            </div>
        @else
            <div class="text-sm text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2.5 inline-flex items-center gap-2 shadow-sm">
                <i class="fas fa-info-circle text-amber-600"></i>
                <span>Advisory: <strong class="italic">Not yet assigned</strong></span>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="text-gray-500 text-sm font-semibold uppercase">Total Advisory Learners</div>
            <div class="text-3xl font-bold text-slate-800 mt-2">{{ $totalStudents ?? 0 }}</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="text-gray-500 text-sm font-semibold uppercase">Total SBFP Learners</div>
            <div class="text-3xl font-bold text-emerald-600 mt-2">{{ $totalSbfp ?? 0 }}</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="text-gray-500 text-sm font-semibold uppercase">Today's Attendance</div>
            <div class="text-3xl font-bold text-blue-600 mt-2">{{ $attendanceCounts[6] ?? 0 }}</div>
        </div>
    </div>

    <!-- Analytics Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-8">
        <h2 class="text-lg font-bold mb-4">SBFP Attendance Frequency (Last 7 Days)</h2>
        <div style="height: 300px;">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
        <a href="{{ route('encoder.reports.sbfp.attendance') }}" class="group bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:border-blue-500 hover:shadow-md transition-all duration-200">
            <div>
                <div class="flex items-center justify-between mb-4"><div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors"><i class="fas fa-calendar-days text-xl"></i></div><span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700">Feeding Cycle</span></div>
                <h2 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">1. Attendance Report</h2>
                <p class="text-sm text-gray-500 mt-2 leading-relaxed">Review daily feeding attendance for your assigned advisory class.</p>
            </div>
            <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-between text-sm font-semibold text-blue-600 group-hover:text-blue-700"><span>View Attendance</span><i class="fas fa-arrow-right"></i></div>
        </a>
        <a href="{{ route('encoder.reports.sbfp.assessment') }}" class="group bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:border-violet-500 hover:shadow-md transition-all duration-200">
            <div>
                <div class="flex items-center justify-between mb-4"><div class="w-12 h-12 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center group-hover:bg-violet-600 group-hover:text-white transition-colors"><i class="fas fa-chart-column text-xl"></i></div><span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-violet-50 text-violet-700">Program Impact</span></div>
                <h2 class="text-lg font-bold text-gray-900 group-hover:text-violet-600 transition-colors">2. SBFP Assessment</h2>
                <p class="text-sm text-gray-500 mt-2 leading-relaxed">Review attendance and nutrition progress for your assigned advisory class.</p>
            </div>
            <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-between text-sm font-semibold text-violet-600 group-hover:text-violet-700"><span>View Assessment</span><i class="fas fa-arrow-right"></i></div>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('encoder.students.index') }}" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition">
            <h2 class="text-lg font-semibold mb-2 text-blue-600">Advisory Learner List</h2>
            <p class="text-gray-600">Manage learner profiles and nutritional data.</p>
        </a>
        
        <a href="{{ route('encoder.students.sbfp') }}" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition">
            <h2 class="text-lg font-semibold mb-2 text-emerald-600">Advisory SBFP List</h2>
            <p class="text-gray-600">View official feeding program participants & approvals.</p>
        </a>

        <a href="{{ route('encoder.attendance.index') }}" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition">
            <h2 class="text-lg font-semibold mb-2 text-purple-600">Attendance List</h2>
            <p class="text-gray-600">Interactive calendar and QR scanner logs.</p>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($attendanceDates),
                datasets: [{
                    label: 'Present Learners',
                    data: @json($attendanceCounts),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>
@endsection

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
            <div class="text-gray-500 text-sm font-semibold uppercase">Total Advisory Students</div>
            <div class="text-3xl font-bold text-slate-800 mt-2">{{ $totalStudents ?? 0 }}</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="text-gray-500 text-sm font-semibold uppercase">Total SBFP Students</div>
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('encoder.students.index') }}" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition">
            <h2 class="text-lg font-semibold mb-2 text-blue-600">Advisory Student List</h2>
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
                    label: 'Present Students',
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

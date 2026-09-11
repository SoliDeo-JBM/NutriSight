@extends('layouts.dashboard')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <h1 class="text-2xl font-bold">Super Admin Dashboard - System Nutrition Overview</h1>
    <form method="GET" action="{{ route('super-admin.dashboard') }}" class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
        <label for="super-dashboard-period" class="text-xs font-semibold uppercase tracking-wide text-slate-500">View period</label>
        <div class="relative">
            <select id="super-dashboard-period" name="period" onchange="this.form.submit()" class="appearance-none rounded-lg border border-indigo-200 bg-indigo-50 py-2 pl-3 pr-9 text-sm font-semibold text-indigo-800 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                <option value="Baseline" {{ ($selectedPeriod ?? 'Baseline') === 'Baseline' ? 'selected' : '' }}>Baseline</option>
                <option value="Midline" {{ ($selectedPeriod ?? '') === 'Midline' ? 'selected' : '' }}>Midline</option>
                <option value="Endline" {{ ($selectedPeriod ?? '') === 'Endline' ? 'selected' : '' }}>Endline</option>
            </select>
            <i class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-indigo-500 fas fa-chevron-down"></i>
        </div>
    </form>
</div>

<!-- KPI Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
        <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Total SBFP Students</div>
        <div class="text-3xl font-bold text-gray-900 mt-2">{{ $totalSbfpStudents }}</div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
        <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">SBFP Recovery Rate</div>
        <div class="text-3xl font-bold text-emerald-600 mt-2">{{ $recoveryRate }}%</div>
        <div class="text-xs text-gray-500 mt-1">{{ $recoveredCount }} of {{ $totalSbfpStudents }} at Normal status</div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
        <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Normal Status</div>
        <div class="text-3xl font-bold text-green-600 mt-2">{{ $bmiDistribution['Normal'] ?? 0 }}</div>
    </div>
    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
        <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Wasted / Severely Wasted</div>
        <div class="text-3xl font-bold text-rose-600 mt-2">{{ ($bmiDistribution['Wasted'] ?? 0) + ($bmiDistribution['Severely Wasted'] ?? 0) }}</div>
    </div>
</div>

<!-- Charts Section (Responsive Grid) -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- BMI Trend Line Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h2 class="text-lg font-bold mb-4 text-gray-800">Average BMI Trend per Period (District/School)</h2>
        <div class="relative" style="height: 280px;">
            <canvas id="bmiTrendChart"></canvas>
        </div>
    </div>

    <!-- BMI Distribution Donut Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">Overall BMI Status Distribution</h2>
        </div>
        <div class="relative flex items-center justify-center" style="height: 280px;">
            <canvas id="bmiDistributionChart"></canvas>
        </div>
    </div>
</div>

<!-- Grade Level Attendance Rate Chart -->
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-8">
    <h2 class="text-lg font-bold mb-4 text-gray-800">Attendance Rate by Grade Level (%)</h2>
    <div class="relative" style="height: 280px;">
        <canvas id="sectionAttendanceChart"></canvas>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-8">
    <h2 class="text-lg font-semibold mb-4 text-gray-800">System Administration & Quick Actions</h2>
    <p class="text-gray-600 mb-4">Manage user roles, permissions, system settings, and monitor overarching nutritional progress across all schools and sections.</p>
    <div class="flex flex-wrap gap-4">
        <a href="{{ route('super-admin.accounts.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-semibold hover:bg-blue-700 transition">Manage Admin Accounts</a>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700 transition">View Admin Period Progress Report</a>
        <a href="{{ route('super-admin.students.index') }}" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-semibold hover:bg-gray-700 transition">Manage Students</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // BMI Trend Line Chart
    const trendCtx = document.getElementById('bmiTrendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($periodBmiChartLabels),
            datasets: [{
                label: 'Average BMI',
                data: @json($periodBmiChartData),
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 5,
                pointBackgroundColor: 'rgb(16, 185, 129)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // BMI Distribution Donut Chart
    const distCtx = document.getElementById('bmiDistributionChart').getContext('2d');
    const bmiData = @json($bmiDistribution);
    new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(bmiData),
            datasets: [{
                data: Object.values(bmiData),
                backgroundColor: [
                    'rgb(16, 185, 129)', // Normal - Emerald
                    'rgb(245, 158, 11)', // Wasted - Amber
                    'rgb(239, 68, 68)', // Severely Wasted - Red
                    'rgb(99, 102, 241)', // Overweight - Indigo
                    'rgb(168, 85, 247)' // Obese - Purple
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // Section Attendance Rate Bar Chart
    const attendanceCtx = document.getElementById('sectionAttendanceChart').getContext('2d');
    new Chart(attendanceCtx, {
        type: 'bar',
        data: {
            labels: @json($sectionAttendanceLabels),
            datasets: [{
                label: 'Attendance Rate (%)',
                data: @json($sectionAttendanceRates),
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                borderRadius: 4
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
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
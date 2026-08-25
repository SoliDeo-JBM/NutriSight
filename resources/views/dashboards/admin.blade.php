@extends('layouts.dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Admin Dashboard - Nutritional Analytics & Term Progress</h1>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
            <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Total SBFP Students</div>
            <div class="text-3xl font-bold text-gray-900 mt-2">{{ $sbfpStudents->count() }}</div>
        </div>
        <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
            <div class="text-gray-500 text-xs font-semibold uppercase tracking-wider">SBFP Recovery Rate</div>
            <div class="text-3xl font-bold text-emerald-600 mt-2">{{ $recoveryRate }}%</div>
            <div class="text-xs text-gray-500 mt-1">{{ $recoveredCount }} of {{ $malnourishedTerm1Count }} recovered to Normal</div>
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
            <h2 class="text-lg font-bold mb-4 text-gray-800">Average BMI Trend per Term</h2>
            <div class="relative" style="height: 280px;">
                <canvas id="bmiTrendChart"></canvas>
            </div>
        </div>

        <!-- BMI Distribution Donut Chart -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-800">BMI Status Distribution</h2>
                <form method="GET" action="{{ route('admin.dashboard') }}" class="mt-2 sm:mt-0">
                    <select name="term" onchange="this.form.submit()" class="text-xs border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 py-1 px-2">
                        <option value="all" {{ ($selectedTerm ?? 'all') == 'all' ? 'selected' : '' }}>All Terms (Latest)</option>
                        <option value="Term 1" {{ ($selectedTerm ?? '') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                        <option value="Term 2" {{ ($selectedTerm ?? '') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                        <option value="Term 3" {{ ($selectedTerm ?? '') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                    </select>
                </form>
            </div>
            <div class="relative flex items-center justify-center" style="height: 280px;">
                <canvas id="bmiDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Section Attendance Rate Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-8">
        <h2 class="text-lg font-bold mb-4 text-gray-800">Attendance Rate by Section (%)</h2>
        <div class="relative" style="height: 280px;">
            <canvas id="sectionAttendanceChart"></canvas>
        </div>
    </div>

    <!-- Term Progress Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-800">Student Term Progress Report</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">No.</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Student ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Name</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Term 1</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Term 2</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Term 3</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sbfpStudents as $index => $student)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $student->student_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $student->first_name }} {{ $student->last_name }}</td>
                        
                        @foreach(['Term 1', 'Term 2', 'Term 3'] as $term)
                        <td class="px-6 py-4 text-center text-sm">
                            @if(!empty($student->termProgress[$term]))
                                @php
                                    $latestAssessment = $student->termProgress[$term][0];
                                @endphp
                                <div class="text-xs bg-blue-50 p-2 rounded inline-block">
                                    <div><strong>H:</strong> {{ $latestAssessment->height_m * 100 }}cm</div>
                                    <div><strong>W:</strong> {{ $latestAssessment->weight_kg }}kg</div>
                                    <div><strong>BMI:</strong> {{ $latestAssessment->bmi }}</div>
                                    <div class="text-xs font-semibold
                                        @if($latestAssessment->nutritional_status == 'Normal') text-green-700
                                        @elseif(in_array($latestAssessment->nutritional_status, ['Wasted', 'Severely Wasted'])) text-red-700
                                        @else text-yellow-700 @endif">
                                        {{ $latestAssessment->nutritional_status }}
                                    </div>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // BMI Trend Line Chart
        const trendCtx = document.getElementById('bmiTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: @json($termBmiChartLabels),
                datasets: [{
                    label: 'Average BMI',
                    data: @json($termBmiChartData),
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
                        ticks: { stepSize: 1 }
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
                        'rgb(239, 68, 68)',  // Severely Wasted - Red
                        'rgb(99, 102, 241)', // Overweight - Indigo
                        'rgb(168, 85, 247)'  // Obese - Purple
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
                                font: { size: 11 }
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
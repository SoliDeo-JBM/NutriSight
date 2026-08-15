@extends('layouts.dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Admin Dashboard - Term Progress Report</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="text-gray-500 text-sm font-semibold uppercase">Total SBFP Students</div>
            <div class="text-3xl font-bold text-emerald-600 mt-2">{{ $sbfpStudents->count() }}</div>
        </div>
    </div>

    <!-- Term Progress Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
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
@endsection
@extends('layouts.dashboard')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Advisory Student Lists</h1>
        <a href="{{ route('students.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Add Advisory Student
        </a>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
        <p class="text-gray-600 mb-4">Master list of advisory students with WHO nutritional metrics. Scroll horizontally to view all details.</p>
        
        <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 border">No.</th>
                        <th class="px-4 py-3 border">LRN / ID</th>
                        <th class="px-4 py-3 border">Learner's Name (Last, First, Ext, Middle)</th>
                        <th class="px-4 py-3 border">Birthdate</th>
                        <th class="px-4 py-3 border">Sex</th>
                        <th class="px-4 py-3 border">Weight (kg)</th>
                        <th class="px-4 py-3 border">Height (cm)</th>
                        <th class="px-4 py-3 border">BMI</th>
                        <th class="px-4 py-3 border">BMI Category</th>
                        <th class="px-4 py-3 border">Height for Age</th>
                        <th class="px-4 py-3 border">Remarks</th>
                        <th class="px-4 py-3 border">Grade & Section</th>
                        <th class="px-4 py-3 border">Guardian's Email</th>
                        <th class="px-4 py-3 border">Guardian's Phone Number</th>
                        <th class="px-4 py-3 border">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($students ?? [] as $index => $student)
                    @php $latestRecord = $student->nutritionalRecords()->latest()->first(); @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 border font-semibold text-slate-800">{{ $student->student_number }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->name_extension }} {{ $student->middle_name }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->birth_date }}</td>
                        <td class="px-4 py-3 border">{{ $student->gender }}</td>
                        <td class="px-4 py-3 border">{{ $latestRecord->weight ?? '-' }}</td>
                        <td class="px-4 py-3 border">{{ $latestRecord->height ?? '-' }}</td>
                        <td class="px-4 py-3 border">{{ $latestRecord->bmi ?? '-' }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">
                            @if($latestRecord)
                                <span class="px-2 py-0.5 rounded text-xs font-bold 
                                    @if($latestRecord->bmi_category == 'Normal') bg-green-100 text-green-800 
                                    @elseif(in_array($latestRecord->bmi_category, ['Wasted', 'Severely Wasted'])) bg-red-100 text-red-800 
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $latestRecord->bmi_category }}
                                </span>
                            @else
                                <span class="text-gray-400 italic">N/A</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 border">{{ $latestRecord->height_for_age ?? 'Normal' }}</td>
                        <td class="px-4 py-3 border">{{ $latestRecord->remarks ?? '-' }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->grade_level }} - {{ $student->section }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->guardian_email ?? '-' }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->guardian_contact ?? '-' }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to archive this student?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-xs font-medium">Archive</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="15" class="px-4 py-6 border text-center text-gray-500">No students found. Click "Add Advisory Student" to begin.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

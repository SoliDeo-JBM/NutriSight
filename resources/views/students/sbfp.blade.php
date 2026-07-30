@extends('layouts.dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Advisory SBFP Lists</h1>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <p class="text-gray-600">Students automatically included due to Wasted / Severely Wasted BMI or explicit parent approval. Scroll horizontally for details.</p>
            <a href="{{ route('students.print-batch') }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 whitespace-nowrap">
                <i class="fas fa-print mr-2"></i> Print Portrait ID QR Sheet
            </a>
        </div>
        
        <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
            <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 border">No.</th>
                        <th class="px-4 py-3 border">LRN / ID</th>
                        <th class="px-4 py-3 border">Learner's Name</th>
                        <th class="px-4 py-3 border">Birthdate / Age / Sex</th>
                        <th class="px-4 py-3 border">Weight (kg)</th>
                        <th class="px-4 py-3 border">Height (cm)</th>
                        <th class="px-4 py-3 border">BMI</th>
                        <th class="px-4 py-3 border">BMI Category</th>
                        <th class="px-4 py-3 border">Parent's Approval</th>
                        <th class="px-4 py-3 border text-center">Student QR Code</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($students ?? [] as $index => $student)
                    @php 
                        $latestRecord = $student->nutritionalRecords()->latest()->first();
                        $isWasted = $latestRecord && in_array($latestRecord->bmi_category, ['Wasted', 'Severely Wasted']);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 border font-semibold text-slate-800">{{ $student->student_number }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->name_extension }} {{ $student->middle_name }}</td>
                        <td class="px-4 py-3 border whitespace-nowrap">{{ $student->birth_date }} ({{ $student->gender }})</td>
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
                        <td class="px-4 py-3 border min-w-[220px]">
                            <form action="{{ route('students.approval', $student->id) }}" method="POST" x-data="{ status: '{{ $student->parent_approval_status ?? ($isWasted ? 'approved' : '') }}', reason: '{{ $student->disapproval_reason }}' }">
                                @csrf
                                @method('PATCH')
                                <div class="space-y-2 text-xs">
                                    <label class="block">
                                        <input type="radio" name="parent_approval_status" value="approved" x-model="status" @change="reason = ''"> Approved
                                    </label>
                                    <label class="block">
                                        <input type="radio" name="parent_approval_status" value="disapproved" x-model="status"> Disapproved
                                    </label>

                                    <div x-show="status === 'disapproved'" class="mt-2 pl-3 border-l-2 border-red-300 space-y-1">
                                        <label class="block">
                                            <input type="radio" name="disapproval_reason" value="unwilling" x-model="reason"> Unwilling to include child
                                        </label>
                                        <label class="block">
                                            <input type="radio" name="disapproval_reason" value="medical_condition" x-model="reason"> Underlying medical condition
                                        </label>

                                        <div x-show="reason === 'medical_condition'" class="mt-1">
                                            <input type="text" name="medical_condition_notes" value="{{ $student->medical_condition_notes }}" placeholder="Specify condition..." class="w-full text-xs border rounded p-1">
                                        </div>
                                    </div>

                                    <button type="submit" class="mt-2 bg-slate-800 text-white px-3 py-1 rounded text-xs hover:bg-slate-700">Update</button>
                                </div>
                            </form>
                        </td>
                        <td class="px-4 py-3 border text-center whitespace-nowrap">
                            @if($student->is_permitted || $isWasted)
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-1 bg-white border inline-block shadow-sm rounded">
                                        {!! QrCode::size(60)->generate($student->student_number) !!}
                                    </div>
                                    <a href="{{ route('students.id-card', $student->id) }}" target="_blank" class="text-[11px] text-blue-600 hover:underline mt-1">Print Portrait ID</a>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs italic">Requires Approval</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-6 border text-center text-gray-500">No SBFP records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alpine.js for interactive radio toggling -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection

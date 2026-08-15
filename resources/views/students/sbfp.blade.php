@extends('layouts.dashboard')

@section('content')
    <div x-data="sbfpManager()" x-cloak>
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
                            <th class="px-4 py-3 border text-center" colspan="3">Term Progress</th>
                            <th class="px-4 py-3 border">Parent's Approval</th>
                            <th class="px-4 py-3 border text-center">Student QR Code</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="px-4 py-2 border"></th>
                            <th class="px-4 py-2 border text-center text-xs bg-blue-50">Term 1</th>
                            <th class="px-4 py-2 border text-center text-xs bg-blue-50">Term 2</th>
                            <th class="px-4 py-2 border text-center text-xs bg-blue-50">Term 3</th>
                            <th colspan="2" class="px-4 py-2 border"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($students ?? [] as $index => $student)
                        @php 
                            $latestRecord = $student->nutritionalRecords()->latest()->first();
                            $isWasted = $latestRecord && in_array($latestRecord->bmi_category, ['Wasted', 'Severely Wasted']);
                            $termData = [
                                'Term 1' => $student->assessments()->whereMonth('assessment_date', 1)->latest()->first(),
                                'Term 2' => $student->assessments()->whereMonth('assessment_date', 2)->latest()->first(),
                                'Term 3' => $student->assessments()->whereMonth('assessment_date', 3)->latest()->first(),
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 border">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 border font-semibold text-slate-800">{{ $student->student_number }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->name_extension }} {{ $student->middle_name }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->birth_date }} ({{ $student->gender }})</td>
                            
                            <!-- Term Data Columns -->
                            @foreach(['Term 1', 'Term 2', 'Term 3'] as $index => $term)
                            <td class="px-4 py-3 border text-center">
                                @if($termData[$term])
                                    @php $data = $termData[$term]; @endphp
                                    <div class="text-xs space-y-1 bg-gray-50 p-2 rounded">
                                        <div><strong>W:</strong> {{ $data->weight_kg }}kg</div>
                                        <div><strong>H:</strong> {{ $data->height_m * 100 }}cm</div>
                                        <div><strong>BMI:</strong> {{ $data->bmi }}</div>
                                        <div class="text-xs font-semibold
                                            @if($data->nutritional_status == 'Normal') text-green-700
                                            @elseif(in_array($data->nutritional_status, ['Wasted', 'Severely Wasted'])) text-red-700
                                            @else text-yellow-700 @endif">
                                            {{ $data->nutritional_status }}
                                        </div>
                                        <button @click="openProgressModal({{ $student->id }}, {{ $index + 1 }})" class="mt-1 text-blue-600 hover:underline text-xs">Edit</button>
                                    </div>
                                @else
                                    <button @click="openProgressModal({{ $student->id }}, {{ $index + 1 }})" class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">
                                        Add
                                    </button>
                                @endif
                            </td>
                            @endforeach

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

        <!-- Progress Input Modal -->
        <div x-show="showModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md mx-4">
                <h3 class="text-lg font-bold mb-4">Add Term Progress</h3>
                
                <form :action="'/students/' + currentStudentId + '/assessment'" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Term</label>
                        <select name="term" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Select Term</option>
                            <option value="1" :selected="currentTerm == 1">Term 1 (Jun - Sep)</option>
                            <option value="2" :selected="currentTerm == 2">Term 2 (Sep - Dec)</option>
                            <option value="3" :selected="currentTerm == 3">Term 3 (Jan - Apr)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Height (centimeters)</label>
                        <input type="number" name="height_cm" step="0.1" placeholder="150" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Weight (kg)</label>
                        <input type="number" name="weight_kg" step="0.1" placeholder="45.5" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 font-semibold">Save Progress</button>
                        <button type="button" @click="closeModal()" class="flex-1 bg-gray-400 text-white px-4 py-2 rounded text-sm hover:bg-gray-500 font-semibold">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function sbfpManager() {
            return {
                showModal: false,
                currentStudentId: null,
                currentTerm: null,
                openProgressModal(studentId, term) {
                    this.currentStudentId = studentId;
                    this.currentTerm = parseInt(term);
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                    this.currentStudentId = null;
                    this.currentTerm = null;
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
@endsection
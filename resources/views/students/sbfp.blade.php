@extends('layouts.dashboard')

@section('content')
    <div x-data="sbfpManager()" x-cloak class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Advisory SBFP List</h1>
                <p class="text-sm text-gray-500 mt-1">Students automatically included due to Wasted / Severely Wasted BMI or explicit parent approval.</p>
            </div>
            <a href="{{ route('encoder.students.print-batch') }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 whitespace-nowrap inline-flex items-center gap-2">
                <i class="fas fa-print"></i> Print Portrait ID QR Sheet
            </a>
        </div>

        <!-- Filters & Search Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('encoder.students.sbfp') }}" class="space-y-4">
                <!-- Search Bar -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search by Name or LRN / ID</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter student name or LRN..." class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Sex Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sex</label>
                        <select name="sex" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All</option>
                            @foreach($sexes as $sex)
                                <option value="{{ $sex }}" {{ request('sex') == $sex ? 'selected' : '' }}>{{ $sex }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- BMI Category Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">BMI Category</label>
                        <select name="bmi_category" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            @foreach($bmiCategories as $cat)
                                <option value="{{ $cat }}" {{ request('bmi_category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Approval Status Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Parent Approval</label>
                        <select name="approval_status" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            @foreach($approvalStatuses as $key => $label)
                                <option value="{{ $key }}" {{ request('approval_status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sort By</label>
                        <select name="sort" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @foreach($sortOptions as $key => $label)
                                <option value="{{ $key }}" {{ request('sort', 'latest') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded text-sm font-semibold hover:bg-blue-700">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('encoder.students.sbfp') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded text-sm font-semibold hover:bg-gray-300">
                        <i class="fas fa-redo mr-2"></i> Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- SBFP Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
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
                            <td class="px-4 py-3 border">{{ $students->firstItem() + $index }}</td>
                            <td class="px-4 py-3 border font-semibold text-slate-800">{{ $student->student_number }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->name_extension }} {{ $student->middle_name }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->birth_date }} ({{ $student->gender }})</td>
                            
                            <!-- Term Data Columns -->
                            @foreach(['Term 1', 'Term 2', 'Term 3'] as $termIndex => $term)
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
                                        <button @click="openProgressModal({{ $student->id }}, {{ $termIndex + 1 }})" class="mt-1 text-blue-600 hover:underline text-xs">Edit</button>
                                    </div>
                                @else
                                    <button @click="openProgressModal({{ $student->id }}, {{ $termIndex + 1 }})" class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">
                                        Add
                                    </button>
                                @endif
                            </td>
                            @endforeach

                            <td class="px-4 py-3 border min-w-[220px]">
                                <form action="{{ route('encoder.students.approval', $student->id) }}" method="POST" x-data="{ status: '{{ $student->parent_approval_status ?? ($isWasted ? 'approved' : '') }}', reason: '{{ $student->disapproval_reason }}' }">
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
                                @if($student->parent_approval_status === 'disapproved')
                                    <span class="text-red-500 text-xs font-semibold">Disapproved (No QR)</span>
                                @elseif($student->is_permitted || $isWasted)
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-1 bg-white border inline-block shadow-sm rounded">
                                            {!! QrCode::size(60)->generate($student->student_number) !!}
                                        </div>
                                        <a href="{{ route('encoder.students.id-card', $student->id) }}" target="_blank" class="text-[11px] text-blue-600 hover:underline mt-1">Print Portrait ID</a>
                                        @if($student->guardian_email)
                                            <button @click="openEmailModal({{ $student->id }}, '{{ $student->first_name }} {{ $student->last_name }}', '{{ $student->guardian_email }}')" class="mt-2 bg-blue-600 text-white px-2.5 py-1 rounded text-xs hover:bg-blue-700 font-semibold inline-flex items-center gap-1">
                                                <i class="fas fa-envelope"></i> Email Parent
                                            </button>
                                        @else
                                            <span class="text-[10px] text-gray-400 block mt-1">No Guardian Email</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">Requires Approval</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 border text-center text-gray-500">No SBFP records found matching your criteria.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($students->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $students->render() }}
            </div>
            @endif
        </div>

        <!-- Progress Input Modal -->
        <div x-show="showModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md mx-4">
                <h3 class="text-lg font-bold mb-4">Add Term Progress</h3>
                
                <form :action="'/encoder/students/' + currentStudentId + '/assessment'" method="POST">
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

        <!-- Email Notice Modal -->
        <div x-show="showEmailModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md mx-4">
                <h3 class="text-lg font-bold mb-2">Send Feeding Day Notice</h3>
                <p class="text-xs text-gray-500 mb-4">Recipient: <span class="font-semibold text-gray-800" x-text="currentGuardianEmail"></span></p>
                
                <form :action="'/encoder/students/' + currentStudentId + '/email-feeding'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Meal to be Served <span class="text-red-500">*</span></label>
                        <input type="text" name="meal" required placeholder="e.g. Rice porridge with egg" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Feeding Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Teacher's Notes (Optional)</label>
                        <textarea name="notes" rows="3" placeholder="Additional instructions or notes for the parent..." class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 font-semibold">Send Email</button>
                        <button type="button" @click="closeEmailModal()" class="flex-1 bg-gray-400 text-white px-4 py-2 rounded text-sm hover:bg-gray-500 font-semibold">Cancel</button>
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
                showEmailModal: false,
                currentStudentId: null,
                currentTerm: null,
                currentGuardianEmail: '',
                openProgressModal(studentId, term) {
                    this.currentStudentId = studentId;
                    this.currentTerm = parseInt(term);
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                    this.currentStudentId = null;
                    this.currentTerm = null;
                },
                openEmailModal(studentId, studentName, guardianEmail) {
                    this.currentStudentId = studentId;
                    this.currentGuardianEmail = guardianEmail;
                    this.showEmailModal = true;
                },
                closeEmailModal() {
                    this.showEmailModal = false;
                    this.currentStudentId = null;
                    this.currentGuardianEmail = '';
                }
            }
        }

        let searchTimeout;
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.form.submit();
                }, 300);
            });
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
@endsection

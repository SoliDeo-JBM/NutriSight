@extends('layouts.dashboard')

@section('content')
    <div x-data="sbfpManager()" x-cloak class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Advisory SBFP List</h1>
                <p class="text-sm text-gray-500 mt-1">Students automatically included due to Wasted / Severely Wasted BMI or explicit parent approval.</p>
                <div class="mt-4 flex flex-nowrap items-center gap-3 overflow-x-auto pb-1">
                    <button type="button" @click="openEditPeriodModal()" class="shrink-0 bg-amber-600 text-white px-4 py-2 rounded text-sm hover:bg-amber-700 whitespace-nowrap inline-flex items-center gap-2">
                        <i class="fas fa-pen"></i> Edit Period
                    </button>
                    <button type="button" @click="openPeriodModal()" class="shrink-0 bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 whitespace-nowrap inline-flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Period
                    </button>
                    <a href="{{ route('encoder.students.print-batch') }}" target="_blank" class="shrink-0 bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 whitespace-nowrap inline-flex items-center gap-2">
                        <i class="fas fa-print"></i> Print Portrait ID QR Sheet
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters & Search Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="GET" action="{{ route('encoder.students.sbfp') }}" class="space-y-4" x-data>
                <!-- Search Bar -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Search by Name or LRN / ID</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter student name or LRN..." @input.debounce.350ms="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Sex Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sex</label>
                        <select name="sex" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All</option>
                            @foreach($sexes as $sex)
                                <option value="{{ $sex }}" {{ request('sex') == $sex ? 'selected' : '' }}>{{ $sex }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Approval Status Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Parent Approval</label>
                        <select name="approval_status" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            @foreach($approvalStatuses as $key => $label)
                                <option value="{{ $key }}" {{ request('approval_status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sort By</label>
                        <select name="sort" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @foreach($sortOptions as $key => $label)
                                <option value="{{ $key }}" {{ request('sort', 'latest') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end pt-1">
                    <a href="{{ route('encoder.students.sbfp') }}" class="text-sm text-gray-600 hover:text-blue-600 inline-flex items-center gap-2">
                        <i class="fas fa-rotate-left"></i> Reset filters
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
                            <th class="px-4 py-3 border">LRN</th>
                            <th class="px-4 py-3 border">Learner's Name</th>
                            <th class="px-4 py-3 border">Birthdate</th>
                            <th class="px-4 py-3 border">Age</th>
                            <th class="px-4 py-3 border">Sex</th>
                            <th class="px-4 py-3 border text-center" colspan="3">Period Progress</th>
                            <th class="px-4 py-3 border">Parent's Approval</th>
                            <th class="px-4 py-3 border text-center">Student QR Code</th>
                        </tr>
                        <tr>
                            <th colspan="6" class="px-4 py-2 border"></th>
                            <th class="px-4 py-2 border text-center text-xs bg-blue-50">Baseline</th>
                            <th class="px-4 py-2 border text-center text-xs bg-blue-50">Midline</th>
                            <th class="px-4 py-2 border text-center text-xs bg-blue-50">Endline</th>
                            <th colspan="2" class="px-4 py-2 border"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($students ?? [] as $index => $student)
                        @php
                            $enrollment = $student->enrollments->first();
                            $latestRecord = $student->nutritionalRecords()->latest()->first();
                            $isWasted = $latestRecord && in_array($latestRecord->bmi_category, ['Wasted', 'Severely Wasted']);
                            $periodData = [
                                'Baseline' => $student->periodProgress['Baseline'][0] ?? null,
                                'Midline' => $student->periodProgress['Midline'][0] ?? null,
                                'Endline' => $student->periodProgress['Endline'][0] ?? null,
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 border">{{ $students->firstItem() + $index }}</td>
                            <td class="px-4 py-3 border font-semibold text-slate-800">{{ $student->student_number }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->name_extension }} {{ $student->middle_name }}</td>
                            <td class="px-4 py-3 border whitespace-nowrap">{{ $student->birth_date?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-4 py-3 border">{{ $student->birth_date?->age ?? '-' }}</td>
                            <td class="px-4 py-3 border">{{ $student->sex ?? '-' }}</td>
                            <!-- Period Data Columns -->
                            @foreach(['Baseline', 'Midline', 'Endline'] as $period)
                            <td class="px-4 py-3 border text-center">
                                @if($periodData[$period])
                                    @php $data = $periodData[$period]; @endphp
                                    <div class="text-xs space-y-1 bg-gray-50 p-2 rounded">
                                        <div><strong>W:</strong> {{ $data->weight }}kg</div>
                                        <div><strong>H:</strong> {{ $data->height }}cm</div>
                                        <div><strong>BMI:</strong> {{ $data->bmi }}</div>
                                        <div class="text-xs font-semibold
                                            @if($data->bmi_category == 'Normal') text-green-700
                                            @elseif(in_array($data->bmi_category, ['Wasted', 'Severely Wasted'])) text-red-700
                                            @else text-yellow-700 @endif">
                                            {{ $data->bmi_category }}
                                        </div>
                                    </div>
                                @endif
                            </td>
                            @endforeach

                            <td class="px-4 py-3 border min-w-[220px]">
                                <form action="{{ route('encoder.students.approval', $student->id) }}" method="POST" x-data="{ status: @js($student->parent_approval_status ?? ($isWasted ? 'approved' : '')), reason: @js($student->disapproval_reason) }">
                                    @csrf
                                    @method('PATCH')
                                    <div class="space-y-2 text-xs">
                                        <label class="block">
                                            <input type="radio" name="parent_consent" value="approved" x-model="status" @change="reason = ''"> Approved
                                        </label>
                                        <label class="block">
                                            <input type="radio" name="parent_consent" value="disapproved" x-model="status"> Disapproved
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
                                             {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(60)->generate($student->student_number) !!}
                                        </div>
                                        <a href="{{ route('encoder.students.id-card', $student->id) }}" target="_blank" class="text-[11px] text-blue-600 hover:underline mt-1">Print Portrait ID</a>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">Requires Approval</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="px-4 py-8 border text-center text-gray-500">No SBFP records found matching your criteria.</td>
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

        <!-- Add Period Modal -->
        <div x-show="showModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-5xl mx-4 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold mb-1">Add Period</h3>
                <p class="text-sm text-gray-500 mb-4">Existing measurements are locked. Enter complete measurements only for students missing the selected period.</p>
                
                <form action="{{ route('encoder.students.assessment.bulk') }}" method="POST" @submit.prevent="requestConfirmation($event, 'add')">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Period</label>
                        <select name="measurement_period" x-model="addPeriod" @change="refreshAddValues()" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">Select Period</option>
                            <option value="baseline">Baseline</option>
                            <option value="midline">Midline</option>
                            <option value="endline">Endline</option>
                        </select>
                    </div>

                    <div class="overflow-x-auto mb-6">
                        <table class="w-full text-sm border-collapse">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr><th class="px-3 py-2 border text-left">Learner</th><th class="px-3 py-2 border">Weight (kg)</th><th class="px-3 py-2 border">Height (cm)</th></tr>
                            </thead>
                            <tbody>
                                @foreach($students ?? [] as $student)
                                    @php
                                        $measurements = collect(['baseline', 'midline', 'endline'])->mapWithKeys(function ($period) use ($student) {
                                            $record = $student->periodProgress[ucfirst($period)][0] ?? null;
                                            return [$period => $record ? ['weight' => $record->weight, 'height' => $record->height] : null];
                                        });
                                    @endphp
                                    <tr data-add-row data-values='@json($measurements)'>
                                        <td class="px-3 py-2 border whitespace-nowrap">{{ $student->last_name }}, {{ $student->first_name }}</td>
                                        <td class="px-3 py-2 border"><input type="hidden" name="measurements[{{ $student->id }}][student_id]" value="{{ $student->id }}"><input type="number" name="measurements[{{ $student->id }}][weight]" data-add-weight step="0.1" min="0.1" class="w-full border border-gray-300 rounded px-2 py-1"></td>
                                        <td class="px-3 py-2 border"><input type="number" name="measurements[{{ $student->id }}][height]" data-add-height step="0.1" min="0.1" class="w-full border border-gray-300 rounded px-2 py-1"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 font-semibold">Add</button>
                        <button type="button" @click="closeModal()" class="flex-1 bg-gray-400 text-white px-4 py-2 rounded text-sm hover:bg-gray-500 font-semibold">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Period Modal -->
        <div x-show="showEditModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
            <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-5xl mx-4 max-h-[90vh] overflow-y-auto">
                <h3 class="text-lg font-bold mb-1">Edit Period</h3>
                <p class="text-sm text-gray-500 mb-4">Edit measurements for multiple students in an existing period.</p>

                <form action="{{ route('encoder.students.assessment.bulk.update') }}" method="POST" @submit.prevent="requestConfirmation($event, 'edit')">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Period</label>
                        <select name="measurement_period" x-model="editPeriod" @change="refreshEditValues()" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            <option value="">Select Period</option>
                            <option value="baseline">Baseline</option>
                            <option value="midline">Midline</option>
                            <option value="endline">Endline</option>
                        </select>
                    </div>

                    <div class="overflow-x-auto mb-6">
                        <table class="w-full text-sm border-collapse">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr><th class="px-3 py-2 border text-left">Learner</th><th class="px-3 py-2 border">Weight (kg)</th><th class="px-3 py-2 border">Height (cm)</th></tr>
                            </thead>
                            <tbody>
                                @foreach($students ?? [] as $student)
                                    @php
                                        $measurements = collect(['baseline', 'midline', 'endline'])->mapWithKeys(function ($period) use ($student) {
                                            $record = $student->periodProgress[ucfirst($period)][0] ?? null;
                                            return [$period => $record ? ['weight' => $record->weight, 'height' => $record->height] : null];
                                        });
                                    @endphp
                                    <tr data-edit-row data-values='@json($measurements)'>
                                        <td class="px-3 py-2 border whitespace-nowrap">{{ $student->last_name }}, {{ $student->first_name }}</td>
                                        <td class="px-3 py-2 border"><input type="hidden" name="measurements[{{ $student->id }}][student_id]" value="{{ $student->id }}"><input type="number" name="measurements[{{ $student->id }}][weight]" data-edit-weight step="0.1" min="0.1" class="w-full border border-gray-300 rounded px-2 py-1"></td>
                                        <td class="px-3 py-2 border"><input type="number" name="measurements[{{ $student->id }}][height]" data-edit-height step="0.1" min="0.1" class="w-full border border-gray-300 rounded px-2 py-1"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-amber-600 text-white px-4 py-2 rounded text-sm hover:bg-amber-700 font-semibold">Update</button>
                        <button type="button" @click="closeEditModal()" class="flex-1 bg-gray-400 text-white px-4 py-2 rounded text-sm hover:bg-gray-500 font-semibold">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Period Confirmation Modal -->
        <div x-show="showConfirmation" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[60]" style="display: none;">
            <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md mx-4">
                <h3 class="text-lg font-bold mb-2">Confirm Period Changes</h3>
                <p class="text-sm text-gray-600 mb-6" x-text="confirmationMessage"></p>
                <div class="flex gap-2">
                    <button type="button" @click="confirmSubmission()" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 font-semibold">Confirm</button>
                    <button type="button" @click="showConfirmation = false" class="flex-1 bg-gray-400 text-white px-4 py-2 rounded text-sm hover:bg-gray-500 font-semibold">Cancel</button>
                </div>
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
                showEditModal: false,
                showConfirmation: false,
                showEmailModal: false,
                currentStudentId: null,
                currentGuardianEmail: '',
                addPeriod: '',
                editPeriod: '',
                pendingForm: null,
                confirmationMessage: '',
                openPeriodModal() {
                    this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                    this.addPeriod = '';
                    document.querySelectorAll('[data-add-row] input[type="number"]').forEach((input) => {
                        input.value = '';
                        input.readOnly = false;
                        input.required = false;
                    });
                },
                refreshAddValues() {
                    document.querySelectorAll('[data-add-row]').forEach((row) => {
                        const values = JSON.parse(row.dataset.values || '{}');
                        const measurement = values[this.addPeriod] || null;
                        const weight = row.querySelector('[data-add-weight]');
                        const height = row.querySelector('[data-add-height]');
                        const hasMeasurement = measurement !== null;

                        weight.value = measurement?.weight || '';
                        height.value = measurement?.height || '';
                        weight.readOnly = hasMeasurement;
                        height.readOnly = hasMeasurement;
                        weight.required = !hasMeasurement;
                        height.required = !hasMeasurement;
                    });
                },
                openEditPeriodModal() {
                    this.showEditModal = true;
                },
                refreshEditValues() {
                    document.querySelectorAll('[data-edit-row]').forEach((row) => {
                        const values = JSON.parse(row.dataset.values || '{}');
                        const measurement = values[this.editPeriod] || {};
                        row.querySelector('[data-edit-weight]').value = measurement.weight || '';
                        row.querySelector('[data-edit-height]').value = measurement.height || '';
                    });
                },
                requestConfirmation(event, action) {
                    const form = event.target;
                    const period = form.querySelector('[name="measurement_period"]');
                    const weightInputs = form.querySelectorAll('[name$="[weight]"]');
                    const count = Array.from(weightInputs).filter((input) => input.value !== '').length;
                    const verb = action === 'edit' ? 'editing' : 'adding';
                    const periodName = period.options[period.selectedIndex]?.text || 'the selected period';
                    this.pendingForm = form;
                    this.confirmationMessage = `${verb} ${count} student weight and height record${count === 1 ? '' : 's'} in ${periodName}. Please confirm.`;
                    this.showConfirmation = true;
                },
                confirmSubmission() {
                    this.showConfirmation = false;
                    if (this.pendingForm) {
                        this.pendingForm.submit();
                    }
                },
                closeEditModal() {
                    this.showEditModal = false;
                    this.editPeriod = '';
                    document.querySelectorAll('[data-edit-row] input[type="number"]').forEach((input) => input.value = '');
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

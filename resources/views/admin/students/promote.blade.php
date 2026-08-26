@extends('layouts.dashboard')

@section('content')
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Student Promotion & Annual Enrollment</h1>
                <p class="text-sm text-gray-500 mt-1">Promote and enroll students from previous academic years into the active school year ({{ $activeSy?->school_year ?? 'N/A' }}).</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Source Selection Filter Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-filter text-blue-600 mr-2"></i> Select Source Academic Year</h2>
            <form method="GET" action="{{ route('super-admin.students.promote') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="w-full sm:w-1/2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Source School Year</label>
                    <select name="source_school_year_id" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500">
                        <option value="">-- Select Source School Year --</option>
                        @foreach($allSy as $sy)
                            <option value="{{ $sy->id }}" {{ $sourceSyId == $sy->id ? 'selected' : '' }}>
                                {{ $sy->school_year }} {{ $sy->is_active ? '(Active)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="bg-blue-600 text-white font-semibold px-6 py-2.5 rounded-lg text-sm hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-1"></i> Load Students
                    </button>
                </div>
            </form>
        </div>

        @if($sourceSyId)
        <!-- Promotion Form -->
        <form method="POST" action="{{ route('super-admin.students.promote.store') }}" class="space-y-6">
            @csrf
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-user-graduate text-emerald-600 mr-2"></i> Target Assignment for Active Year ({{ $activeSy?->school_year ?? '' }})</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Target Grade Level <span class="text-red-500">*</span></label>
                        <select name="grade_level" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500">
                            <option value="">-- Select Grade Level --</option>
                            <option value="Kinder">Kinder</option>
                            <option value="Grade 1">Grade 1</option>
                            <option value="Grade 2">Grade 2</option>
                            <option value="Grade 3">Grade 3</option>
                            <option value="Grade 4">Grade 4</option>
                            <option value="Grade 5">Grade 5</option>
                            <option value="Grade 6">Grade 6</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Target Section <span class="text-red-500">*</span></label>
                        <select name="section_id" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500">
                            <option value="">-- Select Section --</option>
                            @foreach($sections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->grade_level }} - {{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 border w-10 text-center">
                                    <input type="checkbox" id="selectAll" onclick="toggleAll(this)">
                                </th>
                                <th class="px-4 py-3 border">LRN / ID</th>
                                <th class="px-4 py-3 border">Student Name</th>
                                <th class="px-4 py-3 border">Gender</th>
                                <th class="px-4 py-3 border">Previous Grade & Section</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($sourceStudents as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 border text-center">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox">
                                </td>
                                <td class="px-4 py-3 border font-mono text-xs text-gray-700">{{ $student->student_number }}</td>
                                <td class="px-4 py-3 border font-bold text-gray-900">{{ $student->last_name }}, {{ $student->first_name }} {{ $student->middle_name }}</td>
                                <td class="px-4 py-3 border">{{ $student->gender }}</td>
                                <td class="px-4 py-3 border">{{ $student->grade_level }} - {{ $student->section }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 border text-center text-gray-500">No students found in the selected source school year.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sourceStudents->isNotEmpty())
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-emerald-600 text-white font-semibold px-6 py-2.5 rounded-lg text-sm hover:bg-emerald-750 transition">
                        <i class="fas fa-check-circle mr-1"></i> Enroll / Promote Selected Students
                    </button>
                </div>
                @endif
            </div>
        </form>
        @endif
    </div>

    <script>
        function toggleAll(source) {
            checkboxes = document.getElementsByClassName('student-checkbox');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
@endsection

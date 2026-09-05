@extends('layouts.dashboard')

@section('content')
    <div class="flex flex-col gap-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sections & Adviser Assignments</h1>
                <p class="text-sm text-gray-500 mt-1">Manage class sections and assign teacher advisers for the active school year ({{ $activeSy?->school_year ?? 'N/A' }}).</p>
            </div>
            <div>
                <form method="POST" action="{{ route($rolePrefix . '.sections.carry-over') }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Carry over sections and default adviser assignments from the previous school year?')" class="bg-emerald-600 text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-emerald-700 transition">
                        <i class="fas fa-copy mr-1"></i> Carry Over from Previous Year
                    </button>
                </form>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Add Section Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-blue-600"></i> Add New Section
                </h2>
                <form method="POST" action="{{ route($rolePrefix . '.sections.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Grade Level <span class="text-red-500">*</span></label>
                        <select name="grade_level" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500">
                            <option value="">-- Select Grade Level --</option>
                            @foreach($gradeLevels as $grade)
                                <option value="{{ $grade }}" {{ old('grade_level') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Section Name / Label <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. A, Sunflower, Diamond" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Assigned Adviser (Teacher)</label>
                        <select name="adviser_id" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500">
                            <option value="">-- Unassigned --</option>
                            @foreach($encoders as $encoder)
                                <option value="{{ $encoder->id }}" {{ old('adviser_id') == $encoder->id ? 'selected' : '' }}>
                                    {{ $encoder->name }} ({{ $encoder->position ?? 'Encoder' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2.5 rounded-lg text-sm hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-1"></i> Create Section
                    </button>
                </form>
            </div>

            <!-- Sections List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 lg:col-span-2">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-list text-blue-600"></i> Active Sections & Advisers
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse bg-white text-left text-sm text-gray-500">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 border">Grade Level</th>
                                <th class="px-4 py-3 border">Section Name</th>
                                <th class="px-4 py-3 border">Assigned Adviser</th>
                                <th class="px-4 py-3 border text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($sections as $section)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 border font-semibold text-gray-800">{{ $section->grade_level }}</td>
                                <td class="px-4 py-3 border font-bold text-gray-900">{{ $section->name }}</td>
                                <td class="px-4 py-3 border">
                                    @if($section->adviser)
                                        <span class="font-semibold text-blue-700">{{ $section->adviser->name }}</span>
                                        <span class="text-xs text-gray-400 block">{{ $section->adviser->email }}</span>
                                    @else
                                        <span class="text-amber-600 italic text-xs font-semibold">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 border text-center whitespace-nowrap">
                                    <button type="button" onclick="openEditModal({{ $section->id }}, '{{ $section->grade_level }}', '{{ $section->name }}', '{{ $section->adviser_id }}')" class="px-2.5 py-1.5 bg-amber-100 text-amber-800 hover:bg-amber-200 rounded text-xs font-bold transition mr-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                     <button type="button" onclick="openDeleteModal({{ $section->id }}, '{{ $section->grade_level }} - {{ $section->name }}')" class="px-2.5 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded text-xs font-bold transition">
                                         <i class="fas fa-trash"></i> Delete
                                     </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 border text-center text-gray-500">No sections created for this active school year yet. Click "Carry Over" or create one.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-xl mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Section & Adviser</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Grade Level <span class="text-red-500">*</span></label>
                    <select name="grade_level" id="editGradeLevel" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500">
                        @foreach($gradeLevels as $grade)
                            <option value="{{ $grade }}">{{ $grade }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Section Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="editName" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Assigned Adviser (Teacher)</label>
                    <select name="adviser_id" id="editAdviserId" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500">
                        <option value="">-- Unassigned --</option>
                        @foreach($encoders as $encoder)
                            <option value="{{ $encoder->id }}">{{ $encoder->name }} ({{ $encoder->position ?? 'Encoder' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded text-sm font-semibold hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-semibold hover:bg-blue-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full shadow-xl text-center mx-4">
            <div class="text-red-500 text-4xl mb-4">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Delete</h3>
            <p class="text-sm text-gray-600 mb-6">Are you sure you want to delete section <span id="deleteSectionName" class="font-semibold text-gray-800"></span>? This action cannot be undone.</p>
            <div class="flex justify-center gap-4">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded text-sm font-semibold hover:bg-gray-300">
                    Cancel
                </button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded text-sm font-semibold hover:bg-red-700">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(id, grade, name, adviserId) {
            const form = document.getElementById('editForm');
            form.action = "{{ url(auth()->user()->isSuperAdmin() ? 'super-admin/sections' : 'admin/sections') }}/" + id;
            document.getElementById('editGradeLevel').value = grade;
            document.getElementById('editName').value = name;
            document.getElementById('editAdviserId').value = adviserId || '';
            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
        function openDeleteModal(id, sectionName) {
            const form = document.getElementById('deleteForm');
            form.action = "{{ url(auth()->user()->isSuperAdmin() ? 'super-admin/sections' : 'admin/sections') }}/" + id;
            document.getElementById('deleteSectionName').textContent = sectionName;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
@endsection

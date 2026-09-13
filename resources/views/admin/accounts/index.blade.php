@extends('layouts.dashboard')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header with Add Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->isSuperAdmin() ? 'Admin Accounts' : 'Adviser Accounts' }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage account profiles and secure deletions.</p>
        </div>
        <a href="{{ auth()->user()->isSuperAdmin() ? route('super-admin.accounts.create') : route('admin.accounts.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 font-semibold inline-flex items-center gap-2 whitespace-nowrap">
            <i class="fas fa-plus"></i> {{ auth()->user()->isSuperAdmin() ? 'Add New Admin' : 'Add New Adviser' }}
        </a>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->has('delete_error'))
    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm">
        {{ $errors->first('delete_error') }}
    </div>
    @endif

    <!-- Filters & Search Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ auth()->user()->isSuperAdmin() ? route('super-admin.accounts.index') : route('admin.accounts.index') }}" class="space-y-4" x-data>
            <!-- Search Bar -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Search by Name or DepEd ID</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Enter name or DepEd Employee ID..." @input.debounce.350ms="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>

            <!-- Filters Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Grade Level Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Advisory Grade Level</label>
                    <select name="grade_level" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Grades</option>
                        @foreach($gradeLevels as $grade)
                        <option value="{{ $grade }}" @selected((string) request('grade_level')===(string) $grade)>{{ (int) $grade === 0 ? 'Kinder' : 'Grade ' . $grade }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Position Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Position</label>
                    <select name="position" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Positions</option>
                        @foreach($positions as $position)
                        <option value="{{ $position }}" {{ request('position') == $position ? 'selected' : '' }}>{{ $position }}</option>
                        @endforeach
                    </select>
                </div>

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

                <!-- Sort -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sort By</label>
                    <select name="sort_by" @change="$el.form.requestSubmit()" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="name_asc" {{ request('sort_by', 'name_asc') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="grade_level_asc" {{ request('sort_by') == 'grade_level_asc' ? 'selected' : '' }}>Grade Level (Ascending)</option>
                        <option value="date_newest" {{ request('sort_by') == 'date_newest' ? 'selected' : '' }}>Date Added (Newest)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end pt-1">
                <a href="{{ auth()->user()->isSuperAdmin() ? route('super-admin.accounts.index') : route('admin.accounts.index') }}" class="text-sm text-gray-600 hover:text-blue-600 inline-flex items-center gap-2">
                    <i class="fas fa-rotate-left"></i> Reset filters
                </a>
            </div>
        </form>
    </div>

    <!-- Advisers Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-gray-700">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 border text-left text-xs font-semibold text-gray-700 uppercase">No.</th>
                        <th class="px-4 py-3 border text-left text-xs font-semibold text-gray-700 uppercase">Full Name</th>
                        <th class="px-4 py-3 border text-left text-xs font-semibold text-gray-700 uppercase">DepEd ID</th>
                        <th class="px-4 py-3 border text-left text-xs font-semibold text-gray-700 uppercase">Position</th>
                        <th class="px-4 py-3 border text-left text-xs font-semibold text-gray-700 uppercase">Grade / Section</th>
                        <th class="px-4 py-3 border text-left text-xs font-semibold text-gray-700 uppercase">Email</th>
                        <th class="px-4 py-3 border text-center text-xs font-semibold text-gray-700 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($advisers as $index => $adviser)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 border text-sm text-gray-900">{{ $advisers->firstItem() + $index }}</td>
                        <td class="px-4 py-4 border text-sm font-semibold text-gray-900">{{ $adviser->name }}</td>
                        <td class="px-4 py-4 border text-sm text-gray-600">{{ $adviser->deped_id ?? '-' }}</td>
                        <td class="px-4 py-4 border text-sm text-gray-600">{{ $adviser->position ?? '-' }}</td>
                        <td class="px-4 py-4 border text-sm text-gray-600">{{ $adviser->advisory_grade_level ?? '-' }} - {{ $adviser->advisory_section ?? '-' }}</td>
                        <td class="px-4 py-4 border text-sm text-gray-600 truncate">{{ $adviser->email }}</td>

                        <!-- Actions Column -->
                        <td class="px-4 py-4 border text-center space-x-2 whitespace-nowrap">
                            <button type="button"
                                onclick="openEditModal(this)"
                                data-action="{{ auth()->user()->isSuperAdmin() ? route('super-admin.accounts.update', $adviser) : route('admin.accounts.update', $adviser) }}"
                                data-name="{{ $adviser->name }}"
                                data-sex="{{ $adviser->sex }}"
                                data-birthdate="{{ $adviser->birthdate }}"
                                data-email="{{ $adviser->email }}"
                                data-deped-id="{{ $adviser->deped_id }}"
                                data-position="{{ $adviser->position }}"
                                data-grade-level="{{ $adviser->advisory_grade_level }}"
                                data-section="{{ $adviser->advisory_section }}"
                                class="text-xs px-3 py-1.5 bg-blue-100 text-blue-800 hover:bg-blue-200 rounded font-semibold">
                                Edit
                            </button>

                            <!-- Delete Button (Triggers Level 2 Modal) -->
                            <button type="button" onclick="openDeleteModal('{{ $adviser->id }}', '{{ $adviser->name }}')" class="text-xs px-3 py-1.5 bg-rose-100 text-rose-800 hover:bg-rose-200 rounded font-semibold">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-2xl mb-2"></i>
                            <p class="mt-2">No accounts found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($advisers->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $advisers->render() }}
        </div>
        @endif
    </div>
</div>

<!-- Managed Account Profile Modal -->
<div id="editAccountModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="editAccountTitle">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 shadow-xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h3 id="editAccountTitle" class="text-lg font-bold text-gray-900">Profile Information</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-700 text-xl" aria-label="Close">&times;</button>
        </div>

        <form id="editAccountForm" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="edit_name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                    <input id="edit_name" type="text" name="name" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="edit_sex" class="block text-sm font-semibold text-gray-700 mb-2">Sex</label>
                    <select id="edit_sex" name="sex" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm bg-white text-gray-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">Select Sex</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div>
                    <label for="edit_birthdate" class="block text-sm font-semibold text-gray-700 mb-2">Birthdate</label>
                    <input id="edit_birthdate" type="date" name="birthdate" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="edit_email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input id="edit_email" type="email" name="email" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="edit_deped_id" class="block text-sm font-semibold text-gray-700 mb-2">DepEd ID / Employee ID</label>
                    <input id="edit_deped_id" type="text" name="deped_id" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="edit_position" class="block text-sm font-semibold text-gray-700 mb-2">Position / Title</label>
                    <input id="edit_position" type="text" name="position" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                @if(!auth()->user()->isSuperAdmin())
                <div>
                    <label for="edit_grade_level" class="block text-sm font-semibold text-gray-700 mb-2">Advisory Grade Level</label>
                    <select id="edit_grade_level" name="advisory_grade_level" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm bg-white text-gray-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">Select Grade Level</option>
                        @foreach([0, 1, 2, 3, 4, 5, 6] as $grade)
                            <option value="{{ $grade }}">{{ $grade === 0 ? 'Kinder' : 'Grade ' . $grade }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_section" class="block text-sm font-semibold text-gray-700 mb-2">Advisory Section</label>
                    <input id="edit_section" type="text" name="advisory_section" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                @endif
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-300">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Level 2 Security Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Account Deletion</h3>
        <p class="text-sm text-gray-600 mb-4">
            Are you sure you want to delete <span id="deleteUserName" class="font-semibold text-gray-900"></span>? This will archive the account and prevent future access.
        </p>

        <form id="deleteForm" method="POST" class="space-y-4">
            @csrf
            @method('DELETE')

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Enter your password to confirm:</label>
                <input type="password" name="password" required placeholder="Your admin password" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-semibold hover:bg-rose-700">
                    Confirm Deletion
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 300); // 300ms debounce for smooth real-time typing
        });
    }

    function openDeleteModal(userId, userName) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        const nameSpan = document.getElementById('deleteUserName');

        form.action = `/accounts/${userId}`;
        nameSpan.textContent = userName;
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
    }

    function openEditModal(button) {
        const modal = document.getElementById('editAccountModal');
        const form = document.getElementById('editAccountForm');
        const data = button.dataset;

        form.action = data.action;
        document.getElementById('edit_name').value = data.name || '';
        document.getElementById('edit_sex').value = data.sex || '';
        document.getElementById('edit_birthdate').value = data.birthdate || '';
        document.getElementById('edit_email').value = data.email || '';
        document.getElementById('edit_deped_id').value = data.depedId || '';
        document.getElementById('edit_position').value = data.position || '';

        const gradeLevel = document.getElementById('edit_grade_level');
        const section = document.getElementById('edit_section');
        if (gradeLevel) gradeLevel.value = data.gradeLevel || '';
        if (section) section.value = data.section || '';

        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editAccountModal').classList.add('hidden');
    }
</script>
@endsection
@extends('layouts.dashboard')

@section('content')
    <div class="flex flex-col gap-6 max-w-4xl mx-auto">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Account Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your account profile information and security password.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Profile Information Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Profile Information</h2>
            <form method="POST" action="{{ route('account.settings.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <!-- Email (Non-editable) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address (Read-only)</label>
                        <input type="email" value="{{ $user->email }}" disabled class="w-full border border-gray-200 bg-gray-100 rounded-lg p-2.5 text-sm text-gray-500 cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">Email address cannot be modified.</p>
                    </div>

                    <!-- DepEd ID -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">DepEd ID / Employee ID</label>
                        <input type="text" name="deped_id" value="{{ old('deped_id', $user->deped_id) }}" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <!-- Sex -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sex</label>
                        <select name="sex" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">Select Sex</option>
                            <option value="Male" {{ old('sex', $user->sex) == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex', $user->sex) == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <!-- Birthdate -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Birthdate</label>
                        <input type="date" name="birthdate" value="{{ old('birthdate', $user->birthdate) }}" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <!-- Position -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Position / Title</label>
                        <input type="text" name="position" value="{{ old('position', $user->position) }}" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <!-- Advisory Grade Level -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Advisory Grade Level</label>
                        <select name="advisory_grade_level" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">Select Grade Level</option>
                            @foreach(['Kinder', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'] as $grade)
                                <option value="{{ $grade }}" {{ old('advisory_grade_level', $user->advisory_grade_level) == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Advisory Section -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Advisory Section</label>
                        <input type="text" name="advisory_section" value="{{ old('advisory_section', $user->advisory_section) }}" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Update Password Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Update Password</h2>
            <form method="POST" action="{{ route('account.password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Current Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                        <input type="password" name="current_password" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                        <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-slate-800 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-slate-700">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

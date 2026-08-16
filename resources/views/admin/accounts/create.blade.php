@extends('layouts.dashboard')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Add New Adviser Account</h1>
                <p class="text-sm text-gray-500 mt-2">Create a new teacher/adviser account with advisory assignment.</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-600 p-4 rounded-lg text-sm">
                    <div class="font-semibold mb-2 flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i> Please fix the following errors:
                    </div>
                    <ul class="space-y-1 ml-6">
                        @foreach ($errors->all() as $error)
                            <li class="list-disc">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('admin.accounts.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- DepEd ID & Full Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            DepEd Employee ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="deped_id" value="{{ old('deped_id') }}" required 
                               class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('deped_id') border-red-500 @enderror"
                               placeholder="e.g., DEP-2024-001234">
                        @error('deped_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                               class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="e.g., Maria Garcia Santos">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email & Sex -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                               class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                               placeholder="maria.garcia@deped.gov.ph">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Sex <span class="text-red-500">*</span>
                        </label>
                        <select name="sex" required 
                                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('sex') border-red-500 @enderror">
                            <option value="">Select Sex</option>
                            <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('sex')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" required 
                               class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                               placeholder="Minimum 8 characters">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required 
                               class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('password_confirmation') border-red-500 @enderror"
                               placeholder="Re-enter password">
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Birthdate -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Birthdate <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" required 
                           class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('birthdate') border-red-500 @enderror">
                    @error('birthdate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position & Advisory Grade Level -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Position <span class="text-red-500">*</span>
                        </label>
                        <select name="position" required 
                                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('position') border-red-500 @enderror">
                            <option value="">Select Position</option>
                            @foreach($positions as $position)
                                <option value="{{ $position }}" {{ old('position') == $position ? 'selected' : '' }}>{{ $position }}</option>
                            @endforeach
                        </select>
                        @error('position')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Advisory Grade Level <span class="text-red-500">*</span>
                        </label>
                        <select name="advisory_grade_level" required 
                                class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('advisory_grade_level') border-red-500 @enderror">
                            <option value="">Select Grade Level</option>
                            @foreach($gradeLevels as $grade)
                                <option value="{{ $grade }}" {{ old('advisory_grade_level') == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                            @endforeach
                        </select>
                        @error('advisory_grade_level')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Advisory Section -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Advisory Section <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="advisory_section" value="{{ old('advisory_section') }}" required 
                           class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('advisory_section') border-red-500 @enderror"
                           placeholder="e.g., Diamond, Sapphire, Emerald">
                    @error('advisory_section')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.accounts.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-300 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-save"></i> Create Adviser Account
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

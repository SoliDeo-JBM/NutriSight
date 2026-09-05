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
                        <div class="relative">
                            <input type="password" id="password" name="password" required 
                                   class="w-full border border-gray-300 rounded-lg p-2.5 pr-10 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                                   placeholder="Minimum 8 characters">
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition-colors focus:outline-none">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.43 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.779M6.228 6.228L3 3m3.228 3.228L3 3m.252 15.8s.031-1.612-.234-2.5a5.006 5.006 0 01-.233-2.5M16.5 13.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <line x1="3" y1="3" x2="21" y2="21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                            </button>
                        </div>
                        <p id="passwordStrengthIndicator" class="text-xs mt-1 font-medium transition-all"></p>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required 
                                   class="w-full border border-gray-300 rounded-lg p-2.5 pr-10 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('password_confirmation') border-red-500 @enderror"
                                   placeholder="Re-enter password">
                            <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition-colors focus:outline-none">
                                <svg id="eyeConfirmIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.43 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg id="eyeSlashConfirmIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.779M6.228 6.228L3 3m3.228 3.228L3 3m.252 15.8s.031-1.612-.234-2.5a5.006 5.006 0 01-.233-2.5M16.5 13.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <line x1="3" y1="3" x2="21" y2="21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                            </button>
                        </div>
                        <p id="confirmPasswordMatchIndicator" class="text-xs mt-1 font-medium transition-all"></p>
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <script>
                    document.getElementById('togglePassword').addEventListener('click', function () {
                        const passwordInput = document.getElementById('password');
                        const eyeIcon = document.getElementById('eyeIcon');
                        const eyeSlashIcon = document.getElementById('eyeSlashIcon');
                        
                        if (passwordInput.type === 'password') {
                            passwordInput.type = 'text';
                            eyeIcon.style.display = 'none';
                            eyeSlashIcon.style.display = 'block';
                        } else {
                            passwordInput.type = 'password';
                            eyeIcon.style.display = 'block';
                            eyeSlashIcon.style.display = 'none';
                        }
                    });

                    document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
                        const passwordInput = document.getElementById('password_confirmation');
                        const eyeIcon = document.getElementById('eyeConfirmIcon');
                        const eyeSlashIcon = document.getElementById('eyeSlashConfirmIcon');
                        
                        if (passwordInput.type === 'password') {
                            passwordInput.type = 'text';
                            eyeIcon.style.display = 'none';
                            eyeSlashIcon.style.display = 'block';
                        } else {
                            passwordInput.type = 'password';
                            eyeIcon.style.display = 'block';
                            eyeSlashIcon.style.display = 'none';
                        }
                    });

                    const passwordInput = document.getElementById('password');
                    const strengthIndicator = document.getElementById('passwordStrengthIndicator');

                    passwordInput.addEventListener('input', function() {
                        const val = this.value;
                        if (val.length === 0) {
                            strengthIndicator.textContent = '';
                            return;
                        }
                        if (val.length < 8) {
                            strengthIndicator.textContent = 'Password should be at least 8 characters long';
                            strengthIndicator.className = 'text-xs mt-1 font-medium text-red-500';
                        } else if (!/[a-z]/.test(val)) {
                            strengthIndicator.textContent = 'Password should have a lowercase letter';
                            strengthIndicator.className = 'text-xs mt-1 font-medium text-red-500';
                        } else if (!/[A-Z]/.test(val)) {
                            strengthIndicator.textContent = 'Password should have an uppercase letter';
                            strengthIndicator.className = 'text-xs mt-1 font-medium text-red-500';
                        } else if (!/[0-9]/.test(val)) {
                            strengthIndicator.textContent = 'Password should have a number';
                            strengthIndicator.className = 'text-xs mt-1 font-medium text-red-500';
                        } else if (!/[^A-Za-z0-9]/.test(val)) {
                            strengthIndicator.textContent = 'Password should have a symbol';
                            strengthIndicator.className = 'text-xs mt-1 font-medium text-red-500';
                        } else {
                            strengthIndicator.textContent = 'Password is acceptable';
                            strengthIndicator.className = 'text-xs mt-1 font-medium text-green-600';
                        }
                    });

                    const confirmPasswordInput = document.getElementById('password_confirmation');
                    const matchIndicator = document.getElementById('confirmPasswordMatchIndicator');

                    function checkPasswordMatch() {
                        const pass = passwordInput.value;
                        const confirmPass = confirmPasswordInput.value;
                        if (confirmPass.length === 0) {
                            matchIndicator.textContent = '';
                            return;
                        }
                        if (pass !== confirmPass) {
                            matchIndicator.textContent = 'Passwords do not match';
                            matchIndicator.className = 'text-xs mt-1 font-medium text-red-500';
                        } else {
                            matchIndicator.textContent = 'Passwords match';
                            matchIndicator.className = 'text-xs mt-1 font-medium text-green-600';
                        }
                    }

                    passwordInput.addEventListener('input', checkPasswordMatch);
                    confirmPasswordInput.addEventListener('input', checkPasswordMatch);
                </script>

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

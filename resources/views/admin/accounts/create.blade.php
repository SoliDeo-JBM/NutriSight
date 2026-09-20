@extends('layouts.dashboard')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ $isSuperAdmin ? 'Add New Admin Account' : 'Add New Adviser Account' }}</h1>
            <p class="text-sm text-gray-500 mt-2">{{ $isSuperAdmin ? 'Create a new administrator account.' : 'Create a new teacher/adviser account with advisory assignment.' }}</p>
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
        <form action="{{ route($isSuperAdmin ? 'super-admin.accounts.store' : 'admin.accounts.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- DepEd Employee ID -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    DepEd Employee ID <span class="text-red-500">*</span>
                </label>
                <input type="text" name="deped_id" value="{{ old('deped_id') }}" required maxlength="50" pattern="[A-Za-z0-9-]+"
                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('deped_id') border-red-500 @enderror"
                    placeholder="e.g., 123456">
                @error('deped_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Name -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required maxlength="255" pattern="[A-Za-zÀ-ÿ .'-]+" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('last_name') border-red-500 @enderror" placeholder="e.g., Santos">
                    @error('last_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required maxlength="255" pattern="[A-Za-zÀ-ÿ .'-]+" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('first_name') border-red-500 @enderror" placeholder="e.g., Maria">
                    @error('first_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Name Extension (Optional)</label>
                    <input type="text" name="name_extension" value="{{ old('name_extension') }}" maxlength="50" pattern="[A-Za-zÀ-ÿ .'-]+" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('name_extension') border-red-500 @enderror" placeholder="e.g., Jr., III">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Middle Name (Optional)</label>
                    <input type="text" name="middle_name" value="{{ old('middle_name') }}" maxlength="255" pattern="[A-Za-zÀ-ÿ .'-]+" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('middle_name') border-red-500 @enderror" placeholder="e.g., Garcia">
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
                        <option value="" disabled {{ old('sex') ? '' : 'selected' }}>Select Sex</option>
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
                        <button type="button" id="togglePassword" aria-label="Show password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-orange-600 transition-colors focus:outline-none">
                            <i id="eyeIcon" class="fas fa-eye"></i>
                            <i id="eyeSlashIcon" class="fas fa-eye-slash hidden"></i>
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
                        <button type="button" id="toggleConfirmPassword" aria-label="Show password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-orange-600 transition-colors focus:outline-none">
                            <i id="eyeConfirmIcon" class="fas fa-eye"></i>
                            <i id="eyeSlashConfirmIcon" class="fas fa-eye-slash hidden"></i>
                        </button>
                    </div>
                    <p id="confirmPasswordMatchIndicator" class="text-xs mt-1 font-medium transition-all"></p>
                    @error('password_confirmation')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <script>
                document.getElementById('togglePassword').addEventListener('click', function() {
                    const passwordInput = document.getElementById('password');
                    const eyeIcon = document.getElementById('eyeIcon');
                    const eyeSlashIcon = document.getElementById('eyeSlashIcon');

                    const visible = passwordInput.type === 'text';
                    passwordInput.type = visible ? 'password' : 'text';
                    eyeIcon.classList.toggle('hidden', !visible);
                    eyeSlashIcon.classList.toggle('hidden', visible);
                    this.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
                });

                document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
                    const passwordInput = document.getElementById('password_confirmation');
                    const eyeIcon = document.getElementById('eyeConfirmIcon');
                    const eyeSlashIcon = document.getElementById('eyeSlashConfirmIcon');

                    const visible = passwordInput.type === 'text';
                    passwordInput.type = visible ? 'password' : 'text';
                    eyeIcon.classList.toggle('hidden', !visible);
                    eyeSlashIcon.classList.toggle('hidden', visible);
                    this.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
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
                <input type="date" name="birthdate" value="{{ old('birthdate') }}" required max="{{ now()->toDateString() }}"
                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('birthdate') border-red-500 @enderror">
                @error('birthdate')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Position -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Position <span class="text-red-500">*</span>
                </label>
                <select name="position" required
                    class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('position') border-red-500 @enderror">
                    <option value="" disabled {{ old('position') ? '' : 'selected' }}>Select Position</option>
                    @foreach($positions as $position)
                    <option value="{{ $position }}" {{ old('position') == $position ? 'selected' : '' }}>{{ $position }}</option>
                    @endforeach
                </select>
                @error('position')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if(!$isSuperAdmin)
            <!-- Advisory Grade Level & Section (For Encoders / Advisers only) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Advisory Grade Level <span class="text-red-500">*</span>
                    </label>
                    <select name="advisory_grade_level" required
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('advisory_grade_level') border-red-500 @enderror">
                        <option value="" disabled {{ old('advisory_grade_level') !== null ? '' : 'selected' }}>Select Grade Level</option>
                        @foreach($gradeLevels as $grade)
                        <option value="{{ $grade }}" {{ old('advisory_grade_level') == $grade ? 'selected' : '' }}>{{ $grade == 0 ? 'Kinder' : ($grade == 7 ? 'SPED' : $grade) }}</option>
                        @endforeach
                    </select>
                    @error('advisory_grade_level')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Advisory Section <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="advisory_section" value="{{ old('advisory_section') }}" required maxlength="100" pattern="[A-Za-zÀ-ÿ0-9][A-Za-zÀ-ÿ0-9 .'-]*"
                        class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 @error('advisory_section') border-red-500 @enderror"
                        placeholder="e.g., Diamond, Sapphire, Emerald">
                    @error('advisory_section')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="flex gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route($isSuperAdmin ? 'super-admin.accounts.index' : 'admin.accounts.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-save"></i> {{ $isSuperAdmin ? 'Create Admin Account' : 'Create Adviser Account' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
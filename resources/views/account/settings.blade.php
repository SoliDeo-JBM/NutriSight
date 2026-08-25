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
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" x-data="profileManager()">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Profile Information</h2>
            </div>

            <form method="POST" action="{{ route('account.settings.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="name" x-model="form.name" :disabled="!isEditing" required class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
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
                        <input type="text" name="deped_id" x-model="form.deped_id" :disabled="!isEditing" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                    </div>

                    <!-- Sex -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sex</label>
                        <select name="sex" x-model="form.sex" :disabled="!isEditing" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                            <option value="">Select Sex</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <!-- Birthdate -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Birthdate</label>
                        <input type="date" name="birthdate" x-model="form.birthdate" :disabled="!isEditing" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                    </div>

                    <!-- Position -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Position / Title</label>
                        <input type="text" name="position" x-model="form.position" :disabled="!isEditing" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                    </div>

                    <!-- Advisory Grade Level -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Advisory Grade Level</label>
                        <select name="advisory_grade_level" x-model="form.advisory_grade_level" :disabled="!isEditing" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                            <option value="">Select Grade Level</option>
                            @foreach(['Kinder', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'] as $grade)
                                <option value="{{ $grade }}">{{ $grade }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Advisory Section -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Advisory Section</label>
                        <input type="text" name="advisory_section" x-model="form.advisory_section" :disabled="!isEditing" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <template x-if="!isEditing">
                        <button type="button" @click="startEditing()" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">
                            Edit Profile
                        </button>
                    </template>
                    <template x-if="isEditing">
                        <div class="flex gap-2">
                            <button type="button" @click="cancelEditing()" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">
                                Save Changes
                            </button>
                        </div>
                    </template>
                </div>
            </form>
        </div>

        <!-- Update Password Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" x-data="passwordManager()">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-900">Update Password</h2>
            </div>

            <form method="POST" action="{{ route('account.password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Current Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password" x-model="current_password" :disabled="!isEditing" required class="w-full border border-gray-300 rounded-lg p-2.5 pr-10 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed @error('current_password') border-red-500 @enderror">
                            <button type="button" id="toggleCurrentPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 transition-colors focus:outline-none">
                                <svg id="eyeCurrentIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.43 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg id="eyeSlashCurrentIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.779M6.228 6.228L3 3m3.228 3.228L3 3m.252 15.8s.031-1.612-.234-2.5a5.006 5.006 0 01-.233-2.5M16.5 13.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <line x1="3" y1="3" x2="21" y2="21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" x-model="password" :disabled="!isEditing" required class="w-full border border-gray-300 rounded-lg p-2.5 pr-10 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed @error('password') border-red-500 @enderror" placeholder="Minimum 8 characters">
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

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" x-model="password_confirmation" :disabled="!isEditing" required class="w-full border border-gray-300 rounded-lg p-2.5 pr-10 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed @error('password_confirmation') border-red-500 @enderror" placeholder="Re-enter password">
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

                <div class="flex justify-end gap-2 pt-4">
                    <template x-if="!isEditing">
                        <button type="button" @click="startEditing()" class="bg-slate-800 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-slate-700">
                            Change Password
                        </button>
                    </template>
                    <template x-if="isEditing">
                        <div class="flex gap-2">
                            <button type="button" @click="cancelEditing()" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-semibold hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit" class="bg-slate-800 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-slate-700">
                                Update Password
                            </button>
                        </div>
                    </template>
                </div>
            </form>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function profileManager() {
            return {
                isEditing: @json($errors->has('name') || $errors->has('deped_id') || $errors->has('sex') || $errors->has('birthdate') || $errors->has('position') || $errors->has('advisory_grade_level') || $errors->has('advisory_section')),
                form: {
                    name: @json(old('name', $user->name)),
                    deped_id: @json(old('deped_id', $user->deped_id)),
                    sex: @json(old('sex', $user->sex)),
                    birthdate: @json(old('birthdate', $user->birthdate)),
                    position: @json(old('position', $user->position)),
                    advisory_grade_level: @json(old('advisory_grade_level', $user->advisory_grade_level)),
                    advisory_section: @json(old('advisory_section', $user->advisory_section)),
                },
                original: {
                    name: @json(old('name', $user->name)),
                    deped_id: @json(old('deped_id', $user->deped_id)),
                    sex: @json(old('sex', $user->sex)),
                    birthdate: @json(old('birthdate', $user->birthdate)),
                    position: @json(old('position', $user->position)),
                    advisory_grade_level: @json(old('advisory_grade_level', $user->advisory_grade_level)),
                    advisory_section: @json(old('advisory_section', $user->advisory_section)),
                },
                startEditing() {
                    this.isEditing = true;
                },
                cancelEditing() {
                    this.form = { ...this.original };
                    this.isEditing = false;
                }
            }
        }

        function passwordManager() {
            return {
                isEditing: @json($errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation')),
                current_password: '',
                password: '',
                password_confirmation: '',
                startEditing() {
                    this.isEditing = true;
                },
                cancelEditing() {
                    this.current_password = '';
                    this.password = '';
                    this.password_confirmation = '';
                    this.isEditing = false;
                    const strengthIndicator = document.getElementById('passwordStrengthIndicator');
                    const matchIndicator = document.getElementById('confirmPasswordMatchIndicator');
                    if (strengthIndicator) strengthIndicator.textContent = '';
                    if (matchIndicator) matchIndicator.textContent = '';
                }
            }
        }

        function setupPasswordToggle(inputId, toggleBtnId, eyeIconId, eyeSlashIconId) {
            const btn = document.getElementById(toggleBtnId);
            if (!btn) return;
            btn.addEventListener('click', function () {
                const input = document.getElementById(inputId);
                const eye = document.getElementById(eyeIconId);
                const eyeSlash = document.getElementById(eyeSlashIconId);
                
                if (input.type === 'password') {
                    input.type = 'text';
                    eye.style.display = 'none';
                    eyeSlash.style.display = 'block';
                } else {
                    input.type = 'password';
                    eye.style.display = 'block';
                    eyeSlash.style.display = 'none';
                }
            });
        }

        setupPasswordToggle('current_password', 'toggleCurrentPassword', 'eyeCurrentIcon', 'eyeSlashCurrentIcon');
        setupPasswordToggle('password', 'togglePassword', 'eyeIcon', 'eyeSlashIcon');
        setupPasswordToggle('password_confirmation', 'toggleConfirmPassword', 'eyeConfirmIcon', 'eyeSlashConfirmIcon');

        const passwordInput = document.getElementById('password');
        const strengthIndicator = document.getElementById('passwordStrengthIndicator');

        if (passwordInput) {
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
        }

        const confirmPasswordInput = document.getElementById('password_confirmation');
        const matchIndicator = document.getElementById('confirmPasswordMatchIndicator');

        function checkPasswordMatch() {
            if (!passwordInput || !confirmPasswordInput || !matchIndicator) return;
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

        if (passwordInput && confirmPasswordInput) {
            passwordInput.addEventListener('input', checkPasswordMatch);
            confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        }
    </script>
@endsection

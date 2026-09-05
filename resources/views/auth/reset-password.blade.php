<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1">
                <input id="password" class="block w-full pr-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 text-sm" type="password" name="password" required autocomplete="new-password" />
                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-600 transition-colors focus:outline-none">
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
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
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
        </script>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative mt-1">
                <input id="password_confirmation" class="block w-full pr-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 text-sm" type="password" name="password_confirmation" required autocomplete="new-password" />
                <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-600 transition-colors focus:outline-none">
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
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
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
                checkPasswordMatch();
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

            confirmPasswordInput.addEventListener('input', checkPasswordMatch);
        </script>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<x-guest-layout>
    <form id="reset-password-form" method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reset Password</h1>
            <p class="mt-1 text-sm text-gray-600">Choose a new password for your account.</p>
        </div>

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Email Address')" class="mb-2" />
            <x-text-input id="email" class="block w-full rounded-lg border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('New Password')" class="mb-2" />
            <div class="relative">
                <input id="password" class="block w-full rounded-lg border border-gray-300 px-4 py-3 pr-12 text-sm text-gray-900 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500" type="password" name="password" required autocomplete="new-password" placeholder="Minimum 8 characters" />
                <button type="button" id="togglePassword" aria-label="Show password" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 transition-colors hover:text-orange-600 focus:outline-none">
                    <i id="eyeIcon" class="fas fa-eye"></i>
                    <i id="eyeSlashIcon" class="fas fa-eye-slash hidden"></i>
                </button>
            </div>
            <p id="passwordStrengthIndicator" class="mt-1 text-xs font-medium" aria-live="polite"></p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="mb-2" />
            <div class="relative">
                <input id="password_confirmation" class="block w-full rounded-lg border border-gray-300 px-4 py-3 pr-12 text-sm text-gray-900 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Re-enter password" />
                <button type="button" id="toggleConfirmPassword" aria-label="Show confirmation password" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 transition-colors hover:text-orange-600 focus:outline-none">
                    <i id="eyeConfirmIcon" class="fas fa-eye"></i>
                    <i id="eyeSlashConfirmIcon" class="fas fa-eye-slash hidden"></i>
                </button>
            </div>
            <p id="confirmPasswordMatchIndicator" class="mt-1 text-xs font-medium" aria-live="polite"></p>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="flex w-full justify-center rounded-lg bg-orange-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
            {{ __('Reset Password') }}
        </button>
    </form>

    <script>
        (() => {
            const form = document.getElementById('reset-password-form');
            const password = document.getElementById('password');
            const confirmation = document.getElementById('password_confirmation');
            const strength = document.getElementById('passwordStrengthIndicator');
            const match = document.getElementById('confirmPasswordMatchIndicator');

            if (!form || !password || !confirmation || !strength || !match) return;

            const setToggle = (input, button, eye, eyeSlash) => {
                button.addEventListener('click', () => {
                    const visible = input.type === 'text';
                    input.type = visible ? 'password' : 'text';
                    eye.classList.toggle('hidden', !visible);
                    eyeSlash.classList.toggle('hidden', visible);
                    button.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
                });
            };

            setToggle(password, document.getElementById('togglePassword'), document.getElementById('eyeIcon'), document.getElementById('eyeSlashIcon'));
            setToggle(confirmation, document.getElementById('toggleConfirmPassword'), document.getElementById('eyeConfirmIcon'), document.getElementById('eyeSlashConfirmIcon'));

            const updateStrength = () => {
                const value = password.value;
                if (!value) {
                    strength.textContent = '';
                    return;
                }

                const requirement = value.length < 8 ? 'at least 8 characters' : !/[a-z]/.test(value) ? 'a lowercase letter' : !/[A-Z]/.test(value) ? 'an uppercase letter' : !/[0-9]/.test(value) ? 'a number' : !/[^A-Za-z0-9]/.test(value) ? 'a symbol' : null;
                strength.textContent = requirement ? `Password should have ${requirement}` : 'Password is acceptable';
                strength.className = `mt-1 text-xs font-medium ${requirement ? 'text-red-500' : 'text-green-600'}`;
            };

            const updateMatch = () => {
                if (!confirmation.value) {
                    match.textContent = '';
                    return;
                }

                const matched = password.value === confirmation.value;
                match.textContent = matched ? 'Passwords match' : 'Passwords do not match';
                match.className = `mt-1 text-xs font-medium ${matched ? 'text-green-600' : 'text-red-500'}`;
            };

            password.addEventListener('input', () => {
                updateStrength();
                updateMatch();
            });
            confirmation.addEventListener('input', updateMatch);
            form.addEventListener('submit', (event) => {
                if (password.value !== confirmation.value) {
                    event.preventDefault();
                    updateMatch();
                    confirmation.focus();
                }
            });
        })();
    </script>
</x-guest-layout>
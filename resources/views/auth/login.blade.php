<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NutriSight') }} - Sign In</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Fallback Tailwind CDN to ensure styles load during development/testing -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans text-gray-900 antialiased bg-slate-50">
    <!-- Main Wrapper: Forces a full-screen layout -->
    <div class="flex min-h-screen w-full overflow-hidden">
        
        <!-- LEFT COLUMN: Brand & Visuals -->
        <!-- Visible on md (768px) and above. Exactly 50% width. -->
        <div class="hidden md:flex md:w-1/2 relative items-center justify-center bg-cover bg-center" 
             style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1200');">
            
            <!-- High-contrast overlay for readability -->
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            
            <!-- Centered Brand Content -->
            <div class="relative z-10 text-center px-12 max-w-xl">
                <div class="mb-8 flex justify-center">
                    <x-application-logo class="w-24 h-24 fill-current text-white" />
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 tracking-tight">
                    Welcome to <span class="text-indigo-400">NutriSight</span>
                </h1>
                <p class="text-lg text-slate-200 leading-relaxed">
                    Empowering school-based feeding programs with precision tracking and nutritional intelligence for a healthier future.
                </p>
            </div>
        </div>

        <!-- RIGHT COLUMN: Authentication Form -->
        <!-- 100% width on mobile, 50% on md and above -->
        <div class="w-full md:w-1/2 flex items-center justify-center px-6 py-12 sm:px-12 lg:px-20 bg-white">
            <div class="w-full max-w-md">
                
                <!-- Mobile Header: Only visible when the left column is hidden -->
                <div class="md:hidden mb-10 text-center">
                    <a href="/" class="inline-block">
                        <x-application-logo class="w-20 h-20 fill-current text-indigo-600 mx-auto" />
                    </a>
                    <h2 class="mt-4 text-2xl font-bold text-gray-900">Sign In to NutriSight</h2>
                </div>

                <!-- Desktop Form Header -->
                <div class="mb-10 hidden md:block">
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Sign In</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Please enter your credentials to access your account.
                    </p>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <input id="email" 
                                   name="email" 
                                   type="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   class="block w-full px-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-gray-900 placeholder-gray-400" 
                                   placeholder="name@company.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-semibold text-gray-700">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors" href="{{ route('password.request') }}">
                                    Forgot Password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   required 
                                   autocomplete="current-password" 
                                   class="block w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-gray-900" 
                                   placeholder="••••••••">
                            
                            <button type="button" 
                                     id="togglePassword" 
                                     class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-600 transition-colors focus:outline-none">
                                <!-- Eye Icon (Visible when password is hidden) -->
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.43 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <!-- Eye-Slash Icon (Hidden by default) -->
                                <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.779M6.228 6.228L3 3m3.228 3.228L3 3m.252 15.8s.031-1.612-.234-2.5a5.006 5.006 0 01-.233-2.5M16.5 13.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <line x1="3" y1="3" x2="21" y2="21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" 
                                   name="remember" 
                                   type="checkbox" 
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all" />
                            <label for="remember" class="ms-2 block text-sm text-gray-600">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform active:scale-[0.98]">
                            Sign In
                        </button>
                    </div>
                </form>

                <!-- Footer -->
                <p class="mt-8 text-center text-xs text-gray-500">
                    &copy; {{ date('Y') }} NutriSight School-Based Feeding Program. All rights reserved.
                </p>
            </div>
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
    </script>
</body>
</html>
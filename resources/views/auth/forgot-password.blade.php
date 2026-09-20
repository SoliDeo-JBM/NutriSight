<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'NutriSight') }} - Forgot Password</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="font-sans text-gray-900 antialiased bg-stone-100 min-h-screen flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-md">

        <!-- Brand Logo / Icon Badge -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-orange-50 text-orange-600 shadow-sm border border-orange-100 mb-4 transition hover:scale-105">
                <i class="fas fa-key text-2xl"></i>
            </a>
            <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Forgot your password?</h2>
            <p class="mt-2 text-sm text-gray-600 leading-relaxed max-w-sm mx-auto">
                No problem. Just enter your email address below and we will email you a password reset link.
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Card Container -->
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 sm:p-8">
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-envelope text-sm"></i>
                        </div>
                        <input id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            class="block w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-gray-900 placeholder-gray-400 text-sm"
                            placeholder="name@deped.gov.ph">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-lg shadow-sm text-sm font-bold text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200 transform active:scale-[0.98]">
                        <i class="fas fa-paper-plane text-xs"></i> Email Password Reset Link
                    </button>
                </div>
            </form>
        </div>

        <!-- Back to Sign In Link -->
        <div class="mt-8 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-orange-600 hover:text-orange-500 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Back to Sign In
            </a>
        </div>

        <!-- Footer -->
        <p class="mt-10 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} NutriSight School-Based Feeding Program. All rights reserved.
        </p>
    </div>
</body>

</html>
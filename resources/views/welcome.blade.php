<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="NutriSight helps Marisol Bliss Elementary School manage school-based feeding programs with precise attendance, nutrition, and term progress tracking.">

    <title>{{ config('app.name', 'NutriSight') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="NutriSight home">
                <x-application-logo class="h-9 w-9 fill-current text-indigo-600" />
                <span class="text-xl font-extrabold tracking-tight text-slate-900">NutriSight</span>
            </a>

            <div class="hidden items-center gap-8 md:flex">
                <a href="#features" class="text-sm font-semibold text-slate-600 transition hover:text-indigo-600">Features</a>
                <a href="#about" class="text-sm font-semibold text-slate-600 transition hover:text-indigo-600">About</a>
                <a href="{{ route('login') }}" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Login</a>
            </div>

            <a href="{{ route('login') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 md:hidden">Login</a>
        </nav>
    </header>

    <main>
        <section class="relative overflow-hidden bg-white">
            <div class="grid lg:min-h-[620px] lg:grid-cols-[1.1fr_0.9fr]">
                <div class="relative flex min-h-[420px] items-center overflow-hidden bg-slate-900 px-6 py-16 sm:px-12 lg:px-16">
                    <div class="absolute inset-0 h-full min-h-full w-full min-w-full bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80&w=1200');"></div>
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                    <div class="relative z-10 max-w-lg">
                        <x-application-logo class="mb-8 h-20 w-20 fill-current text-white" />
                        <p class="mb-4 text-sm font-bold uppercase tracking-[0.25em] text-indigo-300">School-based feeding intelligence</p>
                        <p class="text-lg leading-relaxed text-slate-200">Empowering school-based feeding programs with precision tracking and nutritional intelligence for a healthier future.</p>
                    </div>
                </div>

                <div class="flex items-center px-6 py-16 sm:px-12 lg:px-16">
                    <div class="max-w-xl">
                        <p class="mb-4 text-sm font-bold uppercase tracking-[0.25em] text-indigo-600">A clearer view of every learner</p>
                        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                            Welcome to <span class="text-indigo-600">NutriSight</span>
                        </h1>
                        <p class="mt-6 max-w-lg text-lg leading-8 text-slate-600">Bring student nutrition, attendance, and feeding program progress into one focused workspace for Marisol Bliss Elementary School.</p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Get Started</a>
                            <a href="#features" class="inline-flex items-center justify-center rounded-lg border border-indigo-600 px-6 py-3 text-sm font-bold text-indigo-600 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="bg-slate-50 px-6 py-20 sm:px-12 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="max-w-2xl">
                    <p class="text-sm font-bold uppercase tracking-[0.25em] text-indigo-600">Features</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Built for accountable feeding programs</h2>
                    <p class="mt-4 text-lg leading-8 text-slate-600">NutriSight turns everyday school records into clear signals that help teams monitor learners and act at the right time.</p>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-2">
                    <article class="border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 16l3-4 3 2 5-7" />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-xl font-bold text-slate-900">BMI Monitoring &amp; Nutritional Status Tracking</h3>
                        <p class="mt-3 leading-7 text-slate-600">Follow average BMI trends per term and see status distribution across Normal, Wasted, Severely Wasted, Overweight, and Obese classifications.</p>
                    </article>

                    <article class="border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2Z" />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-xl font-bold text-slate-900">Email Parent Notifications</h3>
                        <p class="mt-3 leading-7 text-slate-600">Keep parents informed with timely updates on attendance, health status, and feeding program participation.</p>
                    </article>

                    <article class="border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2h-4M9 5a3 3 0 016 0M8 11h8M8 15h5" />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-xl font-bold text-slate-900">Term Progress Reporting for SBFP Beneficiaries</h3>
                        <p class="mt-3 leading-7 text-slate-600">Compare student height, weight, BMI, and nutritional status across Term 1, Term 2, and Term 3 in one progress report.</p>
                    </article>

                    <article class="border border-slate-200 bg-white p-7 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2m8-10a4 4 0 100-8 4 4 0 000 8Zm6-3a3 3 0 110-6m4 17v-2a4 4 0 00-3-3.87" />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-xl font-bold text-slate-900">Transparency Dashboards</h3>
                        <p class="mt-3 leading-7 text-slate-600">Give Super Admin, Admin, and Encoder accounts the right view for their level of program work, while parents get their own read-only transparency dashboard.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="about" class="bg-slate-900 px-6 py-20 sm:px-12 lg:px-8">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.25em] text-indigo-300">About NutriSight</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Every record supports a healthier future.</h2>
                </div>
                <div class="text-lg leading-8 text-slate-300">
                    <p>NutriSight supports the School-Based Feeding Program (SBFP) at Marisol Bliss Elementary School by bringing student nutrition, attendance, feeding, and assessment records into a shared system for better follow-through.</p>
                    <p class="mt-5">The system supports multiple user roles: Super Admin for full system configuration and account management, Admin for day-to-day SBFP program administration and reporting, and Encoder for student profiling and attendance data entry. Parents and stakeholders get read-only access to a public transparency dashboard. Clear permissions keep accountability visible at every level.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-slate-950 px-6 py-10 text-slate-300 sm:px-12 lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="font-bold text-white">NutriSight</p>
                <p class="mt-1 text-sm text-slate-400">A Capstone Project by Magtoto, Mungcal, Nicdao, and Santos</p>
            </div>
            <nav class="flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold" aria-label="Footer navigation">
                <a href="{{ route('home') }}" class="transition hover:text-white">Home</a>
                <a href="#features" class="transition hover:text-white">Features</a>
                <a href="#about" class="transition hover:text-white">About</a>
                <a href="{{ route('login') }}" class="text-indigo-300 transition hover:text-indigo-200">Login</a>
            </nav>
        </div>
    </footer>
</body>
</html>
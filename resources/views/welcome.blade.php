<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>NutriSight | Nutrition Monitoring & Feeding Transparency</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(59,130,246,0.10),_transparent_35%),linear-gradient(to_bottom,_#f8fafc,_#eef7f3)]">
            <header class="bg-[#0f172a] text-white shadow-lg shadow-slate-900/10">
                <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-base font-black text-[#0f172a] shadow-md shadow-emerald-500/30">
                            N
                        </div>
                        <span class="text-xl font-bold tracking-wide">NutriSight</span>
                    </a>

                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Login
                    </a>
                </nav>
            </header>

            <main>
                <section class="relative overflow-hidden">
                    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">
                        <div>
                            <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Nutrition Monitoring
                            </div>

                            <h1 class="max-w-xl text-4xl font-black tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                                NutriSight
                            </h1>

                            <p class="mt-5 max-w-xl text-lg leading-8 text-slate-600">
                                Proactive nutrition monitoring and feeding program transparency for a healthier school community.
                            </p>

                            <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    Get Started
                                </a>
                                <a href="#features" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-6 py-3.5 text-base font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                                    View Features
                                </a>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="absolute -left-8 top-10 h-28 w-28 rounded-full bg-blue-200/60 blur-3xl"></div>
                            <div class="absolute -right-4 bottom-10 h-32 w-32 rounded-full bg-emerald-200/60 blur-3xl"></div>

                            <div class="relative overflow-hidden rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                                <div class="mb-5 flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">School Nutrition</p>
                                        <h2 class="mt-2 text-2xl font-bold text-slate-900">System Snapshot</h2>
                                    </div>
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Active</span>
                                </div>

                                <div class="space-y-4">
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Monitoring</p>
                                        <p class="mt-3 text-base font-semibold text-slate-900">Student health checks and nutritional tracking for timely support.</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Attendance</p>
                                        <p class="mt-3 text-base font-semibold text-slate-900">QR verification helps maintain accurate school attendance records.</p>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Communication</p>
                                        <p class="mt-3 text-base font-semibold text-slate-900">Parent updates and feeding program transparency in one place.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="features" class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-16">
                    <div class="mb-10 text-center">
                        <p class="text-xs font-bold uppercase tracking-[0.28em] text-blue-600">Features</p>
                        <h2 class="mt-3 text-2xl font-bold tracking-tight text-slate-900">Built for transparent, proactive student care</h2>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 12h3l2-5 4 10 2-5h5" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">Automated BMI Monitoring</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Track student nutrition metrics automatically and identify at-risk learners early with reliable health indicators.</p>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9.5V7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2.5M4 10.5h16v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">QR-Based Attendance Tracking</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Scan and verify student attendance quickly to keep records accurate and reduce manual data entry.</p>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8M8 14h5M7 4h10a2 2 0 0 1 2 2v12l-4-2-4 2-4-2-4 2V6a2 2 0 0 1 2-2Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">Email Parent Notifications</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Keep parents informed with timely updates on attendance, health status, and feeding program participation.</p>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 19V5m0 0h16M4 5l8 8m8-8v14m0-14h-8m8 14H4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">Transparency Dashboard</h3>
                            <p class="mt-3 text-sm leading-6 text-slate-600">Present feeding coverage, school health data, and growth trends clearly for administrators and stakeholders.</p>
                        </div>
                    </div>
                </section>

                <section id="about" class="bg-[#0f172a] py-16 text-white">
                    <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[1.3fr_0.7fr] lg:px-8">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.28em] text-emerald-300">About</p>
                            <h2 class="mt-3 text-2xl font-bold tracking-tight text-slate-900">School-Based Feeding Program transparency that builds trust</h2>
                            <p class="mt-5 max-w-2xl text-base leading-8 text-slate-300">
                                The School-Based Feeding Program (SBFP) is a key initiative that supports learners by improving nutrition, attendance, and overall well-being. At Marisol Bliss Elementary School, NutriSight helps administrators, teachers, and stakeholders monitor student health, record attendance, and share meaningful school nutrition insights with clarity and accountability.
                            </p>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400">School Focus</p>
                            <div class="mt-5 space-y-5">
                                <div>
                                    <p class="text-sm text-slate-400">School</p>
                                    <p class="mt-1 text-xl font-bold text-slate-900">Marisol Bliss Elementary School</p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-400">Purpose</p>
                                    <p class="mt-1 text-xl font-bold text-slate-900">Healthy learning, better outcomes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="bg-[#0f172a] text-white">
                <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 text-sm text-slate-300 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <div>
                        <p class="text-base font-bold text-white">Marisol Bliss Elementary School</p>
                        <p class="mt-1 text-slate-300">A Capstone Project by Magtoto, Mungcal, Nicdao, and Santos</p>
                    </div>

                    <div class="flex items-center gap-5 text-sm font-medium text-slate-600">
                        <a href="{{ route('home') }}" class="transition hover:text-blue-300">Home</a>
                        <a href="#features" class="transition hover:text-blue-300">Features</a>
                        <a href="#about" class="transition hover:text-blue-300">About</a>
                        <a href="{{ route('login') }}" class="transition hover:text-blue-300">Login</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>

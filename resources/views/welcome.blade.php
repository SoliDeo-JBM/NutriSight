<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="NutriSight helps Marisol Bliss Elementary School manage school-based feeding programs with precise attendance, nutrition, and period progress tracking.">

    <title>{{ config('app.name', 'NutriSight') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endif
</head>

<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8" aria-label="Main navigation">
            <a href="#hero" class="flex items-center gap-3" aria-label="NutriSight home">
                <x-application-logo class="h-9 w-9 fill-current text-orange-600" />
                <span class="text-xl font-extrabold tracking-tight text-slate-900">NutriSight</span>
            </a>

            <div class="hidden items-center gap-8 md:flex">
                <a href="#features" class="text-sm font-semibold text-slate-600 transition hover:text-orange-600">Features</a>
                <a href="#about" class="text-sm font-semibold text-slate-600 transition hover:text-orange-600">About</a>
                <a href="{{ route('login') }}" class="rounded-lg bg-orange-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">Login</a>
            </div>

            <a href="{{ route('login') }}" class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 md:hidden">Login</a>
        </nav>
    </header>

    <main>
        <section id="hero" class="relative scroll-mt-20 overflow-hidden bg-white">
            <div class="grid lg:min-h-[100dvh] lg:grid-cols-[1.1fr_0.9fr]">
                <div class="relative flex min-h-[420px] items-center justify-center overflow-hidden bg-slate-50 px-4 py-12 sm:px-8 lg:px-10">
                    <div class="relative z-10 w-full max-w-[650px] [perspective:1400px]">
                        <div class="relative rounded-[1.25rem] border-[7px] border-stone-800 bg-stone-800 p-2 shadow-2xl [transform:rotateX(2deg)]">
                            <div class="overflow-hidden rounded-lg bg-slate-100 ring-1 ring-white/10">
                                <div class="flex h-5 items-center gap-1 bg-stone-950 px-2">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                    <span class="ml-2 h-2 w-28 rounded-full bg-stone-800 sm:w-48"></span>
                                </div>
                                <div class="p-3 sm:p-5" x-data="landingDashboardPreview()">
                                    <div class="mb-3 flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-1">
                                            <button type="button" @click="switchMode('dashboard')" :class="mode === 'dashboard' ? 'bg-orange-600 text-white' : 'bg-white text-slate-600'" class="rounded-md border border-orange-200 px-2 py-1 text-[8px] font-bold shadow-sm transition sm:px-3 sm:text-[10px]">Dashboard</button>
                                            <button type="button" @click="switchMode('assessment')" :class="mode === 'assessment' ? 'bg-orange-600 text-white' : 'bg-white text-slate-600'" class="rounded-md border border-orange-200 px-2 py-1 text-[8px] font-bold shadow-sm transition sm:px-3 sm:text-[10px]">Assessment</button>
                                        </div>
                                        <div x-show="mode === 'dashboard'" class="flex items-center">
                                        <label for="landing-dashboard-period" class="mr-2 text-[8px] font-bold uppercase tracking-wide text-slate-500 sm:text-[10px]">View period</label>
                                        <div class="relative">
                                            <select id="landing-dashboard-period" x-model="period" @change="updateCharts($event.target.value)" style="appearance: none; -webkit-appearance: none; background-image: none;" class="rounded-md border border-indigo-300 bg-indigo-50 py-1 pl-1.5 pr-5 text-[9px] font-bold text-indigo-800 outline-none ring-1 ring-indigo-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 sm:pl-2 sm:pr-6 sm:text-[10px]">
                                            <option value="Baseline">Baseline</option>
                                            <option value="Midline">Midline</option>
                                            <option value="Endline">Endline</option>
                                            </select>
                                            <svg class="pointer-events-none absolute right-1.5 top-1/2 h-2.5 w-2.5 -translate-y-1/2 text-indigo-700 sm:right-2" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06Z" clip-rule="evenodd" /></svg>
                                        </div>
                                    </div>
                                    </div>
                                    <div x-show="mode === 'dashboard'" class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                        <div class="rounded-md border border-slate-200 bg-white p-2 shadow-sm">
                                            <div class="text-[7px] font-bold uppercase tracking-wide text-slate-500 sm:text-[9px]">Total SBFP Students</div>
                                            <div class="mt-1 text-xl font-black text-slate-900 sm:text-3xl" x-text="metrics[period].total"></div>
                                        </div>
                                        <div class="rounded-md border border-slate-200 bg-white p-2 shadow-sm">
                                            <div class="text-[7px] font-bold uppercase tracking-wide text-slate-500 sm:text-[9px]">SBFP Recovery Rate</div>
                                            <div class="mt-1 text-xl font-black text-emerald-600 sm:text-3xl" x-text="metrics[period].recovery + '%'"> </div>
                                            <div class="text-[7px] text-slate-500 sm:text-[9px]" x-text="metrics[period].recovered + ' of ' + metrics[period].total + ' normal'"></div>
                                        </div>
                                        <div class="rounded-md border border-slate-200 bg-white p-2 shadow-sm">
                                            <div class="text-[7px] font-bold uppercase tracking-wide text-slate-500 sm:text-[9px]">Normal Status</div>
                                            <div class="mt-1 text-xl font-black text-green-600 sm:text-3xl" x-text="metrics[period].normal"></div>
                                        </div>
                                        <div class="rounded-md border border-slate-200 bg-white p-2 shadow-sm">
                                            <div class="text-[7px] font-bold uppercase tracking-wide text-slate-500 sm:text-[9px]">Wasted</div>
                                            <div class="mt-1 text-xl font-black text-rose-600 sm:text-3xl" x-text="metrics[period].atRisk"></div>
                                        </div>
                                    </div>
                                    <div class="mt-3 grid gap-3 lg:grid-cols-2">
                                        <div x-show="mode === 'dashboard'" class="rounded-md border border-slate-200 bg-white p-2 shadow-sm sm:p-3">
                                            <h2 class="mb-1 text-[10px] font-bold text-slate-800 sm:text-xs">Average BMI Trend per Period</h2>
                                            <div class="relative h-32 sm:h-40"><canvas id="landingBmiTrendChart"></canvas></div>
                                        </div>
                                        <div x-show="mode === 'dashboard'" class="rounded-md border border-slate-200 bg-white p-2 shadow-sm sm:p-3">
                                            <h2 class="mb-1 text-[10px] font-bold text-slate-800 sm:text-xs">BMI Status Distribution</h2>
                                            <div class="relative h-32 sm:h-40"><canvas id="landingBmiDistributionChart"></canvas></div>
                                        </div>
                                    </div>
                                    <div x-show="mode === 'assessment'" class="space-y-3">
                                        <div class="flex items-center justify-between rounded-md border border-indigo-200 bg-indigo-50 px-3 py-2">
                                            <span class="text-[8px] font-bold uppercase tracking-wide text-slate-500 sm:text-[10px]">School year</span>
                                            <select disabled class="rounded border border-indigo-200 bg-white py-0.5 pl-1 text-[9px] font-black text-indigo-800 outline-none sm:text-[11px]"><option x-text="assessment.schoolYear + ' (Active)' "></option></select>
                                        </div>
                                        <div class="grid gap-3 lg:grid-cols-2">
                                            <div class="rounded-md border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div><h2 class="text-[10px] font-bold text-slate-900 sm:text-xs">Student Attendance Summary</h2><p class="mt-1 text-[8px] text-slate-500 sm:text-[9px]">Students with recorded SBFP attendance</p></div>
                                                    <div class="text-right"><div class="text-lg font-black text-emerald-600 sm:text-2xl" x-text="assessment.completeAttendanceRate + '%' "></div><div class="text-[8px] text-slate-500">complete</div></div>
                                                </div>
                                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-rose-100"><div class="h-full bg-emerald-500" :style="'width: ' + assessment.completeAttendanceRate + '%' "></div></div>
                                                <div class="mt-3 grid grid-cols-2 gap-2"><div class="border-l-2 border-emerald-500 pl-2"><div class="text-lg font-black text-slate-900 sm:text-xl" x-text="assessment.completeAttendance"></div><div class="text-[8px] font-semibold uppercase text-slate-500">Complete attendance</div><div class="text-[9px] text-emerald-700" x-text="assessment.completeAttendanceRate + '%' "></div></div><div class="border-l-2 border-rose-500 pl-2"><div class="text-lg font-black text-slate-900 sm:text-xl" x-text="assessment.withAbsences"></div><div class="text-[8px] font-semibold uppercase text-slate-500">With absences</div><div class="text-[9px] text-rose-700" x-text="assessment.withAbsencesRate + '%' "></div></div></div>
                                            </div>
                                            <div class="rounded-md border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div><h2 class="text-[10px] font-bold text-slate-900 sm:text-xs">Endline Nutritional Assessment</h2><p class="mt-1 text-[8px] text-slate-500 sm:text-[9px]">Baseline at-risk cohort assessed against endline reports</p></div>
                                                    <div class="text-right"><div class="text-lg font-black text-emerald-600 sm:text-2xl" x-text="assessment.recoveredRate + '%' "></div><div class="text-[8px] text-slate-500">recovered at endline</div></div>
                                                </div>
                                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-amber-100"><div class="h-full bg-emerald-500" :style="'width: ' + assessment.recoveredRate + '%' "></div></div>
                                                <div class="mt-3 grid grid-cols-2 gap-2"><div class="border-l-2 border-emerald-500 pl-2"><div class="text-lg font-black text-slate-900 sm:text-xl" x-text="assessment.recovered"></div><div class="text-[8px] font-semibold uppercase text-slate-500">Recovered to Normal</div><div class="text-[9px] text-emerald-700" x-text="assessment.recoveredRate + '%' "></div></div><div class="border-l-2 border-amber-500 pl-2"><div class="text-lg font-black text-slate-900 sm:text-xl" x-text="assessment.stillNeedingSupport"></div><div class="text-[8px] font-semibold uppercase text-slate-500">Still needing support</div><div class="text-[9px] text-amber-700" x-text="assessment.stillNeedingSupportRate + '%' "></div></div></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="mt-4 text-center text-xs font-medium text-slate-500 sm:text-sm">Interact with the preview. Data reflects the active school year.</p>
                    </div>
                </div>

                <div class="relative flex min-h-[100dvh] items-center bg-cover bg-center px-6 py-16 sm:px-12 lg:px-16" style="background-image: url('{{ asset('images/id/marisol-bliss-school.jpg') }}');">
                    <div class="absolute inset-0 bg-stone-900/70 backdrop-blur-sm"></div>
                    <div class="relative z-10 max-w-xl">
                        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
                            Feeding Program, <span class="text-orange-400">Programmed</span>
                        </h1>
                        <p class="mt-6 max-w-lg text-xl leading-8 text-slate-200 sm:text-2xl">One focused School Based Feeding Program workspace for <strong class="font-extrabold text-white">Marisol Bliss Elementary School</strong>.</p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg bg-orange-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">Get Started</a>
                            <a href="#features" class="inline-flex items-center justify-center rounded-lg border border-orange-400 px-6 py-3 text-sm font-bold text-orange-300 transition hover:bg-orange-400/10 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            function landingDashboardPreview() {
                const metrics = @json($landingPreviewMetrics);
                const assessment = @json($landingAssessmentMetrics);

                return {
                    mode: 'dashboard',
                    period: 'Baseline',
                    metrics,
                    assessment,
                    init() {
                        this.$nextTick(() => {
                            this.renderCharts();
                        });
                    },
                    switchMode(mode) {
                        this.mode = mode;
                        if (mode === 'dashboard') {
                            this.$nextTick(() => this.renderCharts());
                        } else {
                            this.trendChart?.destroy();
                            this.distributionChart?.destroy();
                        }
                    },
                    updateCharts(selectedPeriod = this.period) {
                        this.period = selectedPeriod;
                        this.$nextTick(() => this.renderCharts());
                    },
                    renderCharts() {
                        this.trendChart?.destroy();
                        this.distributionChart?.destroy();
                        const selectedMetrics = this.metrics[this.period];
                        this.trendChart = new Chart(document.getElementById('landingBmiTrendChart'), {
                            type: 'line',
                            data: {
                                labels: ['Baseline', 'Midline', 'Endline'],
                                datasets: [{ label: 'Average BMI', data: [...selectedMetrics.trend], borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, .12)', fill: true, tension: .3, pointRadius: 3, pointBackgroundColor: '#10b981' }]
                            },
                            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { font: { size: 8 } } }, y: { ticks: { font: { size: 8 }, maxTicksLimit: 4 } } } }
                        });
                        this.distributionChart = new Chart(document.getElementById('landingBmiDistributionChart'), {
                            type: 'doughnut',
                            data: { labels: ['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese'], datasets: [{ data: [...selectedMetrics.distribution], backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1', '#a855f7'], borderWidth: 1 }] },
                            options: { responsive: true, maintainAspectRatio: false, cutout: '58%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 7, padding: 5, font: { size: 8 } } } } }
                        });
                    }
                };
            }
        </script>

        <section id="features" class="bg-slate-50 px-6 py-20 sm:px-12 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="max-w-2xl">
                    <p class="text-sm font-bold uppercase tracking-[0.25em] text-orange-600">Features</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Everything your feeding program needs to stay on track</h2>
                </div>

                <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-5">
                    <article class="border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19a4 4 0 10-6 0M12 13a4 4 0 100-8 4 4 0 000 8Zm7 6a3 3 0 10-4.5-2.6M17 13a3 3 0 100-6" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900">Learner Records</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Maintain searchable student profiles, enrollments, sections, and guardian details.</p>
                    </article>

                    <article class="border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5-1v8a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2h3l1-2h4l1 2h3a2 2 0 012 2Z" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900">SBFP Enrollment</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Identify beneficiaries, record parent consent, and follow program eligibility.</p>
                    </article>

                    <article class="border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 16l3-4 3 2 5-7" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900">Nutrition Monitoring</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Track height, weight, BMI, and WHO-based nutritional status over time.</p>
                    </article>

                    <article class="border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h5v5H4V4Zm11 0h5v5h-5V4ZM4 15h5v5H4v-5Zm11 2h2m2 0h1m-5-2h5v5h-5v-5ZM11 4h2m-2 4h2m-2 4h2m-2 4h2" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900">QR Attendance Scanning</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Scan each learner's SBFP ID to record verified daily feeding attendance.</p>
                    </article>

                    <article class="border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M5 10v9m4-9v9m6-9v9m4-9v9M3 21h18M4 7h16L12 3 4 7Z" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900">Feeding Plans</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Plan meals and keep feeding program activities aligned with daily records.</p>
                    </article>

                    <article class="hidden border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md md:block">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2Z" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900">Parent Email Notices</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Automatically notify guardians when a learner attends feeding day.</p>
                    </article>

                    <article class="hidden border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md md:block">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H5a2 2 0 00-2 2v12a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2h-4M9 5a3 3 0 016 0M8 11h8M8 15h5" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900">Period Progress</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Compare Baseline, Midline, and Endline measurements for every beneficiary.</p>
                    </article>

                    <article class="hidden border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md md:block">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 016.5 17H20M4 19.5A2.5 2.5 0 016.5 22H20V2H6.5A2.5 2.5 0 004 4.5v15Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8M8 11h6" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900">Reports &amp; Exports</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Generate attendance, assessment, and consolidated reports in common formats.</p>
                    </article>

                    <article class="hidden border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md md:block">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5m0 0h15l-3 4 3 4H4m0 6h16" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900">Program Dashboards</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">See feeding, nutrition, attendance, and recovery signals in one workspace.</p>
                    </article>

                    <article class="hidden border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-md md:block">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5M20 12a8 8 0 11-16 0 8 8 0 0116 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 4V2m0 20v-2m8-8h2M2 12h2" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-900">School-Year Control</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Switch active school years while keeping program records organized.</p>
                    </article>

                </div>
            </div>
        </section>

        <section id="about" class="bg-slate-50 px-6 py-20 sm:px-12 lg:px-8">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.25em] text-orange-600">About NutriSight</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Every record supports a healthier future.</h2>
                </div>
                <div class="text-lg leading-8 text-slate-600">
                    <p>NutriSight supports the School-Based Feeding Program (SBFP) at Marisol Bliss Elementary School by bringing student nutrition, attendance, feeding, and assessment records into a shared system for better follow-through.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-slate-50 px-6 py-10 text-slate-600 sm:px-12 lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm">&copy; 2026 NutriSight School-Based Feeding Program. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

</html>
@extends('layouts.dashboard')

@section('content')
    <div class="flex flex-col gap-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">SBFP Reports</h1>
                <p class="text-sm text-gray-500 mt-1">Select a reporting module to inspect program attendance, annual consolidations, and nutritional impact.</p>
            </div>
        </div>

        <!-- 3-Option Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- 1. Attendance Report Card -->
            <a href="{{ route('admin.reports.sbfp.attendance') }}" 
               class="group bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:border-blue-500 hover:shadow-md transition-all duration-200">
                <div>
                    <!-- Icon & Tag -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <!-- Calendar & Check SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5m-9-3.75h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm-3.75-2.25H8.25v.008H8.25v-.008ZM8.25 15h.008v.008H8.25V15Zm7.5-2.25h.008v.008H15.75v-.008Zm0 2.25h.008v.008H15.75V15Z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700">Feeding Cycle</span>
                    </div>

                    <!-- Title & Description -->
                    <h2 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                        1. Attendance Report
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Track daily feeding attendance logs, verify meal distribution, and review feeding cycle completion metrics across sections.
                    </p>
                </div>

                <!-- Footer Action -->
                <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-between text-sm font-semibold text-blue-600 group-hover:text-blue-700">
                    <span>View Attendance</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transform group-hover:translate-x-1 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </div>
            </a>

            <!-- 2. Yearly Summary Report Card -->
            <a href="{{ route('admin.reports.sbfp.annual') }}" 
               class="group bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:border-emerald-500 hover:shadow-md transition-all duration-200">
                <div>
                    <!-- Icon & Tag -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <!-- Document Table SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700">DepEd Format</span>
                    </div>

                    <!-- Title & Description -->
                    <h2 class="text-lg font-bold text-gray-900 group-hover:text-emerald-600 transition-colors">
                        2. Annual Consolidated Report
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        School-wide annual summary formatted for standard DepEd compliance. Consolidates beneficiary demographics and baseline-to-endline progress.
                    </p>
                </div>

                <!-- Footer Action -->
                <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-between text-sm font-semibold text-emerald-600 group-hover:text-emerald-700">
                    <span>View Summary</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transform group-hover:translate-x-1 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </div>
            </a>

            <!-- 3. SBFP Assessment Report Card -->
            <a href="{{ route('admin.reports.sbfp.assessment') }}" 
               class="group bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between hover:border-violet-500 hover:shadow-md transition-all duration-200">
                <div>
                    <!-- Icon & Tag -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center group-hover:bg-violet-600 group-hover:text-white transition-colors">
                            <!-- Analytical Chart SVG -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-violet-50 text-violet-700">Program Impact</span>
                    </div>

                    <!-- Title & Description -->
                    <h2 class="text-lg font-bold text-gray-900 group-hover:text-violet-600 transition-colors">
                        3. SBFP Assessment
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Evaluate program efficacy. Compare baseline vs. endline BMI to verify beneficiaries rehabilitated to Normal status versus those needing sustained intervention.
                    </p>
                </div>

                <!-- Footer Action -->
                <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-between text-sm font-semibold text-violet-600 group-hover:text-violet-700">
                    <span>View Assessment</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transform group-hover:translate-x-1 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </div>
            </a>

        </div>
    </div>
@endsection
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSight Dashboard</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/dashboard-layout.css') }}">
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @if(View::exists('css/' . Auth::user()->role . '-dashboard.css'))
    <link rel="stylesheet" href="{{ asset('css/' . Auth::user()->role . '-dashboard.css') }}">
    @endif
</head>

<body>
    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside id="appSidebar" class="sidebar">
        <div class="sidebar-brand">
            <span>NutriSight</span>
            <button type="button" class="sidebar-close-btn" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-title">Menu</div>
            <a href="{{ route(Auth::user()->dashboardRoute()) }}" class="nav-link {{ request()->routeIs(Auth::user()->dashboardRoute()) ? 'active' : '' }}" onclick="toggleSidebar()">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            @if(Auth::user()->role === 'super_admin')
            <!-- Student Management Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('super-admin.students.*', 'super-admin.attendance.*') ? 'true' : 'false' }} }" x-cloak class="my-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-stone-800 hover:text-white transition">
                    <span class="flex items-center gap-3"><i class="fas fa-users w-5 text-center"></i> Student Management</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-stone-950/50 py-1 space-y-0.5">
                    <a href="{{ route('super-admin.students.index') }}" class="flex items-center gap-3 {{ request()->routeIs('super-admin.students.index') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Complete Student List</a>
                    <a href="{{ route('super-admin.students.sbfp') }}" class="flex items-center gap-3 {{ request()->routeIs('super-admin.students.sbfp') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Complete SBFP List</a>
                    <a href="{{ route('super-admin.attendance.index') }}" class="flex items-center gap-3 {{ request()->routeIs('super-admin.attendance.*') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Complete Attendance List</a>
                </div>
            </div>

            <!-- System Administration Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('super-admin.school-years.*', 'super-admin.accounts.*', 'super-admin.audit-logs.*', 'super-admin.school-logo.*') ? 'true' : 'false' }} }" x-cloak class="my-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-stone-800 hover:text-white transition">
                    <span class="flex items-center gap-3"><i class="fas fa-cogs w-5 text-center"></i> Administration</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-stone-950/50 py-1 space-y-0.5">
                    <a href="{{ route('super-admin.school-years.index') }}" class="flex items-center gap-3 {{ request()->routeIs('super-admin.school-years.*') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">School Years</a>
                    <a href="{{ route('super-admin.accounts.index') }}" class="flex items-center gap-3 {{ request()->routeIs('super-admin.accounts.*') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Admin Accounts</a>
                    <a href="{{ route('super-admin.audit-logs.index') }}" class="flex items-center gap-3 {{ request()->routeIs('super-admin.audit-logs.*') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Audit Logs</a>
                    <a href="{{ route('super-admin.school-logo.edit') }}" class="flex items-center gap-3 {{ request()->routeIs('super-admin.school-logo.*') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Report Logo</a>
                </div>
            </div>

            <a href="{{ route('super-admin.settings') }}" class="nav-link {{ request()->routeIs('super-admin.settings') ? 'active' : '' }}" onclick="toggleSidebar()">
                <i class="fas fa-user-cog"></i> Account Settings
            </a>
            @endif

            @if(Auth::user()->role === 'encoder')
            <!-- Student Management Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('encoder.students.*') ? 'true' : 'false' }} }" x-cloak class="my-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-stone-800 hover:text-white transition">
                    <span class="flex items-center gap-3"><i class="fas fa-users w-5 text-center"></i> Student Records</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-stone-950/50 py-1 space-y-0.5">
                    <a href="{{ route('encoder.students.index') }}" class="flex items-center gap-3 {{ request()->routeIs('encoder.students.index') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Advisory Student List</a>
                    <a href="{{ route('encoder.students.sbfp') }}" class="flex items-center gap-3 {{ request()->routeIs('encoder.students.sbfp') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Advisory SBFP List</a>
                </div>
            </div>

            <a href="{{ route('encoder.attendance.index') }}" class="nav-link {{ request()->routeIs('encoder.attendance.*') ? 'active' : '' }}" onclick="toggleSidebar()">
                <i class="fas fa-calendar-check"></i> Attendance List
            </a>
            <a href="{{ route('encoder.settings') }}" class="nav-link {{ request()->routeIs('encoder.settings') ? 'active' : '' }}" onclick="toggleSidebar()">
                <i class="fas fa-user-cog"></i> Account Settings
            </a>
            @endif

            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.meal-plans.index') }}" class="nav-link {{ request()->routeIs('admin.meal-plans.*') ? 'active' : '' }}" onclick="toggleSidebar()">
                <i class="fas fa-utensils"></i> Meal Plan
            </a>
            <!-- Student Management Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('admin.students.*', 'admin.attendance.*') ? 'true' : 'false' }} }" x-cloak class="my-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-stone-800 hover:text-white transition">
                    <span class="flex items-center gap-3"><i class="fas fa-users w-5 text-center"></i> Student Management</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-stone-950/50 py-1 space-y-0.5">
                    <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 {{ request()->routeIs('admin.students.index') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Complete Student List</a>
                    <a href="{{ route('admin.students.sbfp') }}" class="flex items-center gap-3 {{ request()->routeIs('admin.students.sbfp') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Complete SBFP List</a>
                    <a href="{{ route('admin.attendance.index') }}" class="flex items-center gap-3 {{ request()->routeIs('admin.attendance.*') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Complete Attendance List</a>
                </div>
            </div>

            <!-- Management & Reports Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('admin.accounts.*', 'admin.reports.*', 'admin.audit-logs.*') ? 'true' : 'false' }} }" x-cloak class="my-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-stone-800 hover:text-white transition">
                    <span class="flex items-center gap-3"><i class="fas fa-folder-open w-5 text-center"></i> Management & Reports</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-stone-950/50 py-1 space-y-0.5">
                    <a href="{{ route('admin.accounts.index') }}" class="flex items-center gap-3 {{ request()->routeIs('admin.accounts.*') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Encoder Accounts</a>
                    <a href="{{ route('admin.reports.sbfp.index') }}" class="flex items-center gap-3 {{ request()->routeIs('admin.reports.sbfp.*') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">SBFP Reports</a>
                    <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 {{ request()->routeIs('admin.audit-logs.*') ? 'pl-10 pr-4 py-2 text-xs text-white bg-stone-800 border-l-2 border-orange-500 font-medium' : 'pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-stone-800' }} transition" onclick="toggleSidebar()">Audit Logs</a>
                </div>
            </div>

            <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" onclick="toggleSidebar()">
                <i class="fas fa-user-cog"></i> Account Settings
            </a>
            @endif
        </nav>
        <div class="sidebar-footer">
            <button type="button" onclick="openLogoutModal()" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="top-header flex justify-between items-center px-6">
            <div class="header-left flex items-center gap-4">
                <button type="button" class="hamburger-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="welcome-text">Welcome, {{ Auth::user()->name }}</div>
            </div>
            <div class="header-right flex items-center gap-3">
                <button type="button" x-data @click="$dispatch('open-modal', 'attendance-scanner')" class="text-xs bg-blue-600 text-white rounded px-3 py-1.5 font-semibold hover:bg-blue-700 transition">
                    <i class="fas fa-qrcode mr-1"></i> Scan Attendance
                </button>
                <form method="POST" action="{{ route('school-years.switch') }}" class="school-year-picker">
                    @csrf
                    <label for="global-school-year" class="school-year-label"><i class="fas fa-calendar-alt" aria-hidden="true"></i><span>School Year</span></label>
                    <select id="global-school-year" name="school_year_id" onchange="this.form.submit()" class="school-year-select" aria-label="Select school year">
                        @php
                        $allSy = \App\Services\SchoolYearManager::allSchoolYears();
                        $activeSyId = \App\Services\SchoolYearManager::activeSchoolYearId();
                        @endphp
                        @foreach($allSy as $sy)
                        <option value="{{ $sy->id }}" {{ $activeSyId == $sy->id ? 'selected' : '' }}>
                            {{ $sy->year }} {{ $sy->is_active ? '(Active)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </header>

        <div class="content-body">
            @if(session('success'))
            <div id="success-alert" class="success-alert">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
            <script>
                setTimeout(() => {
                    const alert = document.getElementById('success-alert');
                    if (alert) {
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 200);
                    }
                }, 1500);
            </script>
            @endif

            @yield('content')
        </div>
    </main>

    <x-attendance-scanner-modal />

    <div id="mealRequiredModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50 {{ session('error') ? '' : 'hidden' }}" role="alertdialog" aria-modal="true" aria-labelledby="mealRequiredTitle">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full shadow-xl text-center mx-4">
            <div class="text-amber-500 text-4xl mb-4"><i class="fas fa-utensils"></i></div>
            <h3 id="mealRequiredTitle" class="text-lg font-bold text-gray-900 mb-2">Meal Required</h3>
            <p class="text-sm text-gray-600 mb-6">{{ session('error', 'Add meal first before taking attendance.') }}</p>
            <button type="button" onclick="closeMealRequiredModal()" class="px-5 py-2 bg-blue-600 text-white rounded text-sm font-semibold hover:bg-blue-700">OK</button>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full shadow-xl text-center mx-4">
            <div class="text-red-500 text-4xl mb-4">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Logout</h3>
            <p class="text-sm text-gray-600 mb-6">Are you sure you want to log out of NutriSight?</p>
            <div class="flex justify-center gap-4">
                <button type="button" onclick="closeLogoutModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded text-sm font-semibold hover:bg-gray-300">
                    Cancel
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded text-sm font-semibold hover:bg-red-700">
                        Yes, Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="global-loading-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50" role="status" aria-live="polite" aria-label="Loading">
        <div class="mx-4 w-full max-w-sm rounded-lg bg-white px-8 py-6 text-center shadow-xl">
            <i class="fas fa-spinner fa-spin mb-3 text-3xl text-blue-600" aria-hidden="true"></i>
            <p id="global-loading-message" class="text-sm font-semibold text-gray-700">Processing, please wait...</p>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.toggle('active');
            backdrop.classList.toggle('active');
        }

        function openLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }

        function openMealRequiredModal() {
            document.getElementById('mealRequiredModal').classList.remove('hidden');
        }

        function closeMealRequiredModal() {
            document.getElementById('mealRequiredModal').classList.add('hidden');
        }

        function showLoadingModal(message = 'Processing, please wait...') {
            const modal = document.getElementById('global-loading-modal');
            const label = document.getElementById('global-loading-message');

            if (!modal) return;

            label.textContent = message;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function hideLoadingModal() {
            const modal = document.getElementById('global-loading-modal');

            if (!modal) return;

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('click', (event) => {
            const link = event.target.closest('[data-loading-link], a[href*="/excel"], a[href*="/docx"], a[href*="/pdf"], a[href*="/sql"]');

            if (!link) return;

            showLoadingModal(link.dataset.loadingMessage || 'Preparing download, please wait...');
            window.setTimeout(hideLoadingModal, 1500);
        });

        document.addEventListener('submit', (event) => {
            const form = event.target;

            if (form.matches('[data-loading-form]')) {
                showLoadingModal(form.dataset.loadingMessage || 'Processing, please wait...');
            }
        });

        window.addEventListener('pageshow', hideLoadingModal);

        window.addEventListener('meal-required', openMealRequiredModal);
    </script>
</body>

</html>
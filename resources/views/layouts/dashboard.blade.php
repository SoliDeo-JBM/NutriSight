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
            <a href="{{ route(Auth::user()->dashboardRoute()) }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            @if(Auth::user()->role === 'super_admin')
            <!-- Student Management Dropdown -->
            <div x-data="{ open: true }" class="my-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <span class="flex items-center gap-3"><i class="fas fa-users w-5 text-center"></i> Student Management</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-950/50 py-1 space-y-0.5">
                    <a href="{{ route('super-admin.students.index') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Complete Student List</a>
                    <a href="{{ route('super-admin.students.sbfp') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Complete SBFP List</a>
                    <a href="{{ route('super-admin.students.promote') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Student Promotion</a>
                </div>
            </div>

            <!-- System Administration Dropdown -->
            <div x-data="{ open: true }" class="my-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <span class="flex items-center gap-3"><i class="fas fa-cogs w-5 text-center"></i> Administration</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-950/50 py-1 space-y-0.5">
                    <a href="{{ route('super-admin.sections.index') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Sections & Advisers</a>
                    <a href="{{ route('super-admin.school-years.index') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">School Years</a>
                    <a href="{{ route('super-admin.accounts.index') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Admin Accounts</a>
                    <a href="{{ route('super-admin.audit-logs.index') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Audit Logs</a>
                </div>
            </div>

            <a href="{{ route('super-admin.settings') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-user-cog"></i> Account Settings
            </a>
            @endif

            @if(Auth::user()->role === 'encoder')
            <!-- Student Management Dropdown -->
            <div x-data="{ open: true }" class="my-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <span class="flex items-center gap-3"><i class="fas fa-users w-5 text-center"></i> Student Records</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-950/50 py-1 space-y-0.5">
                    <a href="{{ route('encoder.students.index') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Advisory Student List</a>
                    <a href="{{ route('encoder.students.create') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Add Advisory Student</a>
                    <a href="{{ route('encoder.students.sbfp') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Advisory SBFP List</a>
                </div>
            </div>

            <a href="{{ route('encoder.attendance.index') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-calendar-check"></i> Attendance List
            </a>
            <a href="{{ route('encoder.settings') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-user-cog"></i> Account Settings
            </a>
            @endif

            @if(Auth::user()->role === 'admin')
            <!-- Student Management Dropdown -->
            <div x-data="{ open: true }" class="my-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <span class="flex items-center gap-3"><i class="fas fa-users w-5 text-center"></i> Student Management</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-950/50 py-1 space-y-0.5">
                    <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Complete Student List</a>
                    <a href="{{ route('admin.students.sbfp') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Complete SBFP List</a>
                </div>
            </div>

            <!-- Management & Reports Dropdown -->
            <div x-data="{ open: true }" class="my-1">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <span class="flex items-center gap-3"><i class="fas fa-folder-open w-5 text-center"></i> Management & Reports</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-950/50 py-1 space-y-0.5">
                    <a href="{{ route('admin.sections.index') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Sections & Advisers</a>
                    <a href="{{ route('admin.accounts.index') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Encoder Accounts</a>
                    <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">SBFP Reports</a>
                    <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 pl-11 pr-4 py-2 text-xs text-slate-400 hover:text-white hover:bg-slate-800 transition" onclick="toggleSidebar()">Audit Logs</a>
                </div>
            </div>

            <a href="{{ route('admin.settings') }}" class="nav-link" onclick="toggleSidebar()">
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
                <button type="button" x-data @click="$dispatch('open-modal', 'attendance-scanner')" class="text-xs bg-indigo-600 text-white rounded px-3 py-1.5 font-semibold hover:bg-indigo-700 transition">
                    <i class="fas fa-qrcode mr-1"></i> Scan Attendance
                </button>
                <form method="POST" action="{{ route('school-years.switch') }}" class="flex items-center gap-2">
                    @csrf
                    <span class="text-xs font-semibold text-gray-600 hidden sm:inline"><i class="fas fa-calendar-alt mr-1"></i> School Year:</span>
                    <select name="school_year_id" onchange="this.form.submit()" class="border border-gray-300 rounded px-2.5 py-1 text-xs font-medium text-gray-700 bg-white focus:outline-none focus:border-blue-500">
                        @php
                            $allSy = \App\Services\SchoolYearManager::allSchoolYears();
                            $activeSyId = \App\Services\SchoolYearManager::activeSchoolYearId();
                        @endphp
                        @foreach($allSy as $sy)
                                <option value="{{ $sy->id }}" {{ $activeSyId == $sy->id ? 'selected' : '' }}>
                                    {{ $sy->school_year }} {{ $sy->is_active ? '(Active)' : '' }}
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
                            setTimeout(() => alert.remove(), 500);
                        }
                    }, 3500);
                </script>
            @endif

            @yield('content')
        </div>
    </main>

    <x-attendance-scanner-modal />

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
    </script>
</body>
</html>

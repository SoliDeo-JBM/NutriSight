<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSight Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <a href="{{ route('super-admin.students.index') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-users"></i> Complete Student List
            </a>
            <a href="{{ route('super-admin.students.sbfp') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-clipboard-list"></i> Complete SBFP List
            </a>
            <a href="{{ route('super-admin.students.promote') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-user-graduate"></i> Student Promotion
            </a>
            <a href="{{ route('super-admin.sections.index') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-chalkboard-teacher"></i> Sections & Advisers
            </a>
            <a href="{{ route('super-admin.school-years.index') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-calendar-alt"></i> School Years
            </a>
            <a href="{{ route('super-admin.accounts.index') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-users-cog"></i> Admin Accounts
            </a>
            <a href="{{ route('super-admin.audit-logs.index') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-history"></i> Audit Logs
            </a>
            <a href="{{ route('super-admin.settings') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-user-cog"></i> Account Settings
            </a>
            @endif
            @if(Auth::user()->role === 'encoder')
            <a href="{{ route('encoder.students.index') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-users"></i> Advisory Student List
            </a>
            <a href="{{ route('encoder.students.create') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-user-plus"></i> Add Advisory Student
            </a>
            <a href="{{ route('encoder.students.sbfp') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-clipboard-list"></i> Advisory SBFP List
            </a>
            <a href="{{ route('encoder.attendance.index') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-calendar-check"></i> Attendance List
            </a>
            <a href="{{ route('encoder.settings') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-user-cog"></i> Account Settings
            </a>
            @endif
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.students.index') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-users"></i> Complete Student List
            </a>
            <a href="{{ route('admin.students.sbfp') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-clipboard-list"></i> Complete SBFP List
            </a>
            <a href="{{ route('admin.sections.index') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-chalkboard-teacher"></i> Sections & Advisers
            </a>
            <a href="{{ route('admin.accounts.index') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-users-cog"></i> Encoder Accounts
            </a>
            <a href="{{ route('admin.reports') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-chart-line"></i> SBFP Reports
            </a>
            <a href="{{ route('admin.audit-logs.index') }}" class="nav-link" onclick="toggleSidebar()">
                <i class="fas fa-history"></i> Audit Logs
            </a>
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

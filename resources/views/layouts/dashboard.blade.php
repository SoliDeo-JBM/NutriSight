<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSight Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col">
        <div class="p-6 text-2xl font-bold border-b border-slate-800">NutriSight</div>
        <nav class="flex-1 overflow-y-auto">
            <div class="p-4 text-xs font-semibold text-slate-500 uppercase">Menu</div>
            <a href="{{ route(Auth::user()->dashboardRoute()) }}" class="block py-2.5 px-4 hover:bg-slate-800">
                <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
            </a>
            @if(Auth::user()->role === 'encoder')
            <a href="{{ route('students.index') }}" class="block py-2.5 px-4 hover:bg-slate-800">
                <i class="fas fa-users mr-3"></i> Advisory Student Lists
            </a>
            <a href="{{ route('students.create') }}" class="block py-2.5 px-4 hover:bg-slate-800">
                <i class="fas fa-user-plus mr-3"></i> Add Advisory Student
            </a>
            <a href="{{ route('students.sbfp') }}" class="block py-2.5 px-4 hover:bg-slate-800">
                <i class="fas fa-clipboard-list mr-3"></i> Advisory SBFP Lists
            </a>
            <a href="{{ route('attendance.index') }}" class="block py-2.5 px-4 hover:bg-slate-800">
                <i class="fas fa-calendar-check mr-3"></i> Attendance Lists
            </a>
            @endif
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.reports') }}" class="block py-2.5 px-4 hover:bg-slate-800">
                <i class="fas fa-chart-line mr-3"></i> SBFP Reports
            </a>
            @endif
        </nav>
        <div class="p-4 border-t border-slate-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left py-2 px-4 hover:bg-slate-800 text-red-400">
                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
            <div class="text-gray-500">Welcome, {{ Auth::user()->name }}</div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 relative">
            @if(session('success'))
                <div id="success-alert" class="absolute top-4 right-6 z-50 bg-emerald-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 transition-opacity duration-500">
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
</body>
</html>

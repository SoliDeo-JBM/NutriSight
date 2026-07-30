@extends('layouts.dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Super Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg font-semibold mb-4">System Administration</h2>
            <p class="text-gray-600 mb-4">Manage user accounts and system configuration.</p>
        </div>
    </div>
@endsection

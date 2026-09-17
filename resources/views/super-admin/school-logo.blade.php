@extends('layouts.dashboard')

@section('content')
<div class="mx-auto flex max-w-3xl flex-col gap-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">School Logo</h1>
        <p class="mt-1 text-sm text-gray-500">Upload the school logo displayed in downloadable SBFP reports.</p>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-6 flex min-h-40 items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 p-6">
            <img src="{{ $schoolLogoUrl }}" alt="Current school logo" class="max-h-32 max-w-full object-contain">
        </div>

        <form method="POST" action="{{ route('super-admin.school-logo.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="school_logo" class="mb-2 block text-sm font-semibold text-gray-700">New school logo</label>
                <input id="school_logo" name="school_logo" type="file" accept="image/jpeg,image/png,image/webp" required class="block w-full rounded-lg border border-gray-300 p-2 text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700">
                <p class="mt-1 text-xs text-gray-500">JPG, PNG, or WEBP. Maximum file size: 2 MB.</p>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                <i class="fas fa-upload"></i> Upload and use logo
            </button>
        </form>
    </section>
</div>
@endsection

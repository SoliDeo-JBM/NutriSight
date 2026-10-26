@extends('layouts.dashboard')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Preview Excel Import</h1>
            <p class="mt-1 text-sm text-gray-500">
                Review the learners below before adding them to the {{ $isEncoder ? 'advisory ' : '' }}list.
            </p>
        </div>
        <a href="{{ route($backRoute) }}" class="rounded border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Cancel</a>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm text-gray-600">
                <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                    <tr>
                        <th class="border px-4 py-3">No.</th>
                        <th class="border px-4 py-3">Generated LRN</th>
                        <th class="border px-4 py-3">Learner Name</th>
                        <th class="border px-4 py-3">Grade</th>
                        <th class="border px-4 py-3">Section</th>
                        <th class="border px-4 py-3">Birthdate</th>
                        <th class="border px-4 py-3">Sex</th>
                        <th class="border px-4 py-3">Weight (kg)</th>
                        <th class="border px-4 py-3">Height (cm)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($rows as $row)
                    <tr>
                        <td class="border px-4 py-3">{{ $row['display_number'] }}</td>
                        <td class="border px-4 py-3 font-medium">{{ $row['generated_lrn'] }}</td>
                        <td class="border px-4 py-3">{{ $row['last_name'] }}, {{ $row['first_name'] }} {{ $row['middle_name'] }}</td>
                        <td class="border px-4 py-3">{{ $row['grade'] === 0 ? 'Kinder' : 'Grade ' . $row['grade'] }}</td>
                        <td class="border px-4 py-3">{{ $row['section'] }}</td>
                        <td class="border px-4 py-3">{{ $row['birth_date'] }}</td>
                        <td class="border px-4 py-3">{{ $row['sex'] }}</td>
                        <td class="border px-4 py-3">{{ $row['weight'] }}</td>
                        <td class="border px-4 py-3">{{ $row['height'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-4 py-4">
            <p class="text-sm text-gray-600">{{ count($rows) }} learner(s) will be added.</p>
            <form method="POST" action="{{ route($confirmRoute) }}">
                @csrf
                <button type="submit" class="rounded bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    <i class="fas fa-plus mr-1"></i> Add Learners
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

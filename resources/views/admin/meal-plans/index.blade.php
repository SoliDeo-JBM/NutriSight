@extends('layouts.dashboard')

@section('content')
    @php
        $prevMonth = $currentDate->copy()->subMonth()->toDateString();
        $nextMonth = $currentDate->copy()->addMonth()->toDateString();
        $startOfMonth = $currentDate->copy()->startOfMonth();
    @endphp

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Meal Plan & Calendar</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 lg:col-span-2">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <form method="GET" action="{{ route('admin.meal-plans.index') }}" class="flex items-center gap-2">
                    <select name="month" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm bg-white font-semibold">
                        @for($month = 1; $month <= 12; $month++)
                            <option value="{{ $month }}" @selected($currentDate->month === $month)>{{ \Carbon\Carbon::create()->month($month)->format('F') }}</option>
                        @endfor
                    </select>
                    <select name="year" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm bg-white font-semibold">
                        @for($year = now()->year - 2; $year <= now()->year + 2; $year++)
                            <option value="{{ $year }}" @selected($currentDate->year === $year)>{{ $year }}</option>
                        @endfor
                    </select>
                </form>
            </div>

            <div class="grid grid-cols-7 gap-2 text-center">
                @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="font-semibold text-gray-600 text-sm py-2">{{ $day }}</div>
                @endforeach
                @for($i = 0; $i < $startOfMonth->dayOfWeek; $i++)<div></div>@endfor
                @for($day = 1; $day <= $startOfMonth->daysInMonth; $day++)
                    @php
                        $loopDate = $startOfMonth->copy()->day($day)->toDateString();
                        $hasMeals = in_array($loopDate, $plannedDates, true);
                        $isToday = $loopDate === now()->toDateString();
                    @endphp
                    <a href="{{ route('admin.meal-plans.index', ['date' => $loopDate]) }}" class="p-4 rounded border text-sm font-semibold transition relative {{ $loopDate === $date ? 'ring-2 ring-blue-500 bg-blue-50' : ($hasMeals ? 'bg-emerald-100 text-emerald-800 border-emerald-300 hover:bg-emerald-200' : 'bg-gray-50 text-gray-700 hover:bg-gray-100') }}">
                        {{ $day }}
                        @if($isToday)<span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-blue-500"></span>@endif
                    </a>
                @endfor
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex flex-col min-h-[550px]">
            <h2 class="text-lg font-bold mb-4">Meal for {{ $date }}</h2>
            <form action="{{ route('admin.meal-plans.store') }}" method="POST" class="mb-4 p-3 border rounded-lg bg-gray-50">
                @csrf
                <input type="hidden" name="meal_date" value="{{ $date }}">
                <label for="meal_name" class="block text-xs font-semibold text-gray-600 mb-1">Add meal</label>
                <div class="flex gap-2">
                    <input id="meal_name" name="meal_name" required maxlength="255" class="min-w-0 flex-1 border-gray-300 rounded text-sm" placeholder="Meal name">
                    <button class="px-3 py-2 rounded bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700"><i class="fas fa-plus mr-1"></i>Add</button>
                </div>
                @error('meal_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                @error('meal_date')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </form>

            <div class="flex-1 overflow-y-auto space-y-3 pr-1">
                @forelse($mealPlans as $mealPlan)
                    <div x-data="{ editing: false }" class="p-3 border rounded-lg bg-gray-50">
                        <div class="flex items-center justify-between gap-3">
                            <div x-show="!editing" class="font-semibold text-sm min-w-0 break-words">{{ $mealPlan->meal_name }}</div>
                            <form action="{{ route('admin.meal-plans.update', $mealPlan) }}" method="POST" class="flex items-center gap-2 flex-1" x-show="editing">
                            @csrf @method('PUT')
                                <input name="meal_name" value="{{ $mealPlan->meal_name }}" required maxlength="255" class="min-w-0 flex-1 border-gray-300 rounded text-xs">
                                <button type="submit" class="px-2 py-1 rounded bg-blue-600 text-white text-xs font-semibold">Confirm</button>
                                <button type="button" @click="editing = false" class="px-2 py-1 rounded bg-gray-200 text-gray-700 text-xs font-semibold">Cancel</button>
                            </form>
                            <div x-show="!editing" class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="editing = true" class="px-2 py-1 rounded bg-gray-200 text-gray-700 text-xs font-semibold">Edit</button>
                                <form action="{{ route('admin.meal-plans.destroy', $mealPlan) }}" method="POST" onsubmit="return confirm('Delete this meal?')">
                                    @csrf @method('DELETE')
                                    <button class="px-2 py-1 rounded bg-red-100 text-red-700 text-xs font-semibold">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-6 text-sm">No meal assigned. Add meal first.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

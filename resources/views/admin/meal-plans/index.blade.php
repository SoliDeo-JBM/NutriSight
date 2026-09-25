@extends('layouts.dashboard')

@section('content')
@php
$startOfMonth = $currentDate->copy()->startOfMonth();
@endphp

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Meal Plan & Calendar</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 lg:col-span-2">
        <div class="flex justify-center mb-6">
            @php
            $previousMonth = $currentDate->copy()->subMonth()->startOfMonth();
            $nextMonth = $currentDate->copy()->addMonth()->startOfMonth();
            @endphp
            <div class="flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2">
                <a href="{{ route('admin.meal-plans.index', ['date' => $previousMonth->toDateString()]) }}" class="flex h-10 w-10 items-center justify-center rounded-md border border-gray-300 bg-white text-lg font-bold text-gray-700 hover:bg-gray-100" aria-label="Previous month">&lt;</a>
                <form method="GET" action="{{ route('admin.meal-plans.index') }}" class="flex items-center gap-1">
                    <label class="sr-only" for="meal-plan-month">Select month</label>
                    <select id="meal-plan-month" name="month" onchange="this.form.submit()" class="h-10 appearance-none border-0 bg-transparent px-2 text-center text-lg font-bold text-gray-800 focus:outline-none focus:ring-0">
                        @for($month = 1; $month <= 12; $month++)
                            <option value="{{ $month }}" @selected($currentDate->month === $month)>{{ $currentDate->copy()->month($month)->format('F') }}</option>
                        @endfor
                    </select>
                    <label class="sr-only" for="meal-plan-year">Select year</label>
                    <select id="meal-plan-year" name="year" onchange="this.form.submit()" aria-label="Select calendar year" class="h-10 border-0 bg-transparent px-1 text-center text-lg font-bold text-gray-800 focus:outline-none focus:ring-0">
                        @for($year = now()->year - 2; $year <= now()->year + 2; $year++)
                            <option value="{{ $year }}" @selected($currentDate->year === $year)>{{ $year }}</option>
                        @endfor
                    </select>
                </form>
                <a href="{{ route('admin.meal-plans.index', ['date' => $nextMonth->toDateString()]) }}" class="flex h-10 w-10 items-center justify-center rounded-md border border-gray-300 bg-white text-lg font-bold text-gray-700 hover:bg-gray-100" aria-label="Next month">&gt;</a>
            </div>
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

    @php
    $morningMeals = $mealPlans->where('meal_period', 'morning');
    $afternoonMeals = $mealPlans->where('meal_period', 'afternoon');
    @endphp
    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200 flex flex-col min-h-[550px]">
        <div class="flex items-start justify-between gap-3 mb-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-600">Daily menu</p>
                <h2 class="text-xl font-bold text-gray-900 mt-1">Meal for {{ \Carbon\Carbon::parse($date)->format('M j, Y') }}</h2>
            </div>
            <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                {{ $mealPlans->count() }} {{ $mealPlans->count() === 1 ? 'meal' : 'meals' }}
            </span>
        </div>

        <form action="{{ route('admin.meal-plans.store') }}" method="POST" class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50/60 p-4">
            @csrf
            <input type="hidden" name="meal_date" value="{{ $date }}">
            <div class="mb-3 flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-600 text-white"><i class="fas fa-plus text-xs"></i></span>
                <label for="meal_period" class="text-sm font-bold text-slate-800">Add a feeding session</label>
            </div>
            <div class="space-y-3">
                <div>
                    <label for="meal_period" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Session</label>
                    <select id="meal_period" name="meal_period" required class="w-full rounded-md border-gray-300 bg-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="morning">Morning</option>
                    <option value="afternoon">Afternoon</option>
                    </select>
                </div>
                <div>
                    <label for="meal_name" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Meal name</label>
                    <input id="meal_name" name="meal_name" required maxlength="255" class="w-full rounded-md border-gray-300 bg-white text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. Adobo with rice">
                </div>
                <button class="w-full rounded-md bg-emerald-600 px-3 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700"><i class="fas fa-plus mr-1"></i>Add meal</button>
            </div>
            @error('meal_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            @error('meal_date')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            @error('meal_period')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </form>

        <div class="flex-1 overflow-y-auto space-y-5 pr-1">
            @foreach([
                ['key' => 'morning', 'label' => 'Morning', 'icon' => 'fa-sun', 'accent' => 'emerald', 'meals' => $morningMeals],
                ['key' => 'afternoon', 'label' => 'Afternoon', 'icon' => 'fa-cloud-sun', 'accent' => 'amber', 'meals' => $afternoonMeals],
            ] as $session)
            <section>
                <div class="mb-2 flex items-center justify-between">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-slate-800">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $session['accent'] === 'emerald' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}"><i class="fas {{ $session['icon'] }} text-xs"></i></span>
                        {{ $session['label'] }} meal
                    </h3>
                    <span class="text-xs text-slate-400">{{ $session['meals']->count() }}</span>
                </div>
                <div class="space-y-2">
                    @forelse($session['meals'] as $mealPlan)
                    <div x-data="{ editing: false }" class="rounded-lg border border-slate-200 bg-white p-3 shadow-sm">
                        <div x-show="!editing" class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-words text-sm font-semibold text-slate-800">{{ $mealPlan->meal_name }}</p>
                                <p class="mt-1 text-xs text-slate-400">Added {{ $mealPlan->created_at?->format('g:i A') }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-1">
                                <button type="button" @click="editing = true" title="Edit meal" aria-label="Edit meal" class="flex h-8 w-8 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-800"><i class="fas fa-pen text-xs"></i></button>
                                <form action="{{ route('admin.meal-plans.destroy', $mealPlan) }}" method="POST" data-confirm-message="Delete this meal plan permanently? This action cannot be undone.">
                                    @csrf @method('DELETE')
                                    <button title="Delete meal" aria-label="Delete meal" class="flex h-8 w-8 items-center justify-center rounded-md text-rose-500 hover:bg-rose-50 hover:text-rose-700"><i class="fas fa-trash text-xs"></i></button>
                                </form>
                            </div>
                        </div>
                        <form action="{{ route('admin.meal-plans.update', $mealPlan) }}" method="POST" class="space-y-2" x-show="editing" x-cloak>
                            @csrf @method('PUT')
                            <select name="meal_period" required class="w-full rounded-md border-gray-300 text-sm">
                                <option value="morning" @selected($mealPlan->meal_period === 'morning')>Morning</option>
                                <option value="afternoon" @selected($mealPlan->meal_period === 'afternoon')>Afternoon</option>
                            </select>
                            <input name="meal_name" value="{{ $mealPlan->meal_name }}" required maxlength="255" class="w-full rounded-md border-gray-300 text-sm">
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 rounded-md bg-slate-800 px-3 py-2 text-xs font-bold text-white hover:bg-slate-700">Save changes</button>
                                <button type="button" @click="editing = false" class="rounded-md bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">Cancel</button>
                            </div>
                        </form>
                    </div>
                    @empty
                    <div class="rounded-lg border border-dashed border-slate-200 px-3 py-4 text-center text-xs text-slate-400">No {{ strtolower($session['label']) }} meal planned.</div>
                    @endforelse
                </div>
            </section>
            @endforeach
        </div>
    </div>
</div>
@endsection
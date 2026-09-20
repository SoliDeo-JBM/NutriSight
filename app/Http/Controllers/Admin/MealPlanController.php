<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MealPlan;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealPlanController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'year' => ['nullable', 'integer', 'between:1900,2200'],
            'month' => ['nullable', 'integer', 'between:1,12'],
        ]);

        $requestedDate = $request->input('date');
        $requestedYear = $request->integer('year');
        $requestedMonth = $request->integer('month');
        $currentDate = $requestedDate
            ? Carbon::parse($requestedDate)->startOfMonth()
            : ($requestedYear && $requestedMonth
                ? Carbon::create($requestedYear, $requestedMonth, 1)
                : Carbon::today()->startOfMonth());
        $date = $requestedDate
            ? Carbon::parse($requestedDate)->toDateString()
            : ($requestedYear && $requestedMonth
                ? $currentDate->toDateString()
                : Carbon::today()->toDateString());
        $mealPlans = MealPlan::whereDate('meal_date', $date)->latest()->get();
        $plannedDates = MealPlan::query()->select('meal_date')->distinct()->pluck('meal_date')->map(fn($mealDate) => Carbon::parse($mealDate)->toDateString())->all();

        return view('admin.meal-plans.index', compact('currentDate', 'date', 'mealPlans', 'plannedDates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'meal_date' => ['required', 'date'],
            'meal_name' => ['required', 'string', 'max:255'],
        ]);

        MealPlan::create($validated + ['recorded_by_user_id' => Auth::id()]);
        AuditLogger::log('Created', 'Meal Plan', 'Added meal for ' . $validated['meal_date'] . ': ' . $validated['meal_name']);

        return redirect()->route('admin.meal-plans.index', ['date' => $validated['meal_date']])->with('success', 'Meal added.');
    }

    public function update(Request $request, MealPlan $mealPlan)
    {
        $validated = $request->validate([
            'meal_name' => ['required', 'string', 'max:255'],
        ]);

        $mealPlan->update($validated);
        AuditLogger::log('Updated', 'Meal Plan', 'Updated meal for ' . $mealPlan->meal_date->toDateString() . ' to: ' . $mealPlan->meal_name);

        return redirect()->route('admin.meal-plans.index', ['date' => $mealPlan->meal_date->toDateString()])->with('success', 'Meal updated.');
    }

    public function destroy(MealPlan $mealPlan)
    {
        $date = $mealPlan->meal_date->toDateString();
        $mealPlan->delete();
        AuditLogger::log('Deleted', 'Meal Plan', 'Deleted meal for ' . $date . ': ' . $mealPlan->meal_name);

        return redirect()->route('admin.meal-plans.index', ['date' => $date])->with('success', 'Meal deleted.');
    }
}

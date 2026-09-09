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
        $year = (int) $request->input('year', Carbon::today()->year);
        $month = (int) $request->input('month', Carbon::today()->month);
        $currentDate = Carbon::create($year, $month, 1);
        $date = Carbon::parse($request->input('date', $currentDate->toDateString()))->toDateString();
        $mealPlans = MealPlan::whereDate('meal_date', $date)->latest()->get();
        $plannedDates = MealPlan::query()->select('meal_date')->distinct()->pluck('meal_date')->map(fn ($mealDate) => Carbon::parse($mealDate)->toDateString())->all();

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
        AuditLogger::log('Updated', 'Meal Plan', 'Updated meal plan ID ' . $mealPlan->id);

        return redirect()->route('admin.meal-plans.index', ['date' => $mealPlan->meal_date->toDateString()])->with('success', 'Meal updated.');
    }

    public function destroy(MealPlan $mealPlan)
    {
        $date = $mealPlan->meal_date->toDateString();
        $mealPlan->delete();
        AuditLogger::log('Deleted', 'Meal Plan', 'Deleted meal plan ID ' . $mealPlan->id);

        return redirect()->route('admin.meal-plans.index', ['date' => $date])->with('success', 'Meal deleted.');
    }
}

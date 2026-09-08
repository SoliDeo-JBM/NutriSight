<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\NutritionMeasurement;
use App\Services\NutriCalculationService;
use App\Services\SchoolYearManager;
use Illuminate\Http\Request;

class NutritionalController extends Controller
{
    protected $nutriService;

    public function __construct(NutriCalculationService $nutriService)
    {
        $this->nutriService = $nutriService;
    }

    public function store(Request $request, Student $student)
    {
        $validated = $request->validate([
            'measurement_period' => 'required|in:baseline,mid,end',
            'weight' => 'required|numeric',
            'height' => 'required|numeric',
            'remarks' => 'nullable|string',
        ]);

        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $enrollment = $student->enrollments()->where('school_year_id', $activeSyId)->first();

        if (!$enrollment || !$enrollment->sbfpParticipant) {
            return back()->withErrors(['error' => 'Student is not registered as an SBFP participant for the active school year.']);
        }

        $metrics = $this->nutriService->calculateBMI($validated['weight'], $validated['height']);

        NutritionMeasurement::create([
            'sbfp_participant_id' => $enrollment->sbfpParticipant->id,
            'measurement_period' => $validated['measurement_period'],
            'weight' => $validated['weight'],
            'height' => $validated['height'],
            'bmi' => $metrics['bmi'],
            'bmi_category' => $metrics['category'],
            'hfa' => 'Normal',
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return back()->with('success', 'Nutritional measurement added successfully.');
    }
}

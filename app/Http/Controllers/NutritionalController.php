<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\NutritionalRecord;
use App\Services\NutriCalculationService;
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
            'type' => 'required|in:baseline,mid,end',
            'weight' => 'required|numeric',
            'height' => 'required|numeric', // in meters
        ]);

        $metrics = $this->nutriService->calculateBMI($validated['weight'], $validated['height']);

        NutritionalRecord::create([
            'student_id' => $student->id,
            'type' => $validated['type'],
            'weight' => $validated['weight'],
            'height' => $validated['height'],
            'bmi' => $metrics['bmi'],
            'bmi_category' => $metrics['category'],
        ]);

        return back()->with('success', 'Nutritional record added.');
    }
}

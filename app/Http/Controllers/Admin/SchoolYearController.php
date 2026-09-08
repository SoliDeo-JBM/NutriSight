<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Services\AuditLogger;
use App\Services\SchoolYearManager;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    public function index()
    {
        $schoolYears = SchoolYearManager::allSchoolYears();
        $activeSchoolYear = SchoolYearManager::activeSchoolYear();
        return view('admin.school-years.index', compact('schoolYears', 'activeSchoolYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_year' => 'required|string|max:50|unique:school_years,year',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $schoolYear = SchoolYear::create([
            'year' => $validated['school_year'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => false,
        ]);

        AuditLogger::log('created', 'School Years', 'Created academic school year ' . $schoolYear->year);

        return redirect()->route('super-admin.school-years.index')
            ->with('success', 'School year ' . $schoolYear->year . ' created successfully.');
    }

    public function activate(SchoolYear $schoolYear)
    {
        SchoolYear::query()->update(['is_active' => false]);
        $schoolYear->update(['is_active' => true]);

        SchoolYearManager::setActiveSchoolYear($schoolYear->id);

        AuditLogger::log('updated', 'School Years', 'Activated academic school year ' . $schoolYear->year);

        return redirect()->back()
            ->with('success', 'Active school year changed to ' . $schoolYear->year . '.');
    }

    public function switch(Request $request)
    {
        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
        ]);

        SchoolYearManager::setActiveSchoolYear($validated['school_year_id']);
        $schoolYear = SchoolYear::find($validated['school_year_id']);

        AuditLogger::log('updated', 'School Years', 'Switched viewing context to school year ' . ($schoolYear?->year ?? 'Unknown'));

        return redirect()->back()
            ->with('success', 'Switched viewing school year to ' . ($schoolYear?->year ?? '') . '.');
    }
}

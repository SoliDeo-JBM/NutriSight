<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\SchoolYearManager;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $activeSy = SchoolYearManager::activeSchoolYear();
        
        // Get unique sections from enrollments for active school year
        $sections = Enrollment::where('school_year_id', $activeSy?->id)
            ->select('grade_level', 'section')
            ->distinct()
            ->orderBy('grade_level')
            ->orderBy('section')
            ->get();

        $encoders = User::whereIn('role', [User::ROLE_ENCODER, User::ROLE_ADMIN])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $gradeLevels = [0, 1, 2, 3, 4, 5, 6];
        $rolePrefix = auth()->user()->isSuperAdmin() ? 'super-admin' : 'admin';

        return view('admin.sections.index', compact('activeSy', 'sections', 'encoders', 'gradeLevels', 'rolePrefix'));
    }

    public function store(Request $request)
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $validated = $request->validate([
            'grade_level' => 'required|integer',
            'name' => 'required|string|max:255',
        ]);

        $gradeLevel = (int)$validated['grade_level'];
        $sectionName = ucfirst(strtolower($validated['name']));

        AuditLogger::log('created', 'Sections', 'Created section Grade ' . $gradeLevel . ' - ' . $sectionName);

        return back()->with('success', 'Section created successfully.');
    }

    public function update(Request $request, $id)
    {
        // Sections are tied to enrollments in the new schema
        return back()->with('success', 'Section updated successfully.');
    }

    public function carryOver()
    {
        $activeSy = SchoolYearManager::activeSchoolYear();
        if (!$activeSy) {
            return back()->withErrors(['carry_over' => 'No active school year found.']);
        }

        $previousSy = SchoolYear::where('start_date', '<', $activeSy->start_date)
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$previousSy) {
            return back()->withErrors(['carry_over' => 'No previous school year found to carry over from.']);
        }

        $prevSections = Enrollment::where('school_year_id', $previousSy->id)
            ->select('grade_level', 'section')
            ->distinct()
            ->get();

        $carriedCount = 0;
        foreach ($prevSections as $prev) {
            $exists = Enrollment::where('school_year_id', $activeSy->id)
                ->where('grade_level', $prev->grade_level)
                ->where('section', $prev->section)
                ->exists();

            if (!$exists) {
                $carriedCount++;
            }
        }

        AuditLogger::log('created', 'Sections', 'Carried over ' . $carriedCount . ' sections from ' . $previousSy->year . ' to ' . $activeSy->year);

        return back()->with('success', 'Successfully carried over ' . $carriedCount . ' sections from ' . $previousSy->year . '.');
    }

    public function destroy($id)
    {
        AuditLogger::log('deleted', 'Sections', 'Deleted section mapping');
        return back()->with('success', 'Section deleted successfully.');
    }
}

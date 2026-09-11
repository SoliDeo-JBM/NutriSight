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

        // 1. Get sections from enrollments
        $enrollmentSections = Enrollment::where('school_year_id', $activeSy?->id)
            ->select('grade_level', 'section')
            ->distinct()
            ->get();

        // 2. Get sections from teacher users' advisory assignments
        $userSections = User::where('role', User::ROLE_ENCODER)
            ->whereNotNull('advisory_grade_level')
            ->whereNotNull('advisory_section')
            ->select('advisory_grade_level as grade_level', 'advisory_section as section')
            ->distinct()
            ->get();

        // Merge and unique by grade_level + section
        $allSections = $enrollmentSections->concat($userSections)->unique(fn($item) => $item->grade_level . '-' . strtolower($item->section));

        $sections = $allSections->values()->map(function ($item, $index) {
            $adviser = User::where('role', User::ROLE_ENCODER)
                ->where('advisory_grade_level', (string)$item->grade_level)
                ->whereRaw('LOWER(advisory_section) = ?', [strtolower($item->section)])
                ->first();

            return (object)[
                'id' => $index + 1,
                'grade_level' => $item->grade_level,
                'name' => $item->section,
                'adviser' => $adviser,
                'adviser_id' => $adviser?->id,
            ];
        })->sortBy([['grade_level', 'asc'], ['name', 'asc']]);

        $encoders = User::where('role', User::ROLE_ENCODER)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $gradeLevels = [0, 1, 2, 3, 4, 5, 6];
        $rolePrefix = auth()->user()->isSuperAdmin() ? 'super-admin' : 'admin';

        return view('admin.sections.index', compact('activeSy', 'sections', 'encoders', 'gradeLevels', 'rolePrefix'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade_level' => 'required|integer|between:0,6',
            'name' => 'required|string|max:255',
            'adviser_id' => 'nullable|exists:users,id',
        ]);

        $gradeLevel = (int)$validated['grade_level'];
        $sectionName = ucfirst(strtolower($validated['name']));

        if (!empty($validated['adviser_id'])) {
            $user = User::find($validated['adviser_id']);
            if ($user) {
                $user->update([
                    'advisory_grade_level' => $gradeLevel,
                    'advisory_section' => $sectionName,
                ]);
            }
        }

        AuditLogger::log('created', 'Sections', 'Created section Grade ' . $gradeLevel . ' - ' . $sectionName);

        return back()->with('success', 'Section created and adviser assigned successfully.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'grade_level' => 'required|integer|between:0,6',
            'name' => 'required|string|max:255',
            'adviser_id' => 'nullable|exists:users,id',
        ]);

        $gradeLevel = (int)$validated['grade_level'];
        $sectionName = ucfirst(strtolower($validated['name']));

        if (!empty($validated['adviser_id'])) {
            $user = User::find($validated['adviser_id']);
            if ($user) {
                $user->update([
                    'advisory_grade_level' => $gradeLevel,
                    'advisory_section' => $sectionName,
                ]);
            }
        }

        AuditLogger::log('updated', 'Sections', 'Updated section Grade ' . $gradeLevel . ' - ' . $sectionName);

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

        $prevEnrollments = Enrollment::where('school_year_id', $previousSy->id)
            ->select('grade_level', 'section')
            ->distinct()
            ->get();

        $carriedCount = 0;
        $activeSyId = $activeSy->id;

        foreach ($prevEnrollments as $prev) {
            $exists = Enrollment::where('school_year_id', $activeSyId)
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

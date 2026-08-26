<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Section;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\SchoolYearManager;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $activeSy = SchoolYearManager::activeSchoolYear();
        $sections = Section::with('adviser')
            ->where('school_year_id', $activeSy?->id)
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $encoders = User::where('role', User::ROLE_ENCODER)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $gradeLevels = ['Kinder', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];

        $rolePrefix = auth()->user()->isSuperAdmin() ? 'super-admin' : 'admin';

        return view('admin.sections.index', compact('activeSy', 'sections', 'encoders', 'gradeLevels', 'rolePrefix'));
    }

    public function store(Request $request)
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $validated = $request->validate([
            'grade_level' => 'required|string',
            'name' => 'required|string|max:255',
            'adviser_id' => 'nullable|exists:users,id',
        ]);

        $section = Section::create([
            'school_year_id' => $activeSyId,
            'grade_level' => $validated['grade_level'],
            'name' => ucfirst(strtolower($validated['name'])),
            'adviser_id' => $validated['adviser_id'] ?? null,
        ]);

        AuditLogger::log('created', 'Sections', 'Created section ' . $section->grade_level . ' - ' . $section->name);

        return back()->with('success', 'Section created and assigned successfully.');
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'grade_level' => 'required|string',
            'name' => 'required|string|max:255',
            'adviser_id' => 'nullable|exists:users,id',
        ]);

        $section->update([
            'grade_level' => $validated['grade_level'],
            'name' => ucfirst(strtolower($validated['name'])),
            'adviser_id' => $validated['adviser_id'] ?? null,
        ]);

        AuditLogger::log('updated', 'Sections', 'Updated section ' . $section->grade_level . ' - ' . $section->name);

        return back()->with('success', 'Section updated successfully.');
    }

    public function carryOver()
    {
        $activeSy = SchoolYearManager::activeSchoolYear();
        if (!$activeSy) {
            return back()->withErrors(['carry_over' => 'No active school year found.']);
        }

        // Find previous school year
        $previousSy = Program::where('start_date', '<', $activeSy->start_date)
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$previousSy) {
            return back()->withErrors(['carry_over' => 'No previous school year found to carry over from.']);
        }

        $prevSections = Section::where('school_year_id', $previousSy->id)->get();
        $carriedCount = 0;

        foreach ($prevSections as $prev) {
            $exists = Section::where('school_year_id', $activeSy->id)
                ->where('grade_level', $prev->grade_level)
                ->where('name', $prev->name)
                ->exists();

            if (!$exists) {
                Section::create([
                    'school_year_id' => $activeSy->id,
                    'grade_level' => $prev->grade_level,
                    'name' => $prev->name,
                    'adviser_id' => $prev->adviser_id, // Carries over previous adviser as default
                ]);
                $carriedCount++;
            }
        }

        AuditLogger::log('created', 'Sections', 'Carried over ' . $carriedCount . ' sections from ' . $previousSy->school_year . ' to ' . $activeSy->school_year);

        return back()->with('success', 'Successfully carried over ' . $carriedCount . ' sections and default adviser assignments from ' . $previousSy->school_year . '.');
    }

    public function destroy(Section $section)
    {
        $name = $section->grade_level . ' - ' . $section->name;
        $section->delete();

        AuditLogger::log('deleted', 'Sections', 'Deleted section ' . $name);

        return back()->with('success', 'Section deleted successfully.');
    }
}

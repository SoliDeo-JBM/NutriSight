<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Section;
use App\Models\Student;
use App\Services\AuditLogger;
use App\Services\SchoolYearManager;
use Illuminate\Http\Request;

class StudentPromotionController extends Controller
{
    public function index(Request $request)
    {
        $activeSy = SchoolYearManager::activeSchoolYear();
        $allSy = SchoolYearManager::allSchoolYears();
        
        // Students in active school year
        $activeStudents = Student::with('section')
            ->where('school_year_id', $activeSy?->id)
            ->get();

        // Previous school years for promotion source
        $sourceSyId = $request->input('source_school_year_id');
        $sourceStudents = collect();

        if ($sourceSyId) {
            $sourceStudents = Student::with('section')
                ->where('school_year_id', $sourceSyId)
                ->get();
        }

        $sections = Section::where('school_year_id', $activeSy?->id)->get();

        return view('admin.students.promote', compact('activeSy', 'allSy', 'activeStudents', 'sourceSyId', 'sourceStudents', 'sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'grade_level' => 'required|string',
            'section_id' => 'required|exists:sections,id',
        ]);

        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $section = Section::find($validated['section_id']);
        $promotedCount = 0;

        foreach ($validated['student_ids'] as $id) {
            $sourceStudent = Student::find($id);
            if (!$sourceStudent) {
                continue;
            }

            // Check if already enrolled in active school year by LRN (student_number)
            $existing = Student::where('student_number', $sourceStudent->student_number)
                ->where('school_year_id', $activeSyId)
                ->first();

            if (!$existing) {
                $newStudent = Student::create([
                    'school_year_id' => $activeSyId,
                    'student_number' => $sourceStudent->student_number,
                    'first_name' => $sourceStudent->first_name,
                    'last_name' => $sourceStudent->last_name,
                    'name_extension' => $sourceStudent->name_extension,
                    'middle_name' => $sourceStudent->middle_name,
                    'birth_date' => $sourceStudent->birth_date,
                    'gender' => $sourceStudent->gender,
                    'grade_level' => $validated['grade_level'],
                    'section' => $section?->name ?? '',
                    'section_id' => $section?->id,
                    'guardian_name' => $sourceStudent->guardian_name,
                    'guardian_contact' => $sourceStudent->guardian_contact,
                    'guardian_email' => $sourceStudent->guardian_email,
                    'address' => $sourceStudent->address,
                    'is_permitted' => false,
                    'parent_approval_status' => null,
                ]);

                $promotedCount++;
            }
        }

        AuditLogger::log('created', 'Student Promotion', 'Promoted / enrolled ' . $promotedCount . ' students into active school year (' . ($section?->name ?? '') . ')');

        return redirect()->route('super-admin.students.promote')
            ->with('success', 'Successfully promoted/enrolled ' . $promotedCount . ' students into the active school year.');
    }
}

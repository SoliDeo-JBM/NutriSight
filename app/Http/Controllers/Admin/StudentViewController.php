<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Section;
use Illuminate\Http\Request;

class StudentViewController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['section', 'nutritionalRecords']);

        // Search by name or student number (LRN)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            if (mb_strlen($search) === 1) {
                $searchTerm = strtolower($search) . '%';
            } else {
                $searchTerm = '%' . strtolower($search) . '%';
            }
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(student_number) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(first_name) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(middle_name) LIKE ?', [$searchTerm]);
            });
        }

        // Filter by grade level
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->input('grade_level'));
        }

        // Filter by section
        if ($request->filled('section')) {
            $query->where('section', $request->input('section'));
        }

        // Filter by sex
        if ($request->filled('sex')) {
            $query->where('gender', $request->input('sex'));
        }

        $students = $query->paginate(15)->withQueryString();

        $gradeLevels = Student::whereNotNull('grade_level')->distinct()->pluck('grade_level');
        $sections = Section::pluck('name');
        $sexes = ['Male', 'Female'];

        return view('admin.students.index', compact('students', 'gradeLevels', 'sections', 'sexes'));
    }

    public function sbfpIndex(Request $request)
    {
        $query = Student::with(['section', 'nutritionalRecords', 'assessments'])
            ->where(function ($q) {
                $q->where('is_permitted', true)
                  ->orWhereHas('nutritionalRecords', function ($sub) {
                      $sub->whereIn('bmi_category', ['Wasted', 'Severely Wasted']);
                  });
            })
            ->where(function ($q) {
                $q->where('parent_approval_status', '!=', 'disapproved')
                  ->orWhereNull('parent_approval_status');
            });

        // Search by name or student number (LRN)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            if (mb_strlen($search) === 1) {
                $searchTerm = strtolower($search) . '%';
            } else {
                $searchTerm = '%' . strtolower($search) . '%';
            }
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(student_number) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(first_name) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(last_name) LIKE ?', [$searchTerm])
                  ->orWhereRaw('LOWER(middle_name) LIKE ?', [$searchTerm]);
            });
        }

        // Filter by grade level
        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->input('grade_level'));
        }

        // Filter by section
        if ($request->filled('section')) {
            $query->where('section', $request->input('section'));
        }

        // Filter by sex
        if ($request->filled('sex')) {
            $query->where('gender', $request->input('sex'));
        }

        $students = $query->paginate(15)->withQueryString();

        $gradeLevels = Student::whereNotNull('grade_level')->distinct()->pluck('grade_level');
        $sections = Section::pluck('name');
        $sexes = ['Male', 'Female'];

        return view('admin.students.sbfp', compact('students', 'gradeLevels', 'sections', 'sexes'));
    }
}

<?php

namespace App\Services;

use App\Models\Program;
use Illuminate\Support\Facades\Session;

class SchoolYearManager
{
    public static function activeSchoolYear(): ?Program
    {
        $activeId = Session::get('active_school_year_id');

        if ($activeId) {
            $program = Program::find($activeId);
            if ($program) {
                return $program;
            }
        }

        $program = Program::where('is_active', true)->first() ?? Program::latest('id')->first();
        
        if ($program) {
            Session::put('active_school_year_id', $program->id);
        }

        return $program;
    }

    public static function activeSchoolYearId(): ?int
    {
        return self::activeSchoolYear()?->id;
    }

    public static function setActiveSchoolYear(int $id): bool
    {
        $program = Program::find($id);
        if ($program) {
            Session::put('active_school_year_id', $program->id);
            return true;
        }
        return false;
    }

    public static function allSchoolYears()
    {
        return Program::orderBy('start_date', 'desc')->get();
    }
}

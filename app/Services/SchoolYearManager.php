<?php

namespace App\Services;

use App\Models\SchoolYear;
use Illuminate\Support\Facades\Session;

class SchoolYearManager
{
    public static function activeSchoolYear(): ?SchoolYear
    {
        $activeId = Session::get('active_school_year_id');

        if ($activeId) {
            $schoolYear = SchoolYear::find($activeId);
            if ($schoolYear) {
                return $schoolYear;
            }
        }

        $schoolYear = SchoolYear::where('is_active', true)->first() ?? SchoolYear::latest('id')->first();
        
        if ($schoolYear) {
            Session::put('active_school_year_id', $schoolYear->id);
        }

        return $schoolYear;
    }

    public static function activeSchoolYearId(): ?int
    {
        return self::activeSchoolYear()?->id;
    }

    public static function setActiveSchoolYear(int $id): bool
    {
        $schoolYear = SchoolYear::find($id);
        if ($schoolYear) {
            Session::put('active_school_year_id', $schoolYear->id);
            return true;
        }
        return false;
    }

    public static function allSchoolYears()
    {
        return SchoolYear::orderBy('start_date', 'desc')->get();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function superAdmin()
    {
        return view('dashboards.super-admin');
    }

    public function admin()
    {
        return view('dashboards.admin');
    }

    public function encoder()
    {
        $totalStudents = \App\Models\Student::count();
        $totalSbfp = \App\Models\Student::where('is_permitted', true)->count();
        
        // Attendance chart data (last 7 days)
        $attendanceDates = [];
        $attendanceCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i)->toDateString();
            $attendanceDates[] = \Carbon\Carbon::parse($date)->format('M d');
            $attendanceCounts[] = \App\Models\AttendanceLog::where('date', $date)->where('status', 'present')->count();
        }

        return view('dashboards.encoder', compact('totalStudents', 'totalSbfp', 'attendanceDates', 'attendanceCounts'));
    }
}
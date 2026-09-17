<?php

namespace App\Http\Controllers;

use App\Services\SchoolLogoService;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class SchoolLogoController extends Controller
{
    public function file()
    {
        return response()->file(SchoolLogoService::path(), [
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    public function edit()
    {
        return view('super-admin.school-logo', [
            'schoolLogoUrl' => SchoolLogoService::url(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_logo' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        SchoolLogoService::upload($validated['school_logo']);
        AuditLogger::log('Updated', 'System Settings', 'Updated the school logo used in downloadable SBFP reports');

        return back()->with('success', 'School logo updated successfully.');
    }
}

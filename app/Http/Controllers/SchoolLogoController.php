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
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function depedFile()
    {
        return response()->file(SchoolLogoService::depedPath(), [
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function edit()
    {
        return view('super-admin.school-logo', [
            'schoolLogoUrl' => SchoolLogoService::url(),
            'depedLogoUrl' => SchoolLogoService::depedUrl(),
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

    public function updateDepEd(Request $request)
    {
        $validated = $request->validate([
            'deped_logo' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        SchoolLogoService::uploadDepEd($validated['deped_logo']);
        AuditLogger::log('Updated', 'System Settings', 'Updated the DepEd logo used in downloadable SBFP reports');

        return back()->with('success', 'DepEd logo updated successfully.');
    }

    public function reset()
    {
        SchoolLogoService::reset();
        AuditLogger::log('Updated', 'System Settings', 'Reset the school report logo to the default');

        return back()->with('success', 'School logo reset to the default.');
    }

    public function resetDepEd()
    {
        SchoolLogoService::resetDepEd();
        AuditLogger::log('Updated', 'System Settings', 'Reset the DepEd report logo to the default');

        return back()->with('success', 'DepEd logo reset to the default.');
    }
}

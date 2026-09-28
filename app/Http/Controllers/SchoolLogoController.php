<?php

namespace App\Http\Controllers;

use App\Services\SchoolLogoService;
use App\Services\AuditLogger;
use App\Services\SchoolYearManager;
use App\Models\SchoolYearReportSetting;
use Illuminate\Http\Request;

class SchoolLogoController extends Controller
{
    public function file(Request $request)
    {
        return response()->file(SchoolLogoService::path($request->integer('school_year_id') ?: null), [
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function depedFile(Request $request)
    {
        return response()->file(SchoolLogoService::depedPath($request->integer('school_year_id') ?: null), [
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function edit()
    {
        $schoolYearId = SchoolYearManager::activeSchoolYearId();
        $reportSetting = $schoolYearId
            ? SchoolYearReportSetting::where('school_year_id', $schoolYearId)->first()
            : null;

        return view('super-admin.school-logo', [
            'schoolLogoUrl' => SchoolLogoService::url($schoolYearId),
            'depedLogoUrl' => SchoolLogoService::depedUrl($schoolYearId),
            'projectDevelopmentOfficerName' => $reportSetting?->project_development_officer_name,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_logo' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        SchoolLogoService::upload($validated['school_logo'], SchoolYearManager::activeSchoolYearId());
        AuditLogger::log('Updated', 'System Settings', 'Updated the school logo used in downloadable SBFP reports with file ' . $validated['school_logo']->getClientOriginalName());

        return back()->with('success', 'School logo updated successfully.');
    }

    public function updateDepEd(Request $request)
    {
        $validated = $request->validate([
            'deped_logo' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        SchoolLogoService::uploadDepEd($validated['deped_logo'], SchoolYearManager::activeSchoolYearId());
        AuditLogger::log('Updated', 'System Settings', 'Updated the DepEd logo used in downloadable SBFP reports with file ' . $validated['deped_logo']->getClientOriginalName());

        return back()->with('success', 'DepEd logo updated successfully.');
    }

    public function updateProjectDevelopmentOfficer(Request $request)
    {
        $schoolYearId = SchoolYearManager::activeSchoolYearId();
        abort_unless($schoolYearId, 422, 'An active school year is required.');

        $validated = $request->validate([
            'project_development_officer_name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s.\'-]+$/u'],
        ]);

        SchoolYearReportSetting::updateOrCreate(
            ['school_year_id' => $schoolYearId],
            ['project_development_officer_name' => trim($validated['project_development_officer_name'])]
        );

        AuditLogger::log('Updated', 'System Settings', 'Updated the Project Development Officer name for the active school year');

        return back()->with('success', 'Project Development Officer name updated successfully.');
    }

    public function reset()
    {
        SchoolLogoService::reset(SchoolYearManager::activeSchoolYearId());
        AuditLogger::log('Updated', 'System Settings', 'Reset the school report logo to the default');

        return back()->with('success', 'School logo reset to the default.');
    }

    public function resetDepEd()
    {
        SchoolLogoService::resetDepEd(SchoolYearManager::activeSchoolYearId());
        AuditLogger::log('Updated', 'System Settings', 'Reset the DepEd report logo to the default');

        return back()->with('success', 'DepEd logo reset to the default.');
    }
}

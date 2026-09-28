<?php

namespace Tests\Feature;

use App\Models\SchoolYear;
use App\Models\SchoolYearReportSetting;
use App\Models\User;
use App\Http\Controllers\ReportsController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class ReportSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_development_officer_name_is_saved_for_the_active_school_year(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $schoolYear = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->put(route('super-admin.school-logo.project-development-officer.update'), [
            'project_development_officer_name' => 'Juan Dela Cruz',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('school_year_report_settings', [
            'school_year_id' => $schoolYear->id,
            'project_development_officer_name' => 'Juan Dela Cruz',
        ]);
    }

    public function test_report_signatory_uses_the_school_year_setting_instead_of_admin_name(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Admin Account']);
        $schoolYear = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);
        SchoolYearReportSetting::create([
            'school_year_id' => $schoolYear->id,
            'project_development_officer_name' => 'Juan Dela Cruz',
        ]);

        $method = new ReflectionMethod(ReportsController::class, 'reportSignatories');
        $method->setAccessible(true);
        $signatories = $method->invoke(app(ReportsController::class), $schoolYear->id);

        $this->assertSame('JUAN DELA CRUZ', $signatories[0]);
        $this->assertNotSame('ADMIN ACCOUNT', $signatories[0]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\SchoolYear;
use App\Models\SchoolYearReportSetting;
use App\Models\User;
use App\Http\Controllers\ReportsController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use ReflectionMethod;
use Tests\TestCase;

class ReportSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

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

    public function test_school_logo_upload_prevents_renamed_gif_from_replacing_saved_logo(): void
    {
        [$superAdmin, $schoolYear] = $this->createLogoUploadContext();
        $savedLogo = 'settings/school-logo.' . $schoolYear->id . '.saved.png';
        Storage::disk('public')->put($savedLogo, 'existing logo');
        $gifContents = base64_decode('R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=');

        $response = $this->actingAs($superAdmin)->from(route('super-admin.school-logo.edit'))
            ->put(route('super-admin.school-logo.update'), [
                'school_logo' => UploadedFile::fake()->createWithContent('renamed.jpg', $gifContents),
            ]);

        $response->assertRedirect(route('super-admin.school-logo.edit'))
            ->assertSessionHasErrors('school_logo');
        $this->assertTrue(Storage::disk('public')->exists($savedLogo));
        $this->assertSame([$savedLogo], Storage::disk('public')->allFiles('settings'));
    }

    public function test_deped_logo_upload_prevents_malformed_image_from_replacing_saved_logo(): void
    {
        [$superAdmin, $schoolYear] = $this->createLogoUploadContext();
        $savedLogo = 'settings/deped-logo.' . $schoolYear->id . '.saved.png';
        Storage::disk('public')->put($savedLogo, 'existing logo');

        $response = $this->actingAs($superAdmin)->from(route('super-admin.school-logo.edit'))
            ->put(route('super-admin.school-logo.deped.update'), [
                'deped_logo' => UploadedFile::fake()->createWithContent('malformed.png', base64_decode('iVBORw0KGgo=')),
            ]);

        $response->assertRedirect(route('super-admin.school-logo.edit'))
            ->assertSessionHasErrors('deped_logo');
        $this->assertTrue(Storage::disk('public')->exists($savedLogo));
        $this->assertSame([$savedLogo], Storage::disk('public')->allFiles('settings'));
    }

    public function test_valid_jpeg_logo_upload_is_saved_with_its_detected_extension(): void
    {
        [$superAdmin, $schoolYear] = $this->createLogoUploadContext();

        $response = $this->actingAs($superAdmin)->from(route('super-admin.school-logo.edit'))
            ->put(route('super-admin.school-logo.update'), [
                'school_logo' => UploadedFile::fake()->image('school-logo.jpg'),
            ]);

        $response->assertRedirect(route('super-admin.school-logo.edit'))
            ->assertSessionHasNoErrors();
        $files = Storage::disk('public')->allFiles('settings');
        $this->assertCount(1, $files);
        $this->assertStringEndsWith('.jpg', $files[0]);
        $this->assertStringContainsString('school-logo.' . $schoolYear->id . '.', $files[0]);
    }

    private function createLogoUploadContext(): array
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $schoolYear = SchoolYear::create([
            'year' => '2025-2026',
            'start_date' => '2025-06-01',
            'end_date' => '2026-03-31',
            'is_active' => true,
        ]);

        return [$superAdmin, $schoolYear];
    }
}

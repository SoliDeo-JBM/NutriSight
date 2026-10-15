<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\SbfpParticipant;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SbfpProfileImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('r2');
        config(['filesystems.disks.r2.url' => 'https://images.example.test']);
    }

    public function test_valid_jpeg_upload_is_stored_and_updates_the_participant(): void
    {
        [$encoder, $schoolYear, $participant] = $this->createUploadContext();

        $response = $this->actingAs($encoder)
            ->withSession(['active_school_year_id' => $schoolYear->id])
            ->from(route('encoder.students.sbfp'))
            ->post(route('encoder.students.sbfp.profile-images'), [
                'profiles' => [$participant->id => UploadedFile::fake()->image('student.jpg')],
            ]);

        $response->assertRedirect(route('encoder.students.sbfp'))
            ->assertSessionHas('profile_upload_success', 'Uploaded 1 profile image(s).');

        $this->assertNotNull($participant->fresh()->getRawOriginal('profile_image_url'));
        $this->assertCount(1, Storage::disk('r2')->allFiles('sbfp-profiles'));
    }

    public function test_gif_renamed_as_jpeg_is_rejected_without_storage_or_record_update(): void
    {
        [$encoder, $schoolYear, $participant] = $this->createUploadContext();
        $gifContents = base64_decode('R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=');

        $response = $this->actingAs($encoder)
            ->withSession(['active_school_year_id' => $schoolYear->id])
            ->from(route('encoder.students.sbfp'))
            ->post(route('encoder.students.sbfp.profile-images'), [
                'profiles' => [$participant->id => UploadedFile::fake()->createWithContent('renamed.jpg', $gifContents)],
            ]);

        $response->assertRedirect(route('encoder.students.sbfp'))
            ->assertSessionHasErrors([
                'profiles.' . $participant->id => 'The selected file is not a valid JPEG, PNG, or WEBP image. Renamed or unsupported files are not accepted.',
            ]);

        $this->assertNull($participant->fresh()->getRawOriginal('profile_image_url'));
        $this->assertCount(0, Storage::disk('r2')->allFiles('sbfp-profiles'));
    }

    public function test_malformed_png_is_rejected_without_storage_or_record_update(): void
    {
        [$encoder, $schoolYear, $participant] = $this->createUploadContext();

        $response = $this->actingAs($encoder)
            ->withSession(['active_school_year_id' => $schoolYear->id])
            ->from(route('encoder.students.sbfp'))
            ->post(route('encoder.students.sbfp.profile-images'), [
                'profiles' => [$participant->id => UploadedFile::fake()->createWithContent('malformed.png', base64_decode('iVBORw0KGgo='))],
            ]);

        $response->assertRedirect(route('encoder.students.sbfp'))
            ->assertSessionHasErrors([
                'profiles.' . $participant->id => 'The selected file is not a valid JPEG, PNG, or WEBP image. Renamed or unsupported files are not accepted.',
            ]);

        $this->assertNull($participant->fresh()->getRawOriginal('profile_image_url'));
        $this->assertCount(0, Storage::disk('r2')->allFiles('sbfp-profiles'));
    }

    private function createUploadContext(): array
    {
        $schoolYear = SchoolYear::create([
            'year' => '2026-2027',
            'start_date' => '2026-06-01',
            'end_date' => '2027-03-31',
            'is_active' => true,
        ]);
        $encoder = User::factory()->create([
            'role' => User::ROLE_ENCODER,
            'advisory_grade_level' => 2,
            'advisory_section' => 'A',
        ]);
        $student = Student::create([
            'lrn' => '136542100012',
            'first_name' => 'Test',
            'last_name' => 'Learner',
            'sex' => 'Female',
            'birth_date' => '2015-01-01',
            'guardian_name' => 'Guardian',
            'guardian_email' => 'guardian@example.test',
            'guardian_contact' => '09171234567',
            'address' => 'Test Address',
        ]);
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'school_year_id' => $schoolYear->id,
            'grade_level' => 2,
            'section' => 'A',
            'status' => Enrollment::STATUS_ENROLLED,
        ]);
        $participant = SbfpParticipant::create([
            'enrollment_id' => $enrollment->id,
            'parent_consent' => 'approved',
        ]);

        return [$encoder, $schoolYear, $participant];
    }
}
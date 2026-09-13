<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Enrollment; // Added this import so the method below works!
use App\Services\SchoolYearManager;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'deped_id',
        'sex',
        'birthdate',
        'position',
        'advisory_grade_level',
        'advisory_section',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_ENCODER = 'encoder';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isEncoder(): bool
    {
        return $this->role === self::ROLE_ENCODER;
    }

    public function isAdminOrAbove(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]);
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'super-admin.dashboard',
            self::ROLE_ADMIN => 'admin.dashboard',
            self::ROLE_ENCODER => 'encoder.dashboard',
            default => 'home',
        };
    }

    public function advisoryEnrollments()
    {
        // Matches the teacher's assigned section text directly to the enrollments table
        return Enrollment::where('grade_level', $this->advisory_grade_level)
                         ->where('section', $this->advisory_section);
    }

    public function schoolYearUserRecords()
    {
        return $this->hasMany(SchoolYearUserRecord::class);
    }

    public function currentSchoolYearUserRecord(): ?SchoolYearUserRecord
    {
        $schoolYearId = SchoolYearManager::activeSchoolYearId();

        if (!$schoolYearId) {
            return null;
        }

        return $this->schoolYearUserRecords()->where('school_year_id', $schoolYearId)->first();
    }

    public function syncSchoolYearUserRecord(?int $schoolYearId = null): ?SchoolYearUserRecord
    {
        $schoolYearId ??= SchoolYearManager::activeSchoolYearId();

        if (!$schoolYearId) {
            return null;
        }

        return $this->schoolYearUserRecords()->updateOrCreate(
            ['school_year_id' => $schoolYearId],
            [
                'role' => $this->role,
                'deped_id' => $this->getRawOriginal('deped_id'),
                'position' => $this->getRawOriginal('position'),
                'advisory_grade_level' => $this->getRawOriginal('advisory_grade_level'),
                'advisory_section' => $this->getRawOriginal('advisory_section'),
            ]
        );
    }

    public function getSchoolYearAssignmentAttribute(): ?SchoolYearUserRecord
    {
        return $this->currentSchoolYearUserRecord();
    }

    public function getDepedIdAttribute($value)
    {
        return $this->currentSchoolYearUserRecord()?->deped_id ?? $value;
    }

    public function getPositionAttribute($value)
    {
        return $this->currentSchoolYearUserRecord()?->position ?? $value;
    }

    public function getAdvisoryGradeLevelAttribute($value)
    {
        return $this->currentSchoolYearUserRecord()?->advisory_grade_level ?? $value;
    }

    public function getAdvisorySectionAttribute($value)
    {
        return $this->currentSchoolYearUserRecord()?->advisory_section ?? $value;
    }
}
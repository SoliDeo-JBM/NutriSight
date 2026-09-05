<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'deped_id', 'sex', 'birthdate', 'position', 'advisory_grade_level', 'advisory_section', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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

    public function sections()
    {
        return $this->hasMany(Section::class, 'adviser_id');
    }

    public function activeSections()
    {
        $activeSyId = \App\Services\SchoolYearManager::activeSchoolYearId();
        return $this->sections()->where('school_year_id', $activeSyId);
    }
}

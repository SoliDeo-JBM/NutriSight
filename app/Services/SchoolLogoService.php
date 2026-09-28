<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\SchoolYearManager;

class SchoolLogoService
{
    private const DIRECTORY = 'settings';
    private const FILE_PREFIX = 'school-logo.';
    private const DEFAULT_PATH = 'images/id/mbes.png';
    private const DEPED_FILE_PREFIX = 'deped-logo.';
    private const DEPED_DEFAULT_PATH = 'images/id/deped.png';

    public static function path(?int $schoolYearId = null): string
    {
        $storedPath = self::storedPath(self::FILE_PREFIX, $schoolYearId);

        return $storedPath ? Storage::disk('public')->path($storedPath) : public_path(self::DEFAULT_PATH);
    }

    public static function url(?int $schoolYearId = null): string
    {
        return route('school-logo.file', ['school_year_id' => $schoolYearId ?? SchoolYearManager::activeSchoolYearId(), 'v' => self::version(self::FILE_PREFIX, $schoolYearId)]);
    }

    public static function depedPath(?int $schoolYearId = null): string
    {
        $storedPath = self::storedPath(self::DEPED_FILE_PREFIX, $schoolYearId);

        return $storedPath ? Storage::disk('public')->path($storedPath) : public_path(self::DEPED_DEFAULT_PATH);
    }

    public static function depedUrl(?int $schoolYearId = null): string
    {
        return route('school-logo.deped-file', ['school_year_id' => $schoolYearId ?? SchoolYearManager::activeSchoolYearId(), 'v' => self::version(self::DEPED_FILE_PREFIX, $schoolYearId)]);
    }

    public static function upload(UploadedFile $file, ?int $schoolYearId = null): void
    {
        self::store($file, self::FILE_PREFIX, $schoolYearId);
    }

    public static function uploadDepEd(UploadedFile $file, ?int $schoolYearId = null): void
    {
        self::store($file, self::DEPED_FILE_PREFIX, $schoolYearId);
    }

    public static function reset(?int $schoolYearId = null): void
    {
        self::deleteStored(self::FILE_PREFIX, null, $schoolYearId);
    }

    public static function resetDepEd(?int $schoolYearId = null): void
    {
        self::deleteStored(self::DEPED_FILE_PREFIX, null, $schoolYearId);
    }

    private static function store(UploadedFile $file, string $prefix, ?int $schoolYearId = null): void
    {
        $disk = Storage::disk('public');
        $disk->makeDirectory(self::DIRECTORY);

        $extension = strtolower($file->getClientOriginalExtension());
        $yearId = $schoolYearId ?? SchoolYearManager::activeSchoolYearId();
        $scopedPrefix = self::scopedPrefix($prefix, $yearId);
        $filename = $scopedPrefix . bin2hex(random_bytes(8)) . '.' . $extension;
        $storedPath = $disk->putFileAs(self::DIRECTORY, $file, $filename);

        if ($storedPath === false || !$disk->exists($storedPath)) {
            throw new \RuntimeException('The logo could not be saved to application storage.');
        }

        self::deleteStored($prefix, $storedPath, $yearId);
    }

    private static function deleteStored(string $prefix, ?string $except = null, ?int $schoolYearId = null): void
    {
        $disk = Storage::disk('public');

        foreach ($disk->files(self::DIRECTORY) as $storedPath) {
            if ($storedPath !== $except && str_starts_with(basename($storedPath), self::scopedPrefix($prefix, $schoolYearId))) {
                $disk->delete($storedPath);
            }
        }
    }

    private static function storedPath(string $prefix = self::FILE_PREFIX, ?int $schoolYearId = null): ?string
    {
        $disk = Storage::disk('public');

        foreach ($disk->files(self::DIRECTORY) as $storedPath) {
            if (str_starts_with(basename($storedPath), self::scopedPrefix($prefix, $schoolYearId))) {
                return $storedPath;
            }
        }

        return null;
    }

    private static function version(string $prefix, ?int $schoolYearId = null): string
    {
        $storedPath = self::storedPath($prefix, $schoolYearId);

        if ($storedPath) {
            return substr(hash_file('sha256', Storage::disk('public')->path($storedPath)), 0, 16);
        }

        $defaultPath = $prefix === self::DEPED_FILE_PREFIX ? self::DEPED_DEFAULT_PATH : self::DEFAULT_PATH;

        return substr(hash_file('sha256', public_path($defaultPath)), 0, 16);
    }

    private static function scopedPrefix(string $prefix, ?int $schoolYearId): string
    {
        $schoolYearId ??= SchoolYearManager::activeSchoolYearId();

        return $schoolYearId ? $prefix . $schoolYearId . '.' : $prefix;
    }
}

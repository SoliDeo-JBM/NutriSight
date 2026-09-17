<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SchoolLogoService
{
    private const DIRECTORY = 'settings';
    private const FILE_PREFIX = 'school-logo.';
    private const DEFAULT_PATH = 'images/id/mbes.png';
    private const DEPED_FILE_PREFIX = 'deped-logo.';
    private const DEPED_DEFAULT_PATH = 'images/id/deped.png';

    public static function path(): string
    {
        $storedPath = self::storedPath();

        return $storedPath ? Storage::disk('public')->path($storedPath) : public_path(self::DEFAULT_PATH);
    }

    public static function url(): string
    {
        return '/super-admin/school-logo/file?v=' . self::version(self::FILE_PREFIX);
    }

    public static function depedPath(): string
    {
        $storedPath = self::storedPath(self::DEPED_FILE_PREFIX);

        return $storedPath ? Storage::disk('public')->path($storedPath) : public_path(self::DEPED_DEFAULT_PATH);
    }

    public static function depedUrl(): string
    {
        return '/super-admin/school-logo/deped-file?v=' . self::version(self::DEPED_FILE_PREFIX);
    }

    public static function upload(UploadedFile $file): void
    {
        self::store($file, self::FILE_PREFIX);
    }

    public static function uploadDepEd(UploadedFile $file): void
    {
        self::store($file, self::DEPED_FILE_PREFIX);
    }

    public static function reset(): void
    {
        self::deleteStored(self::FILE_PREFIX);
    }

    public static function resetDepEd(): void
    {
        self::deleteStored(self::DEPED_FILE_PREFIX);
    }

    private static function store(UploadedFile $file, string $prefix): void
    {
        $disk = Storage::disk('public');
        $disk->makeDirectory(self::DIRECTORY);

        $extension = strtolower($file->getClientOriginalExtension());
        $filename = $prefix . bin2hex(random_bytes(8)) . '.' . $extension;
        $storedPath = $disk->putFileAs(self::DIRECTORY, $file, $filename);

        if ($storedPath === false || !$disk->exists($storedPath)) {
            throw new \RuntimeException('The logo could not be saved to application storage.');
        }

        self::deleteStored($prefix, $storedPath);
    }

    private static function deleteStored(string $prefix, ?string $except = null): void
    {
        $disk = Storage::disk('public');

        foreach ($disk->files(self::DIRECTORY) as $storedPath) {
            if ($storedPath !== $except && str_starts_with(basename($storedPath), $prefix)) {
                $disk->delete($storedPath);
            }
        }
    }

    private static function storedPath(string $prefix = self::FILE_PREFIX): ?string
    {
        $disk = Storage::disk('public');

        foreach ($disk->files(self::DIRECTORY) as $storedPath) {
            if (str_starts_with(basename($storedPath), $prefix)) {
                return $storedPath;
            }
        }

        return null;
    }

    private static function version(string $prefix): string
    {
        $storedPath = self::storedPath($prefix);

        if ($storedPath) {
            return substr(hash_file('sha256', Storage::disk('public')->path($storedPath)), 0, 16);
        }

        $defaultPath = $prefix === self::DEPED_FILE_PREFIX ? self::DEPED_DEFAULT_PATH : self::DEFAULT_PATH;

        return substr(hash_file('sha256', public_path($defaultPath)), 0, 16);
    }
}

<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SchoolLogoService
{
    private const DIRECTORY = 'settings';
    private const FILE_PREFIX = 'school-logo.';
    private const DEFAULT_PATH = 'images/id/mbes-logo-1.png';

    public static function path(): string
    {
        $storedPath = self::storedPath();

        return $storedPath ? Storage::disk('public')->path($storedPath) : public_path(self::DEFAULT_PATH);
    }

    public static function url(): string
    {
        return '/super-admin/school-logo/file';
    }

    public static function upload(UploadedFile $file): void
    {
        $disk = Storage::disk('public');
        $disk->makeDirectory(self::DIRECTORY);

        foreach ($disk->files(self::DIRECTORY) as $storedPath) {
            if (str_starts_with(basename($storedPath), self::FILE_PREFIX)) {
                $disk->delete($storedPath);
            }
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $disk->putFileAs(self::DIRECTORY, $file, self::FILE_PREFIX . $extension);
    }

    private static function storedPath(): ?string
    {
        $disk = Storage::disk('public');

        foreach ($disk->files(self::DIRECTORY) as $storedPath) {
            if (str_starts_with(basename($storedPath), self::FILE_PREFIX)) {
                return $storedPath;
            }
        }

        return null;
    }
}

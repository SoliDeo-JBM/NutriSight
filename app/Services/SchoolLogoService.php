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
        $mime = self::validatedImageMime($file);
        self::store($file, self::FILE_PREFIX, $schoolYearId, $mime);
    }

    public static function uploadDepEd(UploadedFile $file, ?int $schoolYearId = null): void
    {
        $mime = self::validatedImageMime($file);
        self::store($file, self::DEPED_FILE_PREFIX, $schoolYearId, $mime);
    }

    public static function reset(?int $schoolYearId = null): void
    {
        self::deleteStored(self::FILE_PREFIX, null, $schoolYearId);
    }

    public static function resetDepEd(?int $schoolYearId = null): void
    {
        self::deleteStored(self::DEPED_FILE_PREFIX, null, $schoolYearId);
    }

    private static function store(UploadedFile $file, string $prefix, ?int $schoolYearId, string $mime): void
    {
        $disk = Storage::disk('public');
        $disk->makeDirectory(self::DIRECTORY);

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        };
        $yearId = $schoolYearId ?? SchoolYearManager::activeSchoolYearId();
        $scopedPrefix = self::scopedPrefix($prefix, $yearId);
        $filename = $scopedPrefix . bin2hex(random_bytes(8)) . '.' . $extension;
        $storedPath = $disk->putFileAs(self::DIRECTORY, $file, $filename);

        if ($storedPath === false || !$disk->exists($storedPath)) {
            throw new \RuntimeException('The logo could not be saved to application storage.');
        }

        self::deleteStored($prefix, $storedPath, $yearId);
    }

    private static function validatedImageMime(UploadedFile $file): string
    {
        $invalidImageMessage = 'The selected file is not a valid JPEG, PNG, or WEBP image. Renamed or unsupported files are not accepted.';

        if (!$file->isValid()) {
            throw new \InvalidArgumentException($invalidImageMessage);
        }

        $contents = @file_get_contents($file->getRealPath());
        if (!is_string($contents) || $contents === '') {
            throw new \InvalidArgumentException($invalidImageMessage);
        }

        try {
            $imageInfo = @getimagesizefromstring($contents);
            $detectedMime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents);
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];

            if (!is_array($imageInfo)
                || !in_array($imageInfo['mime'] ?? null, $allowedMimes, true)
                || $detectedMime !== $imageInfo['mime']
                || ($imageInfo[0] * $imageInfo[1]) > 20000000
                || !function_exists('imagecreatefromstring')) {
                throw new \InvalidArgumentException($invalidImageMessage);
            }

            $decodedImage = @imagecreatefromstring($contents);
            if (!$decodedImage instanceof \GdImage) {
                throw new \InvalidArgumentException($invalidImageMessage);
            }

            imagedestroy($decodedImage);

            return $detectedMime;
        } catch (\InvalidArgumentException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new \InvalidArgumentException($invalidImageMessage, previous: $exception);
        }
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

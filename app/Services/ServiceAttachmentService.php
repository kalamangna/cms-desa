<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestAttachment;
use App\Models\ServiceRequirement;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceAttachmentService
{
    public const DISK = 'local';

    public const BASE_DIRECTORY = 'service-requests';

    public const MAX_FILE_SIZE_KB = 2048;

    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];

    public const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'application/pdf',
    ];

    // Resolusi tinggi agar teks KTP dan KK tetap tajam setelah kompresi WebP
    public const MAX_IMAGE_DIMENSION = 1600;

    public const WEBP_QUALITY = 82;

    public function storeUploadedFile(
        UploadedFile $file,
        ServiceRequest $serviceRequest,
        ServiceRequirement $requirement
    ): ServiceRequestAttachment {
        $directory = self::BASE_DIRECTORY.'/'.$serviceRequest->id;
        $mimeType = (string) ($file->getMimeType() ?: $file->getClientMimeType());
        $originalName = $this->sanitizeOriginalName($file->getClientOriginalName());

        if (str_starts_with($mimeType, 'image/')) {
            [$storedPath, $finalMime, $finalSize] = $this->storeCompressedImage($file, $directory);
        } else {
            [$storedPath, $finalMime, $finalSize] = $this->storeDocument($file, $directory);
        }

        return ServiceRequestAttachment::create([
            'service_request_id' => $serviceRequest->id,
            'service_requirement_id' => $requirement->id,
            'requirement_name' => $requirement->name,
            'file_path' => $storedPath,
            'original_name' => $originalName,
            'mime_type' => $finalMime,
            'file_size' => $finalSize,
        ]);
    }

    public function deleteAttachmentFile(ServiceRequestAttachment $attachment): void
    {
        $path = $this->normalizeSafeRelativePath($attachment->file_path);

        if ($path === null) {
            return;
        }

        $disk = Storage::disk(self::DISK);

        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }

    public function deleteRequestDirectory(int $serviceRequestId): void
    {
        $directory = self::BASE_DIRECTORY.'/'.$serviceRequestId;
        $disk = Storage::disk(self::DISK);

        if ($disk->exists($directory)) {
            $disk->deleteDirectory($directory);
        }
    }

    public function resolveSafeAbsolutePath(ServiceRequestAttachment $attachment): ?string
    {
        $path = $this->normalizeSafeRelativePath($attachment->file_path);

        if ($path === null) {
            return null;
        }

        $disk = Storage::disk(self::DISK);

        if (! $disk->exists($path)) {
            return null;
        }

        return $disk->path($path);
    }

    /**
     * @return array{0: string, 1: string, 2: int}
     */
    private function storeCompressedImage(UploadedFile $file, string $directory): array
    {
        $disk = Storage::disk(self::DISK);
        $extension = strtolower($file->getClientOriginalExtension());
        $realPath = $file->getRealPath();

        if ($realPath && function_exists('imagewebp') && function_exists('imagecreatetruecolor')) {
            $image = $this->createGdImage($realPath, $extension, (string) $file->getMimeType());

            if ($image !== null) {
                $origWidth = imagesx($image);
                $origHeight = imagesy($image);

                if ($origWidth > self::MAX_IMAGE_DIMENSION || $origHeight > self::MAX_IMAGE_DIMENSION) {
                    if ($origWidth >= $origHeight) {
                        $newWidth = self::MAX_IMAGE_DIMENSION;
                        $newHeight = max(1, (int) round(($origHeight / $origWidth) * self::MAX_IMAGE_DIMENSION));
                    } else {
                        $newHeight = self::MAX_IMAGE_DIMENSION;
                        $newWidth = max(1, (int) round(($origWidth / $origHeight) * self::MAX_IMAGE_DIMENSION));
                    }

                    $resized = imagecreatetruecolor($newWidth, $newHeight);
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                    imagedestroy($image);
                    $image = $resized;
                }

                $filename = Str::uuid().'.webp';
                $relativePath = $directory.'/'.$filename;
                $targetFullPath = $disk->path($relativePath);
                $targetDir = dirname($targetFullPath);

                if (! is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }

                $saved = @imagewebp($image, $targetFullPath, self::WEBP_QUALITY);
                imagedestroy($image);

                if ($saved && file_exists($targetFullPath)) {
                    return [$relativePath, 'image/webp', (int) filesize($targetFullPath)];
                }
            }
        }

        return $this->storeDocument($file, $directory);
    }

    /**
     * @return array{0: string, 1: string, 2: int}
     */
    private function storeDocument(UploadedFile $file, string $directory): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            $extension = 'pdf';
        }

        $filename = Str::uuid().'.'.$extension;
        $relativePath = $file->storeAs($directory, $filename, self::DISK);
        $disk = Storage::disk(self::DISK);

        $mimeType = (string) ($disk->mimeType($relativePath) ?: $file->getMimeType() ?: 'application/octet-stream');
        $fileSize = (int) ($disk->size($relativePath) ?: $file->getSize() ?: 0);

        return [$relativePath, $mimeType, $fileSize];
    }

    private function createGdImage(string $realPath, string $extension, string $mimeType): mixed
    {
        if ((in_array($extension, ['jpg', 'jpeg'], true) || $mimeType === 'image/jpeg') && function_exists('imagecreatefromjpeg')) {
            return @imagecreatefromjpeg($realPath) ?: null;
        }

        if (($extension === 'png' || $mimeType === 'image/png') && function_exists('imagecreatefrompng')) {
            $image = @imagecreatefrompng($realPath) ?: null;
            if ($image) {
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }

            return $image;
        }

        if (($extension === 'webp' || $mimeType === 'image/webp') && function_exists('imagecreatefromwebp')) {
            return @imagecreatefromwebp($realPath) ?: null;
        }

        return null;
    }

    private function sanitizeOriginalName(string $name): string
    {
        $base = basename($name);
        $clean = preg_replace('/[^\pL\pN._\-\s]/u', '', $base) ?: 'berkas';

        return Str::limit(trim($clean), 150, '');
    }

    private function normalizeSafeRelativePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        if (str_contains($normalized, '..') || ! str_starts_with($normalized, self::BASE_DIRECTORY.'/')) {
            return null;
        }

        return $normalized;
    }
}

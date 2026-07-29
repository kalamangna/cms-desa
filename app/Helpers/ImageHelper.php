<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Convert an uploaded image file or existing storage path to WebP format.
     *
     * @param UploadedFile|string $file UploadedFile instance or relative storage path
     * @param string $directory Storage subdirectory (e.g. 'posts', 'galleries', 'officials')
     * @param int $quality WebP quality (default: 80)
     * @return string|null Relative storage path of converted WebP file or original path if failed
     */
    public static function convertToWebp(mixed $file, string $directory = 'uploads', int $quality = 80): ?string
    {
        if (!$file) {
            return null;
        }

        // Handle UploadedFile instance directly
        if ($file instanceof UploadedFile) {
            $extension = strtolower($file->getClientOriginalExtension());
            $realPath = $file->getRealPath();
        } elseif (is_string($file)) {
            // Already a relative path in storage/app/public/
            $storagePath = storage_path('app/public/' . ltrim($file, '/'));
            if (!file_exists($storagePath)) {
                return $file;
            }
            $extension = strtolower(pathinfo($storagePath, PATHINFO_EXTENSION));
            $realPath = $storagePath;
            $directory = dirname(ltrim($file, '/'));
        } else {
            return null;
        }

        // If already WebP, save/return directly
        if ($extension === 'webp') {
            if ($file instanceof UploadedFile) {
                return $file->store($directory, 'public');
            }
            return $file;
        }

        // Check if GD PHP extension and imagewebp function are available
        if (!function_exists('imagewebp') || !function_exists('imagecreatetruecolor')) {
            if ($file instanceof UploadedFile) {
                return $file->store($directory, 'public');
            }
            return $file;
        }

        // Create GD image resource based on extension
        $image = null;
        if (in_array($extension, ['jpg', 'jpeg']) && function_exists('imagecreatefromjpeg')) {
            $image = @imagecreatefromjpeg($realPath);
        } elseif ($extension === 'png' && function_exists('imagecreatefrompng')) {
            $image = @imagecreatefrompng($realPath);
            if ($image) {
                // Preserve PNG transparency
                imagealphablending($image, false);
                imagesavealpha($image, true);
            }
        }

        if (!$image) {
            if ($file instanceof UploadedFile) {
                return $file->store($directory, 'public');
            }
            return $file;
        }

        // Generate unique WebP filename and full target path
        $filename = Str::uuid() . '.webp';
        $targetSubDir = trim($directory, '/');
        $targetDir = storage_path('app/public/' . $targetSubDir);

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetFullPath = $targetDir . '/' . $filename;

        // Resize image if width exceeds 800px to optimize mobile LCP image loading
        $origWidth = imagesx($image);
        $origHeight = imagesy($image);
        $maxDimension = 800;

        if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            if ($origWidth >= $origHeight) {
                $newWidth = $maxDimension;
                $newHeight = (int) round(($origHeight / $origWidth) * $maxDimension);
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int) round(($origWidth / $origHeight) * $maxDimension);
            }

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            if ($extension === 'png') {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
            }

            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            imagedestroy($image);
            $image = $resizedImage;
        }

        // Convert and save image to WebP
        $saved = @imagewebp($image, $targetFullPath, $quality);
        imagedestroy($image);

        if ($saved && file_exists($targetFullPath)) {
            // Delete original non-webp file if it was a stored file string
            if (is_string($file) && file_exists($realPath) && $realPath !== $targetFullPath) {
                @unlink($realPath);
            }
            return ($targetSubDir ? $targetSubDir . '/' : '') . $filename;
        }

        // Fallback to normal store if WebP conversion failed
        if ($file instanceof UploadedFile) {
            return $file->store($directory, 'public');
        }

        return $file;
    }
}

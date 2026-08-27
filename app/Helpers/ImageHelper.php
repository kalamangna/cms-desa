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
     * @param  UploadedFile|string  $file  UploadedFile instance or relative storage path
     * @param  string  $directory  Storage subdirectory (e.g. 'posts', 'galleries', 'officials')
     * @param  int  $quality  WebP quality (default: 80)
     * @param  int  $maxDimension  Maximum width/height dimension in pixels (default: 800)
     * @param  bool  $padPortraitToLandscape  Whether to auto-frame portrait photos into 16:9 canvas with blurred background
     * @return string|null Relative storage path of converted WebP file or original path if failed
     */
    public static function convertToWebp(
        mixed $file,
        string $directory = 'uploads',
        int $quality = 80,
        int $maxDimension = 800,
        bool $padPortraitToLandscape = false
    ): ?string {
        if (! $file) {
            return null;
        }

        // Handle UploadedFile instance directly
        if ($file instanceof UploadedFile) {
            $extension = strtolower($file->getClientOriginalExtension());
            $realPath = $file->getRealPath();
        } elseif (is_string($file)) {
            // Already a relative path in storage/app/public/
            $storagePath = storage_path('app/public/'.ltrim($file, '/'));
            if (! file_exists($storagePath)) {
                return $file;
            }
            $extension = strtolower(pathinfo($storagePath, PATHINFO_EXTENSION));
            $realPath = $storagePath;
            $directory = dirname(ltrim($file, '/'));
        } else {
            return null;
        }

        // Check if GD PHP extension and imagewebp function are available
        if (! function_exists('imagewebp') || ! function_exists('imagecreatetruecolor')) {
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
        } elseif ($extension === 'webp' && function_exists('imagecreatefromwebp')) {
            $image = @imagecreatefromwebp($realPath);
        }

        if (! $image) {
            if ($file instanceof UploadedFile) {
                return $file->store($directory, 'public');
            }

            return $file;
        }

        // Generate unique WebP filename and full target path
        $filename = Str::uuid().'.webp';
        $targetSubDir = trim($directory, '/');
        $targetDir = storage_path('app/public/'.$targetSubDir);

        if (! file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $targetFullPath = $targetDir.'/'.$filename;

        $origWidth = imagesx($image);
        $origHeight = imagesy($image);

        // Jika foto portrait dan diminta untuk diubah ke rasio 16:9 dengan latar belakang blur
        if ($padPortraitToLandscape && $origHeight > $origWidth) {
            $canvasH = min($origHeight, $maxDimension);
            $canvasW = (int) round($canvasH * (16 / 9));

            $canvas = imagecreatetruecolor($canvasW, $canvasH);

            // Buat background blur halus dan efisien dari thumbnail kecil
            $smallW = 64;
            $smallH = 36;
            $small = imagecreatetruecolor($smallW, $smallH);
            imagecopyresampled($small, $image, 0, 0, 0, 0, $smallW, $smallH, $origWidth, $origHeight);
            for ($i = 0; $i < 5; $i++) {
                imagefilter($small, IMG_FILTER_GAUSSIAN_BLUR);
            }
            imagecopyresampled($canvas, $small, 0, 0, 0, 0, $canvasW, $canvasH, $smallW, $smallH);
            imagedestroy($small);

            // Gelapkan sedikit background agar foto portrait utama lebih fokus dan kontras
            imagefilter($canvas, IMG_FILTER_BRIGHTNESS, -20);

            // Letakkan foto portrait asli di tengah secara proporsional dan tajam
            $fgH = $canvasH;
            $fgW = (int) round(($origWidth / $origHeight) * $fgH);
            $offsetX = (int) round(($canvasW - $fgW) / 2);
            imagecopyresampled($canvas, $image, $offsetX, 0, 0, 0, $fgW, $fgH, $origWidth, $origHeight);

            imagedestroy($image);
            $image = $canvas;
        } elseif ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            // Resize jika melebihi batas dimensi maksimal
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
        } elseif ($extension === 'webp' && ! ($file instanceof UploadedFile) && $realPath === $targetFullPath) {
            // File webp yang sudah ada dan tidak diubah
            imagedestroy($image);

            return ($targetSubDir ? $targetSubDir.'/' : '').$filename;
        }

        // Convert and save image to WebP
        $saved = @imagewebp($image, $targetFullPath, $quality);
        imagedestroy($image);

        if ($saved && file_exists($targetFullPath)) {
            // Delete original non-webp file if it was a stored file string
            if (is_string($file) && file_exists($realPath) && $realPath !== $targetFullPath) {
                @unlink($realPath);
            }

            return ($targetSubDir ? $targetSubDir.'/' : '').$filename;
        }

        // Fallback to normal store if WebP conversion failed
        if ($file instanceof UploadedFile) {
            return $file->store($directory, 'public');
        }

        return $file;
    }
}

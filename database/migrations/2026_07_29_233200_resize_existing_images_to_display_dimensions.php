<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Resize existing images to match their actual display dimensions:
     *  - officials/        → max 500px  (displayed at ~466px)
     *  - settings/         → max 700px  (popup infografis, displayed at ~662px)
     */
    public function up(): void
    {
        if (
            ! function_exists('imagecreatefromwebp') ||
            ! function_exists('imagewebp') ||
            ! function_exists('imagecreatetruecolor')
        ) {
            return;
        }

        $rules = [
            'officials' => 500,
            'settings' => 700,
        ];

        foreach ($rules as $subDir => $maxDimension) {
            $dir = storage_path('app/public/'.$subDir);
            if (! is_dir($dir)) {
                continue;
            }

            $files = glob($dir.'/*.webp') ?: [];

            foreach ($files as $path) {
                $this->resizeWebp($path, $maxDimension);
            }
        }

        try {
            Cache::flush();
        } catch (Throwable $e) {
            // Ignore
        }
    }

    /**
     * Resize a single WebP file in-place to fit within $maxDimension on its longest edge.
     * Skips files already within the target dimension.
     */
    private function resizeWebp(string $path, int $maxDimension, int $quality = 80): void
    {
        try {
            $img = @imagecreatefromwebp($path);
            if (! $img) {
                return;
            }

            $w = imagesx($img);
            $h = imagesy($img);

            // Skip if already within limits
            if ($w <= $maxDimension && $h <= $maxDimension) {
                imagedestroy($img);

                return;
            }

            // Calculate proportional new dimensions
            if ($w >= $h) {
                $nw = $maxDimension;
                $nh = (int) round(($h / $w) * $maxDimension);
            } else {
                $nh = $maxDimension;
                $nw = (int) round(($w / $h) * $maxDimension);
            }

            $resized = imagecreatetruecolor($nw, $nh);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);

            imagecopyresampled($resized, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
            imagedestroy($img);

            @imagewebp($resized, $path, $quality);
            imagedestroy($resized);
        } catch (Throwable $e) {
            // Skip problematic files silently
        }
    }

    public function down(): void
    {
        // Irreversible — original files are overwritten in-place
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations to resize all existing WebP images in storage/app/public to max 800px width.
     */
    public function up(): void
    {
        if (!function_exists('imagewebp') || !function_exists('imagecreatetruecolor') || !function_exists('imagecreatefromwebp')) {
            return;
        }

        $baseDir = storage_path('app/public');
        if (!file_exists($baseDir)) {
            return;
        }

        try {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($files as $file) {
                if ($file->isDir()) {
                    continue;
                }

                $ext = strtolower($file->getExtension());
                if ($ext !== 'webp') {
                    continue;
                }

                $path = $file->getPathname();
                $img = @imagecreatefromwebp($path);
                if (!$img) {
                    continue;
                }

                $w = imagesx($img);
                $h = imagesy($img);
                $maxW = 800;

                if ($w > $maxW || $h > $maxW) {
                    if ($w >= $h) {
                        $nw = $maxW;
                        $nh = (int) round(($h / $w) * $maxW);
                    } else {
                        $nh = $maxW;
                        $nw = (int) round(($w / $h) * $maxW);
                    }

                    $newImg = imagecreatetruecolor($nw, $nh);
                    imagealphablending($newImg, false);
                    imagesavealpha($newImg, true);

                    imagecopyresampled($newImg, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
                    imagedestroy($img);

                    @imagewebp($newImg, $path, 78);
                    imagedestroy($newImg);
                } else {
                    imagedestroy($img);
                }
            }
        } catch (\Throwable $e) {
            // Ignore error
        }

        try {
            \Illuminate\Support\Facades\Cache::flush();
        } catch (\Throwable $e) {
            // Ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};

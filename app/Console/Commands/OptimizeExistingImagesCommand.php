<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeExistingImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:optimize-images {--max-width=1200 : Lebar maksimal gambar (px)} {--quality=80 : Kualitas kompresi (1-100)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kompresi dan resize otomatis seluruh foto lama yang sudah terlanjur diunggah di folder storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!extension_loaded('gd')) {
            $this->error('Ekstensi PHP GD tidak ditemukan pada server.');
            return 1;
        }

        $maxWidth = (int) $this->option('max-width');
        $quality = (int) $this->option('quality');

        $this->info("Memulai optimasi & kompresi foto lama (Max Width: {$maxWidth}px, Quality: {$quality}%)...");

        $disk = Storage::disk('public');
        $files = $disk->allFiles();

        $imageExtensions = ['jpg', 'jpeg', 'png'];
        $processedCount = 0;
        $savedBytes = 0;

        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (!in_array($extension, $imageExtensions)) {
                continue;
            }

            $fullPath = $disk->path($file);
            if (!file_exists($fullPath)) {
                continue;
            }

            $initialSize = filesize($fullPath);

            // Dapatkan informasi gambar
            $imageInfo = @getimagesize($fullPath);
            if (!$imageInfo) {
                continue;
            }

            list($origWidth, $origHeight, $type) = $imageInfo;

            // Jika ukuran fisik kecil dan lebar sudah di bawah limit, lewati atau resize jika terlalu lebar
            $newWidth = $origWidth;
            $newHeight = $origHeight;

            if ($origWidth > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int) round(($origHeight / $origWidth) * $maxWidth);
            }

            // Buat GD Image Resource
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $srcImg = @imagecreatefromjpeg($fullPath);
                    break;
                case IMAGETYPE_PNG:
                    $srcImg = @imagecreatefrompng($fullPath);
                    break;
                default:
                    $srcImg = null;
            }

            if (!$srcImg) {
                continue;
            }

            // Inisialisasi canvas baru
            $dstImg = imagecreatetruecolor($newWidth, $newHeight);

            // Pertahankan transparansi PNG jika ada
            if ($type === IMAGETYPE_PNG) {
                imagealphablending($dstImg, false);
                imagesavealpha($dstImg, true);
                $transparent = imagecolorallocatealpha($dstImg, 255, 255, 255, 127);
                imagefilledrectangle($dstImg, 0, 0, $newWidth, $newHeight, $transparent);
            }

            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

            // Simpan kembali gambar terkompresi
            if ($type === IMAGETYPE_JPEG) {
                imagejpeg($dstImg, $fullPath, $quality);
            } elseif ($type === IMAGETYPE_PNG) {
                // Konversi kualitas 1-100 ke 0-9 untuk PNG
                $pngQuality = (int) round((100 - $quality) / 10);
                imagepng($dstImg, $fullPath, $pngQuality);
            }

            imagedestroy($srcImg);
            imagedestroy($dstImg);

            clearstatcache(true, $fullPath);
            $newSize = filesize($fullPath);

            if ($newSize < $initialSize) {
                $savedBytes += ($initialSize - $newSize);
                $processedCount++;
            }
        }

        $savedMB = round($savedBytes / 1024 / 1024, 2);
        $this->info("Selesai! Berhasil mengompresi {$processedCount} foto lama.");
        $this->info("Hemat ruang penyimpanan & transfer data: {$savedMB} MB.");

        return 0;
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pastikan ekstensi GD dan fungsi imagewebp tersedia
        if (!function_exists('imagewebp') || !function_exists('imagecreatetruecolor')) {
            Log::warning('Migrasi WebP dilewati: Ekstensi GD PHP / imagewebp tidak aktif pada server ini.');
            return;
        }

        $baseDir = storage_path('app/public');
        if (!file_exists($baseDir)) {
            return;
        }

        // 1. Konversi berkas fisik gambar di folder storage/app/public
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
                if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    continue;
                }

                $path = $file->getPathname();
                $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $path);

                // Jika file .webp sudah ada, lewati agar hemat proses
                if (file_exists($webpPath)) {
                    continue;
                }

                $img = null;
                if ($ext === 'png' && function_exists('imagecreatefrompng')) {
                    $img = @imagecreatefrompng($path);
                } elseif (in_array($ext, ['jpg', 'jpeg']) && function_exists('imagecreatefromjpeg')) {
                    $img = @imagecreatefromjpeg($path);
                }

                if (!$img) {
                    continue;
                }

                $w = imagesx($img);
                $h = imagesy($img);

                // Target maks lebar 1200px (jika lebih besar, resize secara proporsional)
                $maxW = 1200;
                if ($w > $maxW) {
                    $nh = (int) ($h * ($maxW / $w));
                    $newImg = imagecreatetruecolor($maxW, $nh);

                    if ($ext === 'png') {
                        imagealphablending($newImg, false);
                        imagesavealpha($newImg, true);
                    }

                    imagecopyresampled($newImg, $img, 0, 0, 0, 0, $maxW, $nh, $w, $h);
                    imagedestroy($img);
                    $img = $newImg;
                }

                @imagewebp($img, $webpPath, 82);
                imagedestroy($img);
            }
        } catch (\Throwable $e) {
            Log::error('Gagal mengonversi gambar fisik di migrasi WebP: ' . $e->getMessage());
        }

        // 2. Pembaruan tautan path gambar di database secara aman
        $tablesAndColumns = [
            'posts' => ['featured_image'],
            'officials' => ['photo'],
            'popup_infographics' => ['image'],
            'galleries' => ['image'],
            'village_potentials' => ['image'],
            'institutions' => ['logo'],
            'publications' => ['cover'],
            'site_settings' => ['value'],
        ];

        foreach ($tablesAndColumns as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    continue;
                }

                try {
                    // Ganti ekstensi .jpg, .jpeg, .png di database ke .webp jika berkas webp fisiknya ada atau diganti
                    DB::table($table)
                        ->where($column, 'LIKE', '%.jpg')
                        ->orWhere($column, 'LIKE', '%.jpeg')
                        ->orWhere($column, 'LIKE', '%.png')
                        ->chunkById(100, function ($rows) use ($table, $column) {
                            foreach ($rows as $row) {
                                $oldValue = $row->{$column};
                                if (!$oldValue) continue;

                                $newValue = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $oldValue);
                                if ($newValue !== $oldValue) {
                                    DB::table($table)
                                        ->where('id', $row->id)
                                        ->update([$column => $newValue]);
                                }
                            }
                        });
                } catch (\Throwable $e) {
                    Log::warning("Gagal memperbarui tabel {$table} kolom {$column} pada migrasi WebP: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu rollback fisik agar tidak menghapus gambar webp yang terkompresi
    }
};


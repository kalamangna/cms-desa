<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
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
        if (! function_exists('imagewebp') || ! function_exists('imagecreatetruecolor')) {
            Log::warning('Migrasi WebP dilewati: Ekstensi GD PHP / imagewebp tidak aktif pada server ini.');

            return;
        }

        $baseDir = storage_path('app/public');
        if (! file_exists($baseDir)) {
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
                if (! in_array($ext, ['jpg', 'jpeg', 'png'])) {
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

                if (! $img) {
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

                if (@imagewebp($img, $webpPath, 82)) {
                    imagedestroy($img);
                    if ($path !== $webpPath && file_exists($webpPath)) {
                        @unlink($path);
                    }
                }
            }
        } catch (Throwable $e) {
            Log::error('Gagal mengonversi gambar fisik di migrasi WebP: '.$e->getMessage());
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
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                try {
                    // 2a. Ganti ekstensi .jpg, .jpeg, .png di database ke .webp JIKA file .webp fisiknya benar-benar ada di storage
                    $rowsToWebp = DB::table($table)
                        ->where(function ($q) use ($column) {
                            $q->where($column, 'LIKE', '%.jpg')
                                ->orWhere($column, 'LIKE', '%.jpeg')
                                ->orWhere($column, 'LIKE', '%.png');
                        })
                        ->get();

                    foreach ($rowsToWebp as $row) {
                        $oldValue = $row->{$column};
                        if (! $oldValue) {
                            continue;
                        }

                        $newValue = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $oldValue);
                        if ($newValue !== $oldValue) {
                            $fullWebpPath = storage_path('app/public/'.ltrim($newValue, '/'));
                            $publicWebpPath = public_path('storage/'.ltrim($newValue, '/'));
                            // Pastikan file .webp fisiknya memang tersedia di storage_path atau public_path sebelum mengupdate DB
                            if (file_exists($fullWebpPath) || file_exists($publicWebpPath)) {
                                DB::table($table)
                                    ->where('id', $row->id)
                                    ->update([$column => $newValue]);
                            }
                        }
                    }

                    // 2b. Jika tautan di DB sudah .webp tapi file .webp fisiknya TIDAK ADA di storage
                    $rowsFromWebp = DB::table($table)
                        ->where($column, 'LIKE', '%.webp')
                        ->get();

                    foreach ($rowsFromWebp as $row) {
                        $val = $row->{$column};
                        if (! $val) {
                            continue;
                        }

                        $fullWebpPath = storage_path('app/public/'.ltrim($val, '/'));
                        if (! file_exists($fullWebpPath)) {
                            $basePathWithoutExt = preg_replace('/\.webp$/i', '', $val);
                            $foundExt = null;

                            // Coba konversi file asli jika ada
                            foreach (['.jpg', '.jpeg', '.png', '.JPG', '.JPEG', '.PNG'] as $testExt) {
                                $origFile = storage_path('app/public/'.ltrim($basePathWithoutExt.$testExt, '/'));
                                if (file_exists($origFile)) {
                                    $img = null;
                                    $lowerExt = strtolower($testExt);
                                    if (in_array($lowerExt, ['.jpg', '.jpeg']) && function_exists('imagecreatefromjpeg')) {
                                        $img = @imagecreatefromjpeg($origFile);
                                    } elseif ($lowerExt === '.png' && function_exists('imagecreatefrompng')) {
                                        $img = @imagecreatefrompng($origFile);
                                    }

                                    if ($img) {
                                        @imagewebp($img, $fullWebpPath, 82);
                                        imagedestroy($img);
                                        $foundExt = 'converted_to_webp';
                                        break;
                                    } else {
                                        $foundExt = $basePathWithoutExt.$testExt;
                                        break;
                                    }
                                }
                            }

                            // Jika gagal/tidak ada file fisik WebP dan konversi gagal, kembalikan tautan DB
                            if ($foundExt !== 'converted_to_webp') {
                                $targetValue = $foundExt ?: ($basePathWithoutExt.'.jpg');
                                DB::table($table)
                                    ->where('id', $row->id)
                                    ->update([$column => $targetValue]);
                            }
                        }
                    }
                } catch (Throwable $e) {
                    Log::warning("Gagal memperbarui tabel {$table} kolom {$column} pada migrasi WebP: ".$e->getMessage());
                }
            }
        }

        try {
            Cache::flush();
        } catch (Throwable $e) {
            // Ignore cache error if cache driver fails
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

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:compress-post-images', function () {
    $this->info('Memulai kompresi gambar berita di server...');
    
    // 1. Jalankan perbaikan skema database jika kolom photo masih ada
    if (\Illuminate\Support\Facades\Schema::hasTable('posts')) {
        if (\Illuminate\Support\Facades\Schema::hasColumn('posts', 'photo') && !\Illuminate\Support\Facades\Schema::hasColumn('posts', 'featured_image')) {
            $this->comment('Mendeteksi database masih menggunakan kolom "photo". Mengubah nama kolom ke "featured_image"...');
            try {
                \Illuminate\Support\Facades\Schema::table('posts', function ($table) {
                    $table->renameColumn('photo', 'featured_image');
                });
                $this->info('Sukses mengubah kolom database menjadi "featured_image"!');
            } catch (\Exception $e) {
                $this->error('Gagal mengubah nama kolom database: ' . $e->getMessage());
            }
        }
    }
    
    // 2. Ambil seluruh data post
    $posts = \App\Models\Post::all();
    
    if ($posts->isEmpty()) {
        $this->comment('Tidak ada berita ditemukan.');
        return;
    }
    
    foreach ($posts as $post) {
        // Ambil nama berkas gambar dari model (featured_image) atau direct attribute fallback (photo)
        $imageName = $post->featured_image ?: ($post->photo ?? null);
        
        if (!$imageName) {
            $this->comment("Abaikan: Post ID {$post->id} ('{$post->title}') tidak memiliki gambar.");
            continue;
        }
        
        $possiblePaths = [
            storage_path('app/public/' . $imageName),
            public_path('storage/' . $imageName),
            base_path('../public_html/storage/' . $imageName),
            base_path('../public/storage/' . $imageName),
            base_path('../storage/' . $imageName),
        ];
        
        $path = null;
        foreach ($possiblePaths as $p) {
            if (file_exists($p)) {
                $path = $p;
                break;
            }
        }
        
        if (!$path) {
            $this->warn("File tidak ditemukan: {$imageName} di seluruh direktori pencarian.");
            continue;
        }
        
        $size = filesize($path);
        if ($size <= 300 * 1024) {
            $this->comment("Abaikan: {$imageName} (ukuran sudah " . round($size / 1024) . " KB)");
            continue;
        }
        
        $this->info("Mengompres: {$imageName} (" . round($size / 1024 / 1024, 2) . " MB)...");
        
        // Kompres menggunakan GD
        try {
            $info = getimagesize($path);
            $mime = $info['mime'] ?? '';
            
            if ($mime === 'image/jpeg') {
                $image = imagecreatefromjpeg($path);
            } elseif ($mime === 'image/png') {
                $image = imagecreatefrompng($path);
            } else {
                $this->warn("Tipe berkas tidak didukung: {$mime}");
                continue;
            }
            
            // Resize jika lebar > 1200px (menjaga aspek rasio tanpa memotong)
            $width = imagesx($image);
            $height = imagesy($image);
            
            if ($width > 1200) {
                $newWidth = 1200;
                $newHeight = ($height / $width) * $newWidth;
                
                $tmp = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($tmp, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $tmp;
            }
            
            // Simpan kembali dengan kompresi JPEG quality 75
            imagejpeg($image, $path, 75);
            imagedestroy($image);
            
            clearstatcache();
            $newSize = filesize($path);
            $this->info("Sukses! Ukuran baru: " . round($newSize / 1024) . " KB");
        } catch (\Exception $e) {
            $this->error("Gagal mengompres {$imageName}: " . $e->getMessage());
        }
    }
    
    $this->info('Proses kompresi selesai!');
})->purpose('Mengompres seluruh gambar berita di server yang ukurannya melebihi 300 KB');

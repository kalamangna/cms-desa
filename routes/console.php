<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:compress-post-images', function () {
    $this->info('Memulai kompresi gambar berita di server...');
    
    $posts = \App\Models\Post::whereNotNull('featured_image')->get();
    
    foreach ($posts as $post) {
        $path = storage_path('app/public/' . $post->featured_image);
        if (!file_exists($path)) {
            $this->warn("File tidak ditemukan: {$post->featured_image}");
            continue;
        }
        
        $size = filesize($path);
        if ($size <= 300 * 1024) {
            $this->comment("Abaikan: {$post->featured_image} (ukuran sudah " . round($size / 1024) . " KB)");
            continue;
        }
        
        $this->info("Mengompres: {$post->featured_image} (" . round($size / 1024 / 1024, 2) . " MB)...");
        
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
            
            // Resize jika lebar > 1200px
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
            $this->error("Gagal mengompres {$post->featured_image}: " . $e->getMessage());
        }
    }
    
    $this->info('Proses kompresi selesai!');
})->purpose('Mengompres seluruh gambar berita di server yang ukurannya melebihi 300 KB');

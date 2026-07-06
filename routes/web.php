<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\APBDesController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InstitutionController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/robots.txt', [SitemapController::class, 'robots']);

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/profil', [PageController::class, 'profil'])->name('profil');
Route::get('/layanan', [PageController::class, 'layanan'])->name('layanan');
Route::get('/kontak', [PageController::class, 'kontak'])->name('kontak');
Route::get('/peta', [\App\Http\Controllers\MapController::class, 'index'])->name('peta.index');

Route::get('/aparatur', [OfficialController::class, 'index'])->name('officials.index');
Route::get('/lembaga', [InstitutionController::class, 'index'])->name('institutions.index');

Route::get('/berita', [PostController::class, 'index'])->name('posts.index');
Route::get('/berita/{slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');

Route::get('/galeri', [GalleryController::class, 'index'])->name('galleries.index');
Route::get('/dokumen', [DocumentController::class, 'index'])->name('documents.index');

Route::get('/statistik', [StatisticController::class, 'index'])->name('statistics.index');

Route::get('/dataset', [DatasetController::class, 'index'])->name('datasets.index');
Route::get('/dataset/download/{type}', [DatasetController::class, 'download'])->name('datasets.download');

Route::get('/publikasi', [PublicationController::class, 'index'])->name('publications.index');

Route::get('/apbdes', [APBDesController::class, 'index'])->name('apbdes.index');

Route::get('/init-link', function () {
    $src = storage_path('app/public');
    $dst = public_path('storage');
    
    // Pastikan folder public/storage dibuat sebagai folder fisik
    if (!file_exists($dst)) {
        if (!@mkdir($dst, 0755, true)) {
            return "Gagal membuat folder public/storage fisik. Harap buat folder tersebut secara manual melalui cPanel File Manager dengan hak akses 755.";
        }
    }
    
    // Fungsi rekursif menyalin isi folder
    $copyRecursive = function ($src, $dst) use (&$copyRecursive) {
        if (!file_exists($src)) return;
        $dir = opendir($src);
        @mkdir($dst, 0755, true);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $copyRecursive($src . '/' . $file, $dst . '/' . $file);
                } else {
                    @copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    };
    
    $copyRecursive($src, $dst);
    
    return "Berhasil menyalin data dummy & media ke folder fisik public/storage!";
})->middleware('auth');

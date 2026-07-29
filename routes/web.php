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

// Route untuk SEO & Robots
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/robots.txt', [SitemapController::class, 'robots']);

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/profil', [PageController::class, 'profil'])->name('profil');
Route::get('/layanan', [PageController::class, 'layanan'])->name('layanan');
Route::post('/layanan/ajukan', [\App\Http\Controllers\ServiceRequestController::class, 'store'])->name('service-requests.store');
Route::get('/layanan/lacak', [\App\Http\Controllers\ServiceRequestController::class, 'track'])->name('service-requests.track');

Route::get('/kontak', [PageController::class, 'kontak'])->name('kontak');
Route::get('/potensi', [PageController::class, 'potensi'])->name('potensi');
Route::get('/peta', [\App\Http\Controllers\MapController::class, 'index'])->name('peta.index');

Route::get('/buku-tamu', [\App\Http\Controllers\GuestBookController::class, 'index'])->name('guest-book.index');
Route::post('/buku-tamu', [\App\Http\Controllers\GuestBookController::class, 'store'])->name('guest-book.store');

Route::get('/pengaduan', [\App\Http\Controllers\ComplaintController::class, 'index'])->name('complaints.index');
Route::post('/pengaduan', [\App\Http\Controllers\ComplaintController::class, 'store'])->name('complaints.store');
Route::get('/pengaduan/lacak', [\App\Http\Controllers\ComplaintController::class, 'track'])->name('complaints.track');

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

Route::get('/init', function () {
    if (! auth()->user()?->hasRole('super_admin')) {
        abort(403, 'Akses ditolak. Hanya Super Admin yang dapat mengakses perbaikan media storage ini.');
    }

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
    
    // Perbaiki tautan 404 di DB jika file .webp fisik tidak ada tetapi tautan di DB sudah terlanjur ber-ekstensi .webp
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

    $revertedCount = 0;
    foreach ($tablesAndColumns as $table => $columns) {
        if (!\Illuminate\Support\Facades\Schema::hasTable($table)) continue;
        foreach ($columns as $column) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn($table, $column)) continue;

            $rows = \Illuminate\Support\Facades\DB::table($table)
                ->where($column, 'LIKE', '%.webp')
                ->get();

            foreach ($rows as $row) {
                $val = $row->{$column};
                if (!$val) continue;

                $fullWebpPath = storage_path('app/public/' . ltrim($val, '/'));
                if (!file_exists($fullWebpPath)) {
                    // Cari file alternatif .jpg, .jpeg, atau .png di storage
                    $basePathWithoutExt = preg_replace('/\.webp$/i', '', $val);
                    $foundOriginalExt = null;
                    foreach (['.jpg', '.jpeg', '.png'] as $testExt) {
                        if (file_exists(storage_path('app/public/' . ltrim($basePathWithoutExt . $testExt, '/')))) {
                            $foundOriginalExt = $basePathWithoutExt . $testExt;
                            break;
                        }
                    }

                    if ($foundOriginalExt) {
                        \Illuminate\Support\Facades\DB::table($table)
                            ->where('id', $row->id)
                            ->update([$column => $foundOriginalExt]);
                        $revertedCount++;
                    }
                }
            }
        }
    }

    return "Berhasil menyalin data media ke folder fisik public/storage! (Memperbaiki {$revertedCount} tautan gambar 404 ke format asli)";
})->middleware('auth');

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
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/robots.txt', [SitemapController::class, 'robots']);

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/profil', [PageController::class, 'profil'])->name('pages.profil');
Route::get('/layanan', [PageController::class, 'layanan'])->name('pages.layanan');
Route::get('/kontak', [PageController::class, 'kontak'])->name('pages.kontak');

Route::get('/pemerintahan', [OfficialController::class, 'index'])->name('officials.index');

Route::get('/berita', [PostController::class, 'index'])->name('posts.index');
Route::get('/berita/{slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/galeri', [GalleryController::class, 'index'])->name('galleries.index');
Route::get('/dokumen', [DocumentController::class, 'index'])->name('documents.index');

Route::get('/statistik', [StatisticController::class, 'index'])->name('statistics.index');

Route::get('/dataset', [DatasetController::class, 'index'])->name('datasets.index');

Route::get('/publikasi', [PublicationController::class, 'index'])->name('publications.index');

Route::get('/apbdes', [APBDesController::class, 'index'])->name('apbdes.index');

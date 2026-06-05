<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\OfficialController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\APBDesController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/robots.txt', [SitemapController::class, 'robots']);

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/pemerintahan', [OfficialController::class, 'index'])->name('officials.index');

Route::get('/berita', [PostController::class, 'index'])->name('posts.index');
Route::get('/berita/{slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/statistik', [StatisticController::class, 'index'])->name('statistics.index');

Route::get('/dataset', [DatasetController::class, 'index'])->name('datasets.index');

Route::get('/publikasi', [PublicationController::class, 'index'])->name('publications.index');

Route::get('/apbdes', [APBDesController::class, 'index'])->name('apbdes.index');

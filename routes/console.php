<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:sync-production', function () {
    $this->info('Memulai sinkronisasi database produksi secara aman...');

    // 1. Tambahkan kolom softDeletes jika belum ada
    $tables = [
        'budget_categories', 
        'budget_realizations', 
        'categories', 
        'datasets', 
        'metadata', 
        'publications'
    ];

    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            if (!Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function ($tableObj) {
                    $tableObj->softDeletes();
                });
                $this->comment("Kolom 'deleted_at' ditambahkan pada tabel: {$table}");
            } else {
                $this->info("Tabel {$table} sudah memiliki kolom 'deleted_at'.");
            }
        } else {
            $this->warn("Tabel {$table} tidak ditemukan di database.");
        }
    }

    // 2. Bersihkan tabel migrations dan daftarkan migrasi modular baru
    if (Schema::hasTable('migrations')) {
        DB::table('migrations')->truncate();
        
        $migrations = [
            '0001_01_01_000000_create_users_table',
            '0001_01_01_000001_create_cache_table',
            '0001_01_01_000002_create_jobs_table',
            '2026_06_04_050325_create_permission_tables',
            '2026_06_04_050903_create_settings_table',
            '2026_06_04_051000_create_village_features_tables',
            '2026_06_04_051100_create_news_tables',
            '2026_06_04_051200_create_open_data_tables',
            '2026_06_04_051300_create_budget_tables',
            '2026_07_01_151000_create_demography_tables',
            '2026_07_01_151100_create_statistics_tables',
            '2026_07_03_192000_create_visitor_logs_table',
        ];

        foreach ($migrations as $migration) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => 1
            ]);
        }
        $this->info('Riwayat migrasi (migrations table) berhasil diperbarui ke skema modular baru.');
    }

    $this->info('Sinkronisasi database produksi selesai dengan aman! Semua data asli Anda tetap utuh.');
})->purpose('Sinkronisasi riwayat migrasi dan penambahan kolom softDeletes secara aman di server produksi tanpa merusak data asli');

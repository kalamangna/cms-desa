<?php

namespace Database\Seeders;

use App\Models\BudgetCategory;
use App\Models\BudgetRealization;
use App\Models\Dataset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApbdesAndDatasetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membersihkan dan membuat data APBDes serta Open Data (Dataset)...');

        // Membersihkan data lama
        BudgetRealization::query()->forceDelete();
        BudgetCategory::query()->forceDelete();
        Dataset::query()->forceDelete();

        // 1. APBDes (Kategori)
        $catPendapatan = BudgetCategory::create(['name' => 'Pendapatan', 'slug' => 'pendapatan']);
        $catBelanja = BudgetCategory::create(['name' => 'Belanja', 'slug' => 'belanja']);
        $catPembiayaan = BudgetCategory::create(['name' => 'Pembiayaan', 'slug' => 'pembiayaan']);

        // 1a. APBDes (Realisasi Pendapatan)
        $pendapatanData = [
            ['title' => 'Pendapatan Asli Desa (PADes)', 'budget_amount' => 150000000, 'realization_amount' => 145000000],
            ['title' => 'Dana Desa (DD)', 'budget_amount' => 850000000, 'realization_amount' => 850000000],
            ['title' => 'Alokasi Dana Desa (ADD)', 'budget_amount' => 450000000, 'realization_amount' => 450000000],
            ['title' => 'Bagi Hasil Pajak & Retribusi', 'budget_amount' => 75000000, 'realization_amount' => 70000000],
            ['title' => 'Bantuan Keuangan Provinsi', 'budget_amount' => 130000000, 'realization_amount' => 130000000],
        ];

        $currentYear = date('Y');

        foreach ($pendapatanData as $p) {
            BudgetRealization::create([
                'budget_category_id' => $catPendapatan->id,
                'title' => $p['title'],
                'year' => $currentYear,
                'budget_amount' => $p['budget_amount'],
                'realization_amount' => $p['realization_amount'],
            ]);
        }

        // 1b. APBDes (Realisasi Belanja)
        $belanjaData = [
            ['title' => 'Penyelenggaraan Pemerintahan Desa', 'budget_amount' => 480000000, 'realization_amount' => 450000000],
            ['title' => 'Pelaksanaan Pembangunan Desa', 'budget_amount' => 650000000, 'realization_amount' => 600000000],
            ['title' => 'Pembinaan Kemasyarakatan', 'budget_amount' => 120000000, 'realization_amount' => 110000000],
            ['title' => 'Pemberdayaan Masyarakat', 'budget_amount' => 150000000, 'realization_amount' => 135000000],
            ['title' => 'Penanggulangan Bencana, Darurat & Mendesak', 'budget_amount' => 100000000, 'realization_amount' => 80000000],
        ];

        foreach ($belanjaData as $b) {
            BudgetRealization::create([
                'budget_category_id' => $catBelanja->id,
                'title' => $b['title'],
                'year' => $currentYear,
                'budget_amount' => $b['budget_amount'],
                'realization_amount' => $b['realization_amount'],
            ]);
        }

        // 1c. APBDes (Realisasi Pembiayaan)
        $pembiayaanData = [
            ['title' => 'Penerimaan Pembiayaan (SILPA Tahun Sebelumnya)', 'budget_amount' => 50000000, 'realization_amount' => 50000000],
            ['title' => 'Pengeluaran Pembiayaan (Penyertaan Modal BUMDes)', 'budget_amount' => 100000000, 'realization_amount' => 100000000],
        ];

        foreach ($pembiayaanData as $pm) {
            BudgetRealization::create([
                'budget_category_id' => $catPembiayaan->id,
                'title' => $pm['title'],
                'year' => $currentYear,
                'budget_amount' => $pm['budget_amount'],
                'realization_amount' => $pm['realization_amount'],
            ]);
        }

        // 2. Open Data (Dataset)
        $datasets = [
            [
                'title' => 'Data Demografi Warga (Publik)',
                'description' => 'Dataset terbuka yang berisi data statistik kependudukan warga desa yang aktif.',
                'year' => $currentYear,
                'source' => 'system',
                'source_table' => 'citizens',
                'selected_columns' => ['nik', 'name', 'gender', 'religion', 'education', 'job'],
            ],
            [
                'title' => 'Data Keluarga Prasejahtera',
                'description' => 'Dataset keluarga dengan kriteria bangunan atau pendapatan yang memerlukan bantuan.',
                'year' => $currentYear,
                'source' => 'system',
                'source_table' => 'families',
                'selected_columns' => ['kk_number', 'head_name', 'address', 'building_type'],
            ],
        ];

        foreach ($datasets as $ds) {
            $ds['slug'] = Str::slug($ds['title']);
            Dataset::create($ds);
        }

        $this->command->info('Berhasil menyuntikkan data APBDes dan Dataset Terbuka.');
    }
}

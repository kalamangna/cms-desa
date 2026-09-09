<?php

namespace Database\Seeders;

use App\Models\BudgetCategory;
use App\Models\BudgetRealization;
use Illuminate\Database\Seeder;

class ApbdesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membersihkan dan membuat data APBDes...');

        // Membersihkan data lama APBDes
        BudgetRealization::query()->forceDelete();
        BudgetCategory::query()->forceDelete();

        // 1. APBDes (Kategori)
        $catPendapatan = BudgetCategory::create(['name' => 'Pendapatan', 'slug' => 'pendapatan']);
        $catBelanja = BudgetCategory::create(['name' => 'Belanja', 'slug' => 'belanja']);

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

        // Open Data (Dataset) sengaja tidak di-seed agar data terbuka tetap dikelola manual/ril desa.

        $this->command->info('Berhasil menyuntikkan data APBDes.');
    }
}

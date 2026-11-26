<?php

namespace Database\Seeders;

use App\Models\BudgetCategory;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterCategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Menyiapkan kategori berita dan kategori APBDes dasar...');

        $newsCategories = [
            'Berita Utama',
            'Program Desa',
            'Kegiatan',
            'Pemerintahan',
        ];

        foreach ($newsCategories as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        $budgetCategories = [
            'Pendapatan',
            'Belanja',
            'Pembiayaan',
        ];

        foreach ($budgetCategories as $name) {
            BudgetCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        $this->command->info('Berhasil menyiapkan kategori berita dan APBDes dasar.');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\BudgetCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed default news categories and budget categories.
     */
    public function run(): void
    {
        // ── Kategori Berita ────────────────────────────────────────────────
        $newsCategories = [
            'Berita Utama',
            'Program Desa',
        ];

        foreach ($newsCategories as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }

        // ── Kategori Anggaran (APBDes) ─────────────────────────────────────
        $budgetCategories = [
            'Pendapatan',
            'Belanja',
        ];

        foreach ($budgetCategories as $name) {
            BudgetCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\StatisticCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Clean assistance_type text in families table and sync StatisticCategory indicators for Jenis Bantuan Sosial.
     */
    public function up(): void
    {
        // 1. Clean existing assistance_type strings in families table
        $families = DB::table('families')->whereNotNull('assistance_type')->get();
        foreach ($families as $f) {
            $raw = trim($f->assistance_type);
            $lower = strtolower($raw);

            if (empty($raw) || in_array($lower, ['tidak ada', 'tidak', 'none', '-'])) {
                DB::table('families')->where('id', $f->id)->update(['assistance_type' => 'Tidak Ada']);
                continue;
            }

            $items = [];
            if (str_contains($lower, 'pkh')) $items[] = 'PKH';
            if (str_contains($lower, 'bpnt') || str_contains($lower, 'sembako')) $items[] = 'BPNT / Sembako';
            if (str_contains($lower, 'blt')) $items[] = 'BLT Desa';
            if (str_contains($lower, 'listrik') || str_contains($lower, 'subsidi')) $items[] = 'Subsidi Listrik';
            if (str_contains($lower, 'bedah') || str_contains($lower, 'rumah')) $items[] = 'Bedah Rumah';

            if (empty($items)) {
                $items[] = 'Bantuan Lainnya';
            }

            DB::table('families')->where('id', $f->id)->update([
                'assistance_type' => implode(', ', array_unique($items))
            ]);
        }

        // 2. Create or Update Statistic Category for Jenis Bantuan Sosial
        $category = StatisticCategory::firstOrCreate(
            ['slug' => 'bantuan-sosial'],
            [
                'name' => 'Jenis Bantuan Sosial',
                'description' => 'Statistik penerima program bantuan sosial keluarga',
                'is_active' => true,
                'mapping_table' => 'families',
                'mapping_column' => 'assistance_type',
            ]
        );

        // Clear existing indicators to rebuild cleanly
        $category->indicators()->delete();

        $indicators = [
            ['name' => 'PKH', 'operator' => 'LIKE', 'value' => '%PKH%'],
            ['name' => 'BPNT / Sembako', 'operator' => 'LIKE', 'value' => '%BPNT%'],
            ['name' => 'BLT Desa', 'operator' => 'LIKE', 'value' => '%BLT%'],
            ['name' => 'Subsidi Listrik', 'operator' => 'LIKE', 'value' => '%Subsidi Listrik%'],
            ['name' => 'Bedah Rumah', 'operator' => 'LIKE', 'value' => '%Bedah Rumah%'],
            ['name' => 'Bantuan Lainnya', 'operator' => 'LIKE', 'value' => '%Bantuan Lainnya%'],
            ['name' => 'Tidak Menerima Bantuan', 'operator' => '=', 'value' => 'Tidak Ada'],
        ];

        foreach ($indicators as $idx => $item) {
            $category->indicators()->create([
                'name' => $item['name'],
                'unit' => 'Keluarga',
                'mapping_column' => 'assistance_type',
                'mapping_operator' => $item['operator'],
                'mapping_value' => $item['value'],
                'order' => $idx + 1,
                'is_active' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};

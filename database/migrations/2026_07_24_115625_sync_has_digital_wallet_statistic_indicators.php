<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\StatisticCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ensure "Kepemilikan Dompet Digital/Rekening" statistic category and indicators exist.
     */
    public function up(): void
    {
        $category = StatisticCategory::where('slug', 'dompet-digital-rekening')->first();
        if (!$category) {
            return;
        }

        if ($category->indicators()->count() === 0) {
            $walletItems = [
                ['name' => 'Tidak Ada', 'mapping_value' => 'Tidak ada'],
                ['name' => 'Ya untuk Pribadi', 'mapping_value' => 'Ya untuk pribadi'],
                ['name' => 'Ya untuk Usaha & Pribadi', 'mapping_value' => 'Ya untuk usaha dan pribadi'],
                ['name' => 'Ya untuk Usaha', 'mapping_value' => 'Ya untuk usaha'],
            ];

            foreach ($walletItems as $idx => $item) {
                $category->indicators()->create([
                    'name' => $item['name'],
                    'unit' => 'Jiwa',
                    'mapping_column' => 'has_digital_wallet',
                    'mapping_operator' => '=',
                    'mapping_value' => $item['mapping_value'],
                    'order' => $idx + 1,
                    'is_active' => true,
                ]);
            }
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

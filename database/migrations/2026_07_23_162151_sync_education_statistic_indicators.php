<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $cat = \App\Models\StatisticCategory::where('slug', 'pendidikan')->first();
        if ($cat) {
            $cat->indicators()->delete();
            $items = [
                ['name' => 'Tidak punya ijazah SD', 'mapping_value' => 'Tidak punya ijazah SD'],
                ['name' => 'SD/sederajat', 'mapping_value' => 'SD/sederajat'],
                ['name' => 'SMP/sederajat', 'mapping_value' => 'SMP/sederajat'],
                ['name' => 'SMA/sederajat', 'mapping_value' => 'SMA/sederajat'],
                ['name' => 'D1/D2/D3', 'mapping_value' => 'D1/D2/D3'],
                ['name' => 'D4/S1/Profesi', 'mapping_value' => 'D4/S1/Profesi'],
                ['name' => 'S2/S3', 'mapping_value' => 'S2/S3'],
            ];
            foreach ($items as $idx => $item) {
                $cat->indicators()->create([
                    'name' => $item['name'],
                    'unit' => 'Jiwa',
                    'mapping_column' => 'education_level',
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
        //
    }
};

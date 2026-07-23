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
        $cat = \App\Models\StatisticCategory::firstOrCreate(
            ['slug' => 'status-pekerjaan'],
            [
                'name' => 'Status Pekerjaan',
                'description' => 'Statistik Penduduk Berdasarkan Status Kedudukan Pekerjaan Utama',
                'mapping_table' => 'citizens',
                'mapping_column' => 'job_status',
                'chart_type' => 'bar',
                'order' => 5,
                'is_active' => true,
            ]
        );

        if ($cat) {
            $cat->indicators()->delete();
            $items = [
                ['name' => 'Mandiri', 'mapping_value' => 'Mandiri'],
                ['name' => 'Karyawan Swasta', 'mapping_value' => 'Karyawan Swasta'],
                ['name' => 'Pekerja Bebas', 'mapping_value' => 'Pekerja Bebas'],
                ['name' => 'Pekerja Keluarga', 'mapping_value' => 'Pekerja Keluarga'],
                ['name' => 'PNS / Aparatur', 'mapping_value' => 'PNS / Aparatur'],
                ['name' => 'Pemberi Kerja', 'mapping_value' => 'Pemberi Kerja'],
                ['name' => 'Lainnya', 'mapping_value' => 'Lainnya'],
            ];
            foreach ($items as $idx => $item) {
                $cat->indicators()->create([
                    'name' => $item['name'],
                    'unit' => 'Jiwa',
                    'mapping_column' => 'job_status',
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

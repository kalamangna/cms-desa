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
        $cat = \App\Models\StatisticCategory::where('slug', 'status-pekerjaan')->first();
        if ($cat) {
            $cat->indicators()->delete();
            $items = [
                ['name' => 'Berusaha Sendiri', 'mapping_value' => 'Berusaha sendiri'],
                ['name' => 'Buruh / Karyawan / Pegawai Swasta', 'mapping_value' => 'Buruh/karyawan/pegawai swasta'],
                ['name' => 'Pekerja Bebas', 'mapping_value' => 'Pekerja bebas'],
                ['name' => 'Pekerja Keluarga / Tidak Dibayar', 'mapping_value' => 'Pekerja keluarga/tidak dibayar'],
                ['name' => 'ASN / TNI / Polri / BUMN / BUMD / Pejabat Negara', 'mapping_value' => 'ASN/TNI/Polri/BUMN/BUMD/pejabat negara'],
                ['name' => 'Berusaha Dibantu Buruh', 'mapping_value' => 'Berusaha dibantu buruh'],
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

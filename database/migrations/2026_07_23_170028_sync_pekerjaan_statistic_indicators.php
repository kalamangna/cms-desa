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
        $cat = \App\Models\StatisticCategory::where('slug', 'pekerjaan')->first();
        if ($cat) {
            $cat->indicators()->delete();
            $items = [
                ['name' => 'Belum / Tidak Bekerja', 'mapping_value' => 'Belum / Tidak Bekerja'],
                ['name' => 'Ibu Rumah Tangga', 'mapping_value' => 'Ibu Rumah Tangga'],
                ['name' => 'Petani / Pekebun', 'mapping_value' => 'Petani / Pekebun'],
                ['name' => 'Pelajar / Mahasiswa', 'mapping_value' => 'Pelajar / Mahasiswa'],
                ['name' => 'Wiraswasta / Pengusaha', 'mapping_value' => 'Wiraswasta / Pengusaha'],
                ['name' => 'Karyawan Swasta', 'mapping_value' => 'Karyawan Swasta'],
                ['name' => 'Tenaga Pendidikan', 'mapping_value' => 'Tenaga Pendidikan'],
                ['name' => 'Tenaga Kesehatan', 'mapping_value' => 'Tenaga Kesehatan'],
                ['name' => 'PNS / Aparatur', 'mapping_value' => 'PNS / Aparatur'],
                ['name' => 'Buruh / Pekerja Harian', 'mapping_value' => 'Buruh / Pekerja Harian'],
                ['name' => 'Pekerja Jasa & Transportasi', 'mapping_value' => 'Pekerja Jasa & Transportasi'],
                ['name' => 'Pensiunan', 'mapping_value' => 'Pensiunan'],
                ['name' => 'Lainnya', 'mapping_value' => 'Lainnya'],
            ];
            foreach ($items as $idx => $item) {
                $cat->indicators()->create([
                    'name' => $item['name'],
                    'unit' => 'Jiwa',
                    'mapping_column' => 'job',
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

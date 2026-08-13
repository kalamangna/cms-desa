<?php

namespace Database\Seeders;

use App\Models\PopupInfographic;
use App\Models\StatisticCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StatisticAndPopupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membersihkan dan membuat data Statistik dan Popup Infografis...');

        StatisticCategory::query()->forceDelete();
        PopupInfographic::query()->forceDelete();

        // 1. Kategori Statistik
        $statistics = [
            [
                'name' => 'Statistik Kelompok Umur & Jenis Kelamin',
                'description' => 'Data statistik komposisi penduduk berdasarkan jenis kelamin yang tercatat secara aktif di desa.',
                'mapping_table' => 'citizens',
                'mapping_column' => 'gender', // Ini akan otomatis men-generate indikator Laki-laki / Perempuan dari data citizens
                'is_active' => true,
            ],
            [
                'name' => 'Statistik Tingkat Pendidikan Terakhir',
                'description' => 'Persentase tingkat pendidikan masyarakat untuk mengukur taraf indeks pembangunan manusia (IPM).',
                'mapping_table' => 'citizens',
                'mapping_column' => 'education_level',
                'is_active' => true,
            ],
            [
                'name' => 'Statistik Status Pekerjaan',
                'description' => 'Pengelompokan profesi masyarakat desa dari berbagai sektor baik formal maupun informal.',
                'mapping_table' => 'citizens',
                'mapping_column' => 'job_status',
                'is_active' => true,
            ],
            [
                'name' => 'Statistik Jenis Pekerjaan',
                'description' => 'Data statistik berdasarkan bidang pekerjaan utama masyarakat desa.',
                'mapping_table' => 'citizens',
                'mapping_column' => 'job',
                'is_active' => true,
            ],
            [
                'name' => 'Statistik Agama',
                'description' => 'Data statistik komposisi penduduk berdasarkan penganut agama.',
                'mapping_table' => 'citizens',
                'mapping_column' => 'religion',
                'is_active' => true,
            ],
            [
                'name' => 'Statistik Penerima Bantuan Sosial',
                'description' => 'Pendataan keluarga prasejahtera yang berstatus sebagai Keluarga Penerima Manfaat (KPM).',
                'mapping_table' => 'families',
                'mapping_column' => 'assistance_type',
                'is_active' => true,
            ],
            [
                'name' => 'Statistik Jenis Bangunan',
                'description' => 'Data statistik berdasarkan status kepemilikan dan jenis bangunan rumah keluarga.',
                'mapping_table' => 'families',
                'mapping_column' => 'building_type',
                'is_active' => true,
            ],
            [
                'name' => 'Statistik Fasilitas Air & Sanitasi',
                'description' => 'Data statistik fasilitas air bersih dan sanitasi (toilet) milik warga desa.',
                'mapping_table' => 'families',
                'mapping_column' => 'water_source',
                'is_active' => true,
            ],
            [
                'name' => 'Statistik Kapasitas Listrik',
                'description' => 'Data kapasitas daya listrik terpasang pada rumah warga desa.',
                'mapping_table' => 'families',
                'mapping_column' => 'electricity_power',
                'is_active' => true,
            ],
        ];

        foreach ($statistics as $stat) {
            $stat['slug'] = Str::slug($stat['name']);
            StatisticCategory::create($stat);
        }

        // Pastikan direktori popup-infographics ada
        if (! Storage::disk('public')->exists('popup-infographics')) {
            Storage::disk('public')->makeDirectory('popup-infographics');
        }
        // Salin meta.webp sebagai dummy jika belum ada
        if (! Storage::disk('public')->exists('popup-infographics/meta.webp') && file_exists(public_path('img/meta.webp'))) {
            Storage::disk('public')->put('popup-infographics/meta.webp', file_get_contents(public_path('img/meta.webp')));
        }

        // 2. Popup Infografis
        $popups = [
            [
                'title' => 'Selamat Datang di Website Desa Tompobulu',
                'image' => 'popup-infographics/meta.webp',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Waspada Demam Berdarah',
                'image' => 'popup-infographics/meta.webp',
                'sort_order' => 2,
                'is_active' => false,
            ],
            [
                'title' => 'Layanan Surat Online Kini Tersedia',
                'image' => 'popup-infographics/meta.webp',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($popups as $popup) {
            PopupInfographic::create($popup);
        }

        $this->command->info('Berhasil menyuntikkan 9 Kategori Statistik (Otomatis Terhubung) dan 3 Popup Infografis.');
    }
}

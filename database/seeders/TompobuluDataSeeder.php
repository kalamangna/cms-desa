<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class TompobuluDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'village_name' => 'Tompobulu',
            'district_name' => 'Bulupoddo',
            'regency_name' => 'Sinjai',
            'province_name' => 'Sulawesi Selatan',
            'village_address' => 'Jl. Poros Tompobulu - Karampuang, Kec. Bulupoddo, Kab. Sinjai',
            'village_phone' => '0812-XXXX-XXXX',
            'village_email' => 'desa.tompobulu@sinjaikab.go.id',
            'village_vision' => 'Mewujudkan Desa Tompobulu sebagai Pusat Pelestarian Budaya dan Desa Wisata Mandiri Berbasis Data Presisi.',
            'village_mission' => '<ul><li>Melestarikan dan mempromosikan Kawasan Adat Karampuang sebagai warisan budaya nasional.</li><li>Meningkatkan tata kelola pemerintahan desa yang transparan melalui Program Desa Cantik.</li><li>Membangun infrastruktur desa yang mendukung sektor pertanian dan pariwisata.</li><li>Memberdayakan ekonomi kreatif masyarakat melalui inovasi pengolahan hasil alam.</li></ul>',
            'village_history' => '<p>Desa Tompobulu merupakan salah satu desa di wilayah Kecamatan Bulupoddo, Kabupaten Sinjai, yang memiliki akar sejarah yang sangat kuat. Desa ini dikenal luas sebagai rumah bagi <strong>Kampung Adat Karampuang</strong>, sebuah kawasan yang menjadi simbol kearifan lokal masyarakat Sinjai.</p><p>Kawasan Karampuang sendiri secara geografis sangat unik karena diapit oleh tiga aliran sungai besar, yaitu Sungai Lamole di selatan, Sungai Launre di barat, dan Sungai Bulu Tellue di sebelah timur. Keberadaan sungai-sungai ini sejak lama telah menjadi sumber kehidupan utama bagi masyarakat yang mayoritas bermata pencaharian sebagai petani.</p><p>Salah satu kekayaan budaya yang tetap terjaga hingga kini adalah ritual adat <strong>Mappogau Sihanua</strong>. Upacara ini dilaksanakan secara rutin setiap tahun sebagai bentuk rasa syukur dan permohonan doa bagi keselamatan seluruh warga. Ritual ini menjadi bukti nyata akulturasi yang indah antara tradisi leluhur dengan nilai-nilai religius Islam yang dianut masyarakat setempat.</p>',
            'village_area' => '14.5',
            'village_topography' => 'Dataran Tinggi / Perbukitan',
            'village_population' => '2.842',
        ];

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}

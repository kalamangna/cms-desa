<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membuat data pengaturan dan profil desa default...');

        $settings = [
            // Identitas Desa
            ['key' => 'village_name', 'value' => 'Tompobulu'],
            ['key' => 'province_name', 'value' => 'Sulawesi Selatan'],
            ['key' => 'regency_name', 'value' => 'Sinjai'],
            ['key' => 'district_name', 'value' => 'Bulupoddo'],
            
            // Pemerintahan
            ['key' => 'village_period_start', 'value' => '2022'],
            ['key' => 'village_period_end', 'value' => '2028'],
            
            // Kontak & Lokasi
            ['key' => 'village_email', 'value' => 'pemdes@tompobulu.desa.id'],
            ['key' => 'village_phone', 'value' => '0411123456'],
            ['key' => 'village_address', 'value' => 'Jl. Poros Desa No. 1, Desa Tompobulu, Kec. Bulupoddo, Kab. Sinjai, 92653'],
            
            // Media Sosial
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/desatompobulu'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/desatompobulu'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/c/desatompobulu'],
            
            // Tampilan & Tema
            ['key' => 'primary_color', 'value' => '#10b981'],
            
            // Sejarah & Visi Misi
            ['key' => 'village_history', 'value' => '<p>Desa Tompobulu adalah desa agraris yang terletak di dataran tinggi yang sejuk. Desa ini terbentuk dari hasil pemekaran wilayah pada tahun 1980 dan terus berkembang menjadi desa mandiri dengan potensi pertanian dan pariwisata yang kuat.</p>'],
            ['key' => 'village_vision', 'value' => 'Mewujudkan Desa Tompobulu yang Mandiri, Sejahtera, Religius, dan Berbudaya Lingkungan'],
            ['key' => 'village_mission', 'value' => '<ol><li>Meningkatkan kualitas pelayanan publik pemerintahan desa.</li><li>Membangun infrastruktur desa yang memadai dan berwawasan lingkungan.</li><li>Mendorong pertumbuhan ekonomi kerakyatan melalui BUMDes dan sektor pertanian.</li><li>Menjaga dan melestarikan budaya gotong royong serta nilai-nilai keagamaan.</li></ol>'],
            
            // Sambutan Kades
            ['key' => 'village_head_greeting_title', 'value' => 'Sambutan Kepala Desa Tompobulu'],
            ['key' => 'village_head_greeting', 'value' => '<p><em>Assalamu\'alaikum Warahmatullahi Wabarakatuh.</em></p><p>Selamat datang di website resmi Desa Tompobulu. Puji syukur kita panjatkan ke hadirat Allah SWT, karena atas rahmat-Nya website ini dapat hadir sebagai jendela informasi desa kita tercinta.</p><p>Website ini dibangun sebagai wujud nyata komitmen kami dalam menghadirkan tata kelola pemerintahan desa yang transparan, akuntabel, dan berbasis digital. Melalui platform ini, seluruh warga desa dapat mengakses berbagai informasi pelayanan publik, berita terkini, hingga memantau realisasi anggaran pembangunan secara langsung.</p><p>Semoga website ini memberikan manfaat yang seluas-luasnya bagi kemajuan Desa Tompobulu.</p><p><em>Wassalamu\'alaikum Warahmatullahi Wabarakatuh.</em></p>'],
            
            // Karakteristik & Wilayah
            ['key' => 'village_area', 'value' => '450'],
            ['key' => 'village_area_unit', 'value' => 'Hektar'],
            ['key' => 'village_topography', 'value' => 'Pegunungan'],
            
            // Peta Spasial
            ['key' => 'village_latitude', 'value' => '-6.556'],
            ['key' => 'village_longitude', 'value' => '107.011'],
            ['key' => 'village_geojson', 'value' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[[[107.000,-6.550],[107.020,-6.550],[107.020,-6.560],[107.000,-6.560],[107.000,-6.550]]]}}'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        $this->command->info('Berhasil menyuntikkan ' . count($settings) . ' pengaturan website default.');
    }
}

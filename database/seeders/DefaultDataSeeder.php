<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Setting;
use App\Models\StatisticCategory;
use App\Models\StatisticIndicator;
use App\Models\Service;
use App\Models\Dusun;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions Setup
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'admin_desa']);
        Role::firstOrCreate(['name' => 'agen_statistik']);

        $user = User::firstOrCreate(
            ['username' => 'kalamangna'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Syazani'),
            ]
        );

        $user->assignRole($superAdminRole);

        // 2. Default Settings Setup
        $settings = [
            ['key' => 'village_name', 'value' => 'Nama Desa'],
            ['key' => 'village_logo', 'value' => null],
            ['key' => 'village_head', 'value' => 'Nama Kepala Desa'],
            ['key' => 'village_email', 'value' => 'email@desa.go.id'],
            ['key' => 'village_phone', 'value' => '08123456789'],
            ['key' => 'village_address', 'value' => 'Alamat Kantor Desa'],
            ['key' => 'district_name', 'value' => 'Nama Kecamatan'],
            ['key' => 'regency_name', 'value' => 'Nama Kabupaten'],
            ['key' => 'province_name', 'value' => 'Nama Provinsi'],
            ['key' => 'village_history', 'value' => '<p>Sejarah Desa berawal dari pemukiman tradisional yang kaya akan nilai budaya dan gotong royong. Selama berpuluh-puluh tahun, desa ini terus berkembang menjadi pusat kegiatan ekonomi dan sosial bagi masyarakat sekitarnya.</p>'],
            ['key' => 'village_vision', 'value' => 'Mewujudkan Desa Mandiri, Cerdas, dan Berbasis Data untuk Kesejahteraan Masyarakat.'],
            ['key' => 'village_mission', 'value' => '<ul><li>Meningkatkan kualitas pelayanan publik berbasis teknologi informasi.</li><li>Mendorong kemandirian ekonomi desa melalui pemberdayaan UMKM.</li><li>Meningkatkan kualitas sumber daya manusia melalui pendidikan dan pelatihan.</li><li>Mengoptimalkan pengelolaan data desa yang akurat dan transparan.</li></ul>'],
            ['key' => 'village_head_greeting_title', 'value' => 'Mewujudkan Desa Mandiri Berbasis Data Presisi'],
            ['key' => 'village_head_greeting', 'value' => 'Selamat datang di portal resmi Desa. Kami berkomitmen untuk menghadirkan pemerintahan yang transparan dan akuntabel. Melalui Program Desa Cantik (Cinta Statistik), kami berupaya mengelola data desa secara profesional sebagai dasar pembangunan yang tepat sasaran.'],
            ['key' => 'village_area', 'value' => '12.4'],
            ['key' => 'village_population', 'value' => '3.500'],
            ['key' => 'village_topography', 'value' => 'Dataran Tinggi'],
            ['key' => 'village_dusun_count', 'value' => '6'],
            ['key' => 'village_latitude', 'value' => '-5.230000'],
            ['key' => 'village_longitude', 'value' => '120.210000'],
            ['key' => 'social_facebook', 'value' => '#'],
            ['key' => 'social_instagram', 'value' => '#'],
            ['key' => 'social_youtube', 'value' => '#'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }

        // 3. Statistic Categories & Indicators Setup
        $statistics = [
            'Penduduk' => [
                'Jumlah Laki-laki' => 'Jiwa',
                'Jumlah Perempuan' => 'Jiwa',
            ],
            'Pendidikan' => [
                'SD' => 'Orang',
                'SMP' => 'Orang',
                'SMA' => 'Orang',
                'Sarjana' => 'Orang',
            ],
            'Pekerjaan' => [
                'Nelayan' => 'Orang',
                'Petani' => 'Orang',
                'PNS' => 'Orang',
                'Wiraswasta' => 'Orang',
                'Lainnya' => 'Orang',
            ],
            'Kemiskinan' => [
                'Keluarga Prasejahtera' => 'KK',
            ],
            'Stunting' => [
                'Balita Stunting' => 'Anak',
            ],
            'UMKM' => [
                'Jumlah Unit UMKM' => 'Unit',
            ],
        ];

        foreach ($statistics as $catName => $indicators) {
            $category = StatisticCategory::firstOrCreate(
                ['name' => $catName],
                ['slug' => Str::slug($catName)]
            );

            foreach ($indicators as $indName => $unit) {
                StatisticIndicator::firstOrCreate(
                    [
                        'statistic_category_id' => $category->id,
                        'name' => $indName
                    ],
                    ['unit' => $unit]
                );
            }
        }

        // 4. Default Services Setup
        $defaultServices = [
            [
                'title' => 'Kartu Keluarga (KK)',
                'icon' => 'fa-users',
                'description' => 'Prosedur pembuatan KK baru, penambahan anggota keluarga, atau perubahan data.',
                'requirements' => '<ul><li>Surat Pengantar dari RT/RW setempat.</li><li>Kartu Keluarga lama (untuk perubahan data/kehilangan).</li><li>Akta Kelahiran atau Akta Nikah (jika ada penambahan anggota).</li><li>Surat Keterangan Kehilangan dari Kepolisian (jika KK lama hilang).</li></ul>'
            ],
            [
                'title' => 'KTP Elektronik',
                'icon' => 'fa-id-card',
                'description' => 'Syarat perekaman KTP-el baru, penggantian KTP rusak, atau hilang.',
                'requirements' => '<ul><li>Berusia minimal 17 tahun atau sudah menikah.</li><li>Fotokopi Kartu Keluarga (KK).</li><li>Surat Keterangan Kehilangan dari Kepolisian (jika KTP lama hilang).</li><li>KTP lama yang rusak (jika mengajukan penggantian KTP rusak).</li></ul>'
            ],
            [
                'title' => 'Akta Kelahiran',
                'icon' => 'fa-baby',
                'description' => 'Persyaratan pembuatan akta kelahiran untuk anak yang baru lahir.',
                'requirements' => '<ul><li>Surat Keterangan Kelahiran dari Rumah Sakit, Bidan, atau Penolong Kelahiran.</li><li>Fotokopi Akta Nikah/Buku Nikah orang tua.</li><li>Fotokopi Kartu Keluarga (KK) orang tua.</li><li>Fotokopi KTP orang tua dan 2 orang saksi kelahiran.</li></ul>'
            ],
            [
                'title' => 'Surat Pengantar Nikah',
                'icon' => 'fa-heart',
                'description' => 'Layanan administrasi bagi warga yang akan melangsungkan pernikahan.',
                'requirements' => '<ul><li>Surat Pengantar dari RT/RW setempat.</li><li>Fotokopi KTP dan Kartu Keluarga (KK) calon mempelai.</li><li>Fotokopi Akta Kelahiran atau Ijazah terakhir.</li><li>Pas foto ukuran 2x3 dan 3x4 masing-masing 4 lembar dengan latar belakang biru.</li></ul>'
            ],
            [
                'title' => 'Keterangan Domisili',
                'icon' => 'fa-house-user',
                'description' => 'Pembuatan surat keterangan tempat tinggal untuk berbagai keperluan.',
                'requirements' => '<ul><li>Surat Pengantar dari RT/RW setempat.</li><li>Fotokopi KTP Pemohon.</li><li>Fotokopi Kartu Keluarga (KK) Pemohon.</li></ul>'
            ],
            [
                'title' => 'Keterangan Tidak Mampu (SKTM)',
                'icon' => 'fa-hand-holding-heart',
                'description' => 'Layanan SKTM untuk keperluan pendidikan, kesehatan, atau bantuan sosial.',
                'requirements' => '<ul><li>Surat Pengantar dari RT/RW setempat dengan keterangan Tidak Mampu.</li><li>Fotokopi KTP dan Kartu Keluarga (KK) Kepala Keluarga.</li><li>Surat Pernyataan Tidak Mampu bermeterai cukup dari yang bersangkutan.</li><li>Fotokopi Kartu Indonesia Sehat (KIS/BPJS) jika ada.</li></ul>'
            ],
        ];

        foreach ($defaultServices as $srv) {
            Service::firstOrCreate(
                ['slug' => Str::slug($srv['title'])],
                [
                    'title' => $srv['title'],
                    'icon' => $srv['icon'],
                    'description' => $srv['description'],
                    'requirements' => $srv['requirements'],
                ]
            );
        }

        // 5. Default Dusuns Setup
        $defaultDusuns = [
            ['name' => 'Dusun I', 'head_name' => 'M. Yusuf'],
            ['name' => 'Dusun II', 'head_name' => 'H. Mansur'],
            ['name' => 'Dusun III', 'head_name' => 'Ahmad'],
            ['name' => 'Dusun IV', 'head_name' => 'Rahman'],
            ['name' => 'Dusun V', 'head_name' => 'Syarifuddin'],
            ['name' => 'Dusun VI', 'head_name' => 'Hasbullah'],
        ];

        foreach ($defaultDusuns as $dus) {
            Dusun::firstOrCreate(
                ['name' => $dus['name']],
                ['head_name' => $dus['head_name']]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Official;
use App\Models\Dusun;
use App\Models\Institution;
use Illuminate\Support\Str;

class VillageProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membuat data Perangkat Desa, Dusun, dan Kelembagaan...');

        // 1. Data Dusun
        $dusuns = [
            ['name' => 'Bonto', 'head_name' => 'Baharuddin', 'total_rt' => 4, 'total_rw' => 1, 'geojson' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[[[107.000,-6.550],[107.010,-6.550],[107.010,-6.560],[107.000,-6.560],[107.000,-6.550]]]}}'],
            ['name' => 'Tassililu', 'head_name' => 'Amiruddin', 'total_rt' => 5, 'total_rw' => 2, 'geojson' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[[[107.010,-6.550],[107.015,-6.550],[107.015,-6.560],[107.010,-6.560],[107.010,-6.550]]]}}'],
            ['name' => 'Panaikang', 'head_name' => 'Kaharuddin', 'total_rt' => 3, 'total_rw' => 1, 'geojson' => '{"type":"Feature","geometry":{"type":"Polygon","coordinates":[[[107.015,-6.550],[107.020,-6.550],[107.020,-6.560],[107.015,-6.560],[107.015,-6.550]]]}}'],
        ];

        foreach ($dusuns as $dusun) {
            Dusun::updateOrCreate(['name' => $dusun['name']], $dusun);
        }

        // 2. Data Perangkat Desa (Struktur Organisasi)
        // Level 1: Kepala Desa
        $kades = Official::firstOrCreate(
            ['position' => 'Kepala Desa'],
            ['name' => 'Andi Syamsuddin', 'level' => 1, 'order' => 1]
        );

        // Level 2: Sekretaris Desa
        $sekdes = Official::firstOrCreate(
            ['position' => 'Sekretaris Desa'],
            ['name' => 'Hasniati, S.E.', 'level' => 2, 'order' => 2, 'parent_id' => $kades->id]
        );

        // Level 3: Kaur & Kasi
        $level3 = [
            ['name' => 'Hendra Saputra', 'position' => 'Kaur Keuangan', 'level' => 3, 'order' => 3, 'parent_id' => $sekdes->id],
            ['name' => 'Nurlia', 'position' => 'Kaur Umum & Perencanaan', 'level' => 3, 'order' => 4, 'parent_id' => $sekdes->id],
            ['name' => 'Basri', 'position' => 'Kasi Pemerintahan', 'level' => 3, 'order' => 5, 'parent_id' => $kades->id],
            ['name' => 'Salmah', 'position' => 'Kasi Kesejahteraan', 'level' => 3, 'order' => 6, 'parent_id' => $kades->id],
            ['name' => 'Zainuddin', 'position' => 'Kasi Pelayanan', 'level' => 3, 'order' => 7, 'parent_id' => $kades->id],
        ];

        foreach ($level3 as $official) {
            Official::firstOrCreate(['position' => $official['position']], $official);
        }

        // Level 4: Kepala Dusun (Kadus)
        $kadus = [
            ['name' => 'Baharuddin', 'position' => 'Kepala Dusun Bonto', 'level' => 4, 'order' => 8, 'parent_id' => $kades->id],
            ['name' => 'Amiruddin', 'position' => 'Kepala Dusun Tassililu', 'level' => 4, 'order' => 9, 'parent_id' => $kades->id],
            ['name' => 'Kaharuddin', 'position' => 'Kepala Dusun Panaikang', 'level' => 4, 'order' => 10, 'parent_id' => $kades->id],
        ];

        foreach ($kadus as $official) {
            Official::firstOrCreate(['position' => $official['position']], $official);
        }

        // 3. Data Kelembagaan Desa
        $institutions = [
            [
                'name' => 'Badan Permusyawaratan Desa (BPD)',
                'description' => '<p>BPD merupakan lembaga perwujudan demokrasi dalam penyelenggaraan pemerintahan desa. BPD berfungsi menetapkan Peraturan Desa bersama Kepala Desa, menampung dan menyalurkan aspirasi masyarakat.</p>',
                'management' => [
                    ['position' => 'Ketua', 'name' => 'H. M. Arsyad'],
                    ['position' => 'Wakil Ketua', 'name' => 'Drs. H. M. Yasin'],
                    ['position' => 'Sekretaris', 'name' => 'Kaharuddin, S.Pd'],
                ],
            ],
            [
                'name' => 'Lembaga Pemberdayaan Masyarakat Desa (LPMD)',
                'description' => '<p>LPMD adalah lembaga kemasyarakatan yang bertugas membantu Kepala Desa dalam menyerap aspirasi masyarakat terkait perencanaan dan pelaksanaan pembangunan, serta menggerakkan swadaya gotong royong masyarakat.</p>',
                'management' => [
                    ['position' => 'Ketua', 'name' => 'Ambo Tang'],
                    ['position' => 'Wakil Ketua', 'name' => 'Sudirman'],
                    ['position' => 'Sekretaris', 'name' => 'Hasbullah'],
                ],
            ],
            [
                'name' => 'Pemberdayaan Kesejahteraan Keluarga (PKK)',
                'description' => '<p>PKK merupakan gerakan nasional dalam pembangunan masyarakat yang tumbuh dari bawah, yang pengelolaannya dari, oleh, dan untuk masyarakat menuju terwujudnya keluarga yang beriman, bertaqwa, berakhlak mulia dan berbudi luhur.</p>',
                'management' => [
                    ['position' => 'Ketua', 'name' => 'Hj. Rosdiana'],
                    ['position' => 'Wakil Ketua', 'name' => 'Hj. Murniati'],
                    ['position' => 'Sekretaris', 'name' => 'Nurmila'],
                ],
            ],
            [
                'name' => 'Karang Taruna',
                'description' => '<p>Karang Taruna adalah wadah pengembangan generasi muda non-partisan yang tumbuh atas dasar kesadaran dan rasa tanggung jawab sosial dari, oleh, dan untuk masyarakat khususnya generasi muda di wilayah desa.</p>',
                'management' => [
                    ['position' => 'Ketua', 'name' => 'Asriadi'],
                    ['position' => 'Wakil Ketua', 'name' => 'Firman'],
                    ['position' => 'Sekretaris', 'name' => 'Reza'],
                ],
            ]
        ];

        foreach ($institutions as $inst) {
            $inst['slug'] = Str::slug($inst['name']);
            Institution::firstOrCreate(['name' => $inst['name']], $inst);
        }

        $this->command->info('Berhasil membuat data Profil Desa (Wilayah, Aparatur, dan Lembaga).');
    }
}

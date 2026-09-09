<?php

namespace Database\Seeders;

use App\Models\Dusun;
use App\Models\Institution;
use App\Models\Official;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VillageProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membersihkan dan membuat data Perangkat Desa dan Kelembagaan...');

        // 1. Data Dusun (Dikosongkan agar wilayah diisi manual/riil oleh desa)
        Dusun::query()->forceDelete();

        // 2. Data Perangkat Desa (Hanya Kepala Desa)
        Official::query()->forceDelete();
        Official::create([
            'name' => 'Andi Syamsuddin',
            'position' => 'Kepala Desa',
            'level' => 1,
            'order' => 1,
        ]);

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
            ],
        ];

        foreach ($institutions as $inst) {
            $inst['slug'] = Str::slug($inst['name']);
            Institution::updateOrCreate(['name' => $inst['name']], $inst);
        }

        $this->command->info('Berhasil membuat data Profil Desa (Kepala Desa dan Lembaga).');
    }
}

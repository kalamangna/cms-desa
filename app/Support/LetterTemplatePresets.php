<?php

namespace App\Support;

class LetterTemplatePresets
{
    /**
     * Daftar preset template surat desa bawaan.
     *
     * @return array<string, array{
     *     label: string,
     *     letter_code: string,
     *     letter_title: string,
     *     letter_template: string,
     *     form_fields: array<int, array{label: string, key: string, type: string, placeholder: string, is_required: bool}>
     * }>
     */
    public static function all(): array
    {
        return [
            'domisili' => [
                'label' => 'Surat Keterangan Domisili',
                'letter_code' => '470',
                'letter_title' => 'SURAT KETERANGAN DOMISILI',
                'letter_template' => '<p>Yang bertanda tangan di bawah ini {{signer_position}} {{village_name}}, Kecamatan {{district_name}}, Kabupaten {{regency_name}}, menerangkan dengan sebenarnya bahwa:</p>
<table>
    <tr><td style="width: 170px;">Nama Lengkap</td><td style="width: 14px;">:</td><td><strong>{{applicant_name}}</strong></td></tr>
    <tr><td>NIK</td><td>:</td><td>{{applicant_nik}}</td></tr>
    <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>{{place_of_birth}}, {{date_of_birth}}</td></tr>
    <tr><td>Jenis Kelamin</td><td>:</td><td>{{gender}}</td></tr>
    <tr><td>Agama</td><td>:</td><td>{{religion}}</td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td>{{job}}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{address}} RT {{rt}} RW {{rw}}, Dusun {{dusun}}, Desa {{village_name}}</td></tr>
</table>
<p>Berdasarkan data kependudukan dan pengamatan di lapangan, orang tersebut di atas benar berdomisili di wilayah Desa {{village_name}} sampai saat surat ini diterbitkan.</p>
<p>Surat keterangan ini diterbitkan untuk keperluan: <strong>{{keperluan}}</strong>.</p>
<p>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>',
                'form_fields' => [
                    [
                        'label' => 'Keperluan Surat',
                        'key' => 'keperluan',
                        'type' => 'textarea',
                        'placeholder' => 'Pengurusan rekening bank atau administrasi sekolah',
                        'is_required' => true,
                    ],
                ],
            ],
            'usaha' => [
                'label' => 'Surat Keterangan Usaha',
                'letter_code' => '503',
                'letter_title' => 'SURAT KETERANGAN USAHA',
                'letter_template' => '<p>Yang bertanda tangan di bawah ini {{signer_position}} {{village_name}}, Kecamatan {{district_name}}, Kabupaten {{regency_name}}, menerangkan bahwa:</p>
<table>
    <tr><td style="width: 170px;">Nama Lengkap</td><td style="width: 14px;">:</td><td><strong>{{applicant_name}}</strong></td></tr>
    <tr><td>NIK</td><td>:</td><td>{{applicant_nik}}</td></tr>
    <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>{{place_of_birth}}, {{date_of_birth}}</td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td>{{job}}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{address}} RT {{rt}} RW {{rw}}, Dusun {{dusun}}, Desa {{village_name}}</td></tr>
</table>
<p>Orang tersebut di atas benar memiliki dan menjalankan usaha sebagai berikut:</p>
<table>
    <tr><td style="width: 170px;">Nama Usaha</td><td style="width: 14px;">:</td><td><strong>{{nama_usaha}}</strong></td></tr>
    <tr><td>Bidang Usaha</td><td>:</td><td>{{bidang_usaha}}</td></tr>
    <tr><td>Alamat Usaha</td><td>:</td><td>{{alamat_usaha}}</td></tr>
</table>
<p>Surat keterangan ini diterbitkan untuk keperluan: <strong>{{keperluan}}</strong>.</p>
<p>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>',
                'form_fields' => [
                    [
                        'label' => 'Nama Usaha',
                        'key' => 'nama_usaha',
                        'type' => 'text',
                        'placeholder' => 'Warung Sembako Berkah',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Bidang Usaha',
                        'key' => 'bidang_usaha',
                        'type' => 'text',
                        'placeholder' => 'Perdagangan eceran',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Alamat Usaha',
                        'key' => 'alamat_usaha',
                        'type' => 'textarea',
                        'placeholder' => 'Dusun Mattirowalie, Desa Tompobulu',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Keperluan Surat',
                        'key' => 'keperluan',
                        'type' => 'text',
                        'placeholder' => 'Pengajuan kredit usaha rakyat',
                        'is_required' => true,
                    ],
                ],
            ],
            'tidak_mampu' => [
                'label' => 'Surat Keterangan Tidak Mampu',
                'letter_code' => '401',
                'letter_title' => 'SURAT KETERANGAN TIDAK MAMPU',
                'letter_template' => '<p>Yang bertanda tangan di bawah ini {{signer_position}} {{village_name}}, Kecamatan {{district_name}}, Kabupaten {{regency_name}}, menerangkan bahwa:</p>
<table>
    <tr><td style="width: 170px;">Nama Lengkap</td><td style="width: 14px;">:</td><td><strong>{{applicant_name}}</strong></td></tr>
    <tr><td>NIK</td><td>:</td><td>{{applicant_nik}}</td></tr>
    <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>{{place_of_birth}}, {{date_of_birth}}</td></tr>
    <tr><td>Jenis Kelamin</td><td>:</td><td>{{gender}}</td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td>{{job}}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{address}} RT {{rt}} RW {{rw}}, Dusun {{dusun}}, Desa {{village_name}}</td></tr>
</table>
<p>Berdasarkan catatan pemerintah desa, yang bersangkutan tergolong keluarga kurang mampu secara ekonomi.</p>
<p>Surat keterangan ini diterbitkan untuk keperluan: <strong>{{keperluan}}</strong>.</p>
<p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>',
                'form_fields' => [
                    [
                        'label' => 'Keperluan Surat',
                        'key' => 'keperluan',
                        'type' => 'textarea',
                        'placeholder' => 'Pengajuan beasiswa pendidikan atau layanan kesehatan',
                        'is_required' => true,
                    ],
                ],
            ],
            'kelahiran' => [
                'label' => 'Surat Keterangan Kelahiran',
                'letter_code' => '472.11',
                'letter_title' => 'SURAT KETERANGAN KELAHIRAN',
                'letter_template' => '<p>Yang bertanda tangan di bawah ini {{signer_position}} {{village_name}}, Kecamatan {{district_name}}, Kabupaten {{regency_name}}, menerangkan bahwa telah lahir seorang anak:</p>
<table>
    <tr><td style="width: 170px;">Nama Anak</td><td style="width: 14px;">:</td><td><strong>{{nama_anak}}</strong></td></tr>
    <tr><td>Jenis Kelamin</td><td>:</td><td>{{jenis_kelamin_anak}}</td></tr>
    <tr><td>Tempat Lahir</td><td>:</td><td>{{tempat_lahir_anak}}</td></tr>
    <tr><td>Tanggal Lahir</td><td>:</td><td>{{tanggal_lahir_anak}}</td></tr>
</table>
<p>Anak tersebut adalah anak dari pasangan:</p>
<table>
    <tr><td style="width: 170px;">Nama Ayah</td><td style="width: 14px;">:</td><td>{{nama_ayah}}</td></tr>
    <tr><td>Nama Ibu</td><td>:</td><td>{{nama_ibu}}</td></tr>
    <tr><td>Alamat Orang Tua</td><td>:</td><td>{{address}} RT {{rt}} RW {{rw}}, Dusun {{dusun}}, Desa {{village_name}}</td></tr>
</table>
<p>Demikian surat keterangan kelahiran ini dibuat berdasarkan laporan pemohon {{applicant_name}} untuk dipergunakan sebagaimana mestinya.</p>',
                'form_fields' => [
                    [
                        'label' => 'Nama Anak',
                        'key' => 'nama_anak',
                        'type' => 'text',
                        'placeholder' => 'Ahmad Fauzan',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Jenis Kelamin Anak',
                        'key' => 'jenis_kelamin_anak',
                        'type' => 'text',
                        'placeholder' => 'Laki-laki',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Tempat Lahir Anak',
                        'key' => 'tempat_lahir_anak',
                        'type' => 'text',
                        'placeholder' => 'Sinjai',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Tanggal Lahir Anak',
                        'key' => 'tanggal_lahir_anak',
                        'type' => 'date',
                        'placeholder' => '2026-11-20',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Nama Ayah',
                        'key' => 'nama_ayah',
                        'type' => 'text',
                        'placeholder' => 'Budi Santoso',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Nama Ibu',
                        'key' => 'nama_ibu',
                        'type' => 'text',
                        'placeholder' => 'Siti Aminah',
                        'is_required' => true,
                    ],
                ],
            ],
            'pengantar' => [
                'label' => 'Surat Pengantar Umum',
                'letter_code' => '470',
                'letter_title' => 'SURAT PENGANTAR',
                'letter_template' => '<p>Yang bertanda tangan di bawah ini {{signer_position}} {{village_name}}, Kecamatan {{district_name}}, Kabupaten {{regency_name}}, memberikan pengantar kepada:</p>
<table>
    <tr><td style="width: 170px;">Nama Lengkap</td><td style="width: 14px;">:</td><td><strong>{{applicant_name}}</strong></td></tr>
    <tr><td>NIK</td><td>:</td><td>{{applicant_nik}}</td></tr>
    <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>{{place_of_birth}}, {{date_of_birth}}</td></tr>
    <tr><td>Jenis Kelamin</td><td>:</td><td>{{gender}}</td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td>{{job}}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{address}} RT {{rt}} RW {{rw}}, Dusun {{dusun}}, Desa {{village_name}}</td></tr>
</table>
<p>Surat pengantar ini diberikan untuk keperluan: <strong>{{keperluan}}</strong>.</p>
<p>Demikian surat pengantar ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>',
                'form_fields' => [
                    [
                        'label' => 'Keperluan Surat',
                        'key' => 'keperluan',
                        'type' => 'textarea',
                        'placeholder' => 'Pengurusan dokumen kependudukan atau SKCK',
                        'is_required' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Pilihan label preset untuk dropdown Filament.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::all() as $key => $preset) {
            $options[$key] = $preset['label'];
        }

        return $options;
    }

    public static function get(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }

    /**
     * Daftar placeholder standar yang dapat dipakai di dalam template surat.
     *
     * @return array<string, string>
     */
    public static function availablePlaceholders(): array
    {
        return [
            '{{applicant_name}}' => 'Nama lengkap pemohon',
            '{{applicant_nik}}' => 'NIK pemohon',
            '{{applicant_phone}}' => 'Nomor WhatsApp pemohon',
            '{{place_of_birth}}' => 'Tempat lahir pemohon',
            '{{date_of_birth}}' => 'Tanggal lahir pemohon',
            '{{gender}}' => 'Jenis kelamin pemohon',
            '{{religion}}' => 'Agama pemohon',
            '{{marital_status}}' => 'Status perkawinan pemohon',
            '{{job}}' => 'Pekerjaan pemohon',
            '{{address}}' => 'Alamat domisili pemohon',
            '{{rt}}' => 'Nomor RT',
            '{{rw}}' => 'Nomor RW',
            '{{dusun}}' => 'Nama dusun',
            '{{letter_number}}' => 'Nomor surat resmi',
            '{{letter_date}}' => 'Tanggal surat terformat',
            '{{village_name}}' => 'Nama desa',
            '{{district_name}}' => 'Nama kecamatan',
            '{{regency_name}}' => 'Nama kabupaten',
            '{{signer_name}}' => 'Nama penanda tangan',
            '{{signer_position}}' => 'Jabatan penanda tangan',
        ];
    }
}

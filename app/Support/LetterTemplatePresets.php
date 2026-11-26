<?php

namespace App\Support;

class LetterTemplatePresets
{
    /**
     * Katalog 10 layanan standar inti desa beserta template surat dan berkas persyaratan.
     *
     * @return array<string, array{
     *     label: string,
     *     title: string,
     *     category: string,
     *     sort_order: int,
     *     description: string,
     *     requirements: string,
     *     letter_code: string,
     *     letter_title: string,
     *     letter_template: string,
     *     form_fields: array<int, array{label: string, key: string, type: string, placeholder: string, is_required: bool}>,
     *     requirement_items: array<int, array{name: string, description: string, is_required: bool}>,
     *     aliases: array<int, string>
     * }>
     */
    public static function standardCatalog(): array
    {
        return [
            'domisili' => [
                'label' => 'Surat Keterangan Domisili',
                'title' => 'Surat Keterangan Domisili',
                'category' => 'Kependudukan',
                'sort_order' => 1,
                'description' => 'Surat keterangan resmi yang menyatakan alamat tempat tinggal warga di wilayah desa.',
                'requirements' => '<ol><li>KTP asli pemohon</li><li>Kartu Keluarga sebagai pendukung</li></ol>',
                'letter_code' => '470',
                'letter_title' => 'SURAT KETERANGAN DOMISILI',
                'letter_template' => '<p>Yang bertanda tangan di bawah ini {{signer_position}} {{village_name}}, Kecamatan {{district_name}}, Kabupaten {{regency_name}}, menerangkan bahwa:</p>
<table>
    <tr><td style="width: 170px;">Nama Lengkap</td><td style="width: 14px;">:</td><td><strong>{{applicant_name}}</strong></td></tr>
    <tr><td>NIK</td><td>:</td><td>{{applicant_nik}}</td></tr>
    <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>{{place_of_birth}}, {{date_of_birth}}</td></tr>
    <tr><td>Jenis Kelamin</td><td>:</td><td>{{gender}}</td></tr>
    <tr><td>Agama</td><td>:</td><td>{{religion}}</td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td>{{job}}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{address}} RT {{rt}} RW {{rw}}, Dusun {{dusun}}, Desa {{village_name}}</td></tr>
</table>
<p>Berdasarkan catatan pemerintah desa, orang tersebut di atas benar berdomisili di wilayah Desa {{village_name}} hingga surat ini diterbitkan.</p>
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
                'requirement_items' => [
                    ['name' => 'Foto KTP Pemohon', 'description' => 'Foto KTP asli yang terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto Kartu Keluarga', 'description' => 'Foto KK asli atau fotokopi', 'is_required' => false],
                ],
                'aliases' => [
                    'Keterangan Domisili',
                    'Surat Domisili',
                ],
            ],
            'ktp' => [
                'label' => 'Surat Pengantar KTP',
                'title' => 'Surat Pengantar KTP',
                'category' => 'Kependudukan',
                'sort_order' => 2,
                'description' => 'Surat pengantar resmi untuk perekaman, cetak ulang, atau pembaruan data KTP elektronik.',
                'requirements' => '<ol><li>Kartu Keluarga asli</li><li>Pengantar RT setempat jika diperlukan</li></ol>',
                'letter_code' => '474.4',
                'letter_title' => 'SURAT PENGANTAR KARTU TANDA PENDUDUK',
                'letter_template' => '<p>Yang bertanda tangan di bawah ini {{signer_position}} {{village_name}}, Kecamatan {{district_name}}, Kabupaten {{regency_name}}, memberikan pengantar kepada:</p>
<table>
    <tr><td style="width: 170px;">Nama Lengkap</td><td style="width: 14px;">:</td><td><strong>{{applicant_name}}</strong></td></tr>
    <tr><td>NIK</td><td>:</td><td>{{applicant_nik}}</td></tr>
    <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>{{place_of_birth}}, {{date_of_birth}}</td></tr>
    <tr><td>Jenis Kelamin</td><td>:</td><td>{{gender}}</td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td>{{job}}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{address}} RT {{rt}} RW {{rw}}, Dusun {{dusun}}, Desa {{village_name}}</td></tr>
</table>
<p>Surat pengantar ini diberikan untuk keperluan pengurusan KTP elektronik dengan alasan: <strong>{{jenis_pengajuan}}</strong>.</p>
<p>Demikian surat pengantar ini dibuat agar dapat diproses di instansi terkait sebagaimana mestinya.</p>',
                'form_fields' => [
                    [
                        'label' => 'Jenis Pengajuan KTP',
                        'key' => 'jenis_pengajuan',
                        'type' => 'text',
                        'placeholder' => 'Perekaman baru, cetak ulang rusak, atau hilang',
                        'is_required' => true,
                    ],
                ],
                'requirement_items' => [
                    ['name' => 'Foto Kartu Keluarga', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                    ['name' => 'Pengantar RT Setempat', 'description' => 'Foto surat pengantar RT', 'is_required' => false],
                ],
                'aliases' => [
                    'Pengantar KTP',
                    'Surat Keterangan KTP',
                ],
            ],
            'kk' => [
                'label' => 'Surat Pengantar Kartu Keluarga',
                'title' => 'Surat Pengantar Kartu Keluarga',
                'category' => 'Kependudukan',
                'sort_order' => 3,
                'description' => 'Surat pengantar resmi untuk penerbitan baru atau perubahan data Kartu Keluarga.',
                'requirements' => '<ol><li>Kartu Keluarga lama</li><li>KTP asli pemohon</li></ol>',
                'letter_code' => '474.1',
                'letter_title' => 'SURAT PENGANTAR KARTU KELUARGA',
                'letter_template' => '<p>Yang bertanda tangan di bawah ini {{signer_position}} {{village_name}}, Kecamatan {{district_name}}, Kabupaten {{regency_name}}, memberikan pengantar kepada:</p>
<table>
    <tr><td style="width: 170px;">Nama Lengkap</td><td style="width: 14px;">:</td><td><strong>{{applicant_name}}</strong></td></tr>
    <tr><td>NIK</td><td>:</td><td>{{applicant_nik}}</td></tr>
    <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>{{place_of_birth}}, {{date_of_birth}}</td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td>{{job}}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{address}} RT {{rt}} RW {{rw}}, Dusun {{dusun}}, Desa {{village_name}}</td></tr>
</table>
<p>Surat pengantar ini diberikan untuk keperluan: <strong>{{keperluan}}</strong>.</p>
<p>Demikian surat pengantar ini dibuat untuk dipergunakan sebagaimana mestinya.</p>',
                'form_fields' => [
                    [
                        'label' => 'Keperluan Perubahan KK',
                        'key' => 'keperluan',
                        'type' => 'textarea',
                        'placeholder' => 'Penambahan anggota keluarga atau pembaruan elemen data',
                        'is_required' => true,
                    ],
                ],
                'requirement_items' => [
                    ['name' => 'Foto Kartu Keluarga', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto KTP Pemohon', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                ],
                'aliases' => [
                    'Pengantar Perubahan KK',
                    'Pengantar KK',
                    'Surat Pengantar Perubahan KK',
                ],
            ],
            'kelahiran' => [
                'label' => 'Surat Keterangan Kelahiran',
                'title' => 'Surat Keterangan Kelahiran',
                'category' => 'Kependudukan',
                'sort_order' => 4,
                'description' => 'Surat keterangan resmi peristiwa kelahiran anak untuk pengurusan akta kelahiran.',
                'requirements' => '<ol><li>Surat keterangan lahir dari bidan atau rumah sakit</li><li>Kartu Keluarga orang tua</li><li>Buku nikah atau akta perkawinan orang tua</li></ol>',
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
    <tr><td>Nama Ibu</td><td style="width: 14px;">:</td><td>{{nama_ibu}}</td></tr>
    <tr><td>Alamat Orang Tua</td><td>:</td><td>{{address}} RT {{rt}} RW {{rw}}, Dusun {{dusun}}, Desa {{village_name}}</td></tr>
</table>
<p>Demikian surat keterangan kelahiran ini dibuat berdasarkan laporan pemohon {{applicant_name}} untuk dipergunakan sebagaimana mestinya.</p>',
                'form_fields' => [
                    ['label' => 'Nama Anak', 'key' => 'nama_anak', 'type' => 'text', 'placeholder' => 'Ahmad Fauzan', 'is_required' => true],
                    ['label' => 'Jenis Kelamin Anak', 'key' => 'jenis_kelamin_anak', 'type' => 'text', 'placeholder' => 'Laki-laki', 'is_required' => true],
                    ['label' => 'Tempat Lahir Anak', 'key' => 'tempat_lahir_anak', 'type' => 'text', 'placeholder' => 'Sinjai', 'is_required' => true],
                    ['label' => 'Tanggal Lahir Anak', 'key' => 'tanggal_lahir_anak', 'type' => 'date', 'placeholder' => '2026-11-20', 'is_required' => true],
                    ['label' => 'Nama Ayah', 'key' => 'nama_ayah', 'type' => 'text', 'placeholder' => 'Budi Santoso', 'is_required' => true],
                    ['label' => 'Nama Ibu', 'key' => 'nama_ibu', 'type' => 'text', 'placeholder' => 'Siti Aminah', 'is_required' => true],
                ],
                'requirement_items' => [
                    ['name' => 'Surat Keterangan Lahir', 'description' => 'Foto surat lahir dari bidan atau rumah sakit', 'is_required' => true],
                    ['name' => 'Foto Kartu Keluarga', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto Buku Nikah', 'description' => 'Foto halaman identitas buku nikah', 'is_required' => false],
                ],
                'aliases' => [
                    'Keterangan Kelahiran',
                    'Surat Kelahiran',
                ],
            ],
            'kematian' => [
                'label' => 'Surat Keterangan Kematian',
                'title' => 'Surat Keterangan Kematian',
                'category' => 'Kependudukan',
                'sort_order' => 5,
                'description' => 'Surat keterangan resmi peristiwa meninggal dunia untuk penerbitan akta kematian.',
                'requirements' => '<ol><li>KTP almarhum</li><li>Kartu Keluarga almarhum</li><li>KTP pelapor</li></ol>',
                'letter_code' => '472.12',
                'letter_title' => 'SURAT KETERANGAN KEMATIAN',
                'letter_template' => '<p>Yang bertanda tangan di bawah ini {{signer_position}} {{village_name}}, Kecamatan {{district_name}}, Kabupaten {{regency_name}}, menerangkan bahwa:</p>
<table>
    <tr><td style="width: 170px;">Nama Almarhum</td><td style="width: 14px;">:</td><td><strong>{{nama_almarhum}}</strong></td></tr>
    <tr><td>NIK Almarhum</td><td>:</td><td>{{nik_almarhum}}</td></tr>
    <tr><td>Umur</td><td>:</td><td>{{umur_almarhum}}</td></tr>
    <tr><td>Hari dan Tanggal Wafat</td><td>:</td><td>{{tanggal_meninggal}}</td></tr>
    <tr><td>Tempat Meninggal</td><td>:</td><td>{{tempat_meninggal}}</td></tr>
    <tr><td>Penyebab Kematian</td><td>:</td><td>{{penyebab_kematian}}</td></tr>
</table>
<p>Surat keterangan kematian ini diterbitkan berdasarkan laporan {{applicant_name}} untuk keperluan administrasi kependudukan dan ahli waris.</p>
<p>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>',
                'form_fields' => [
                    ['label' => 'Nama Almarhum', 'key' => 'nama_almarhum', 'type' => 'text', 'placeholder' => 'Nama lengkap almarhum', 'is_required' => true],
                    ['label' => 'NIK Almarhum', 'key' => 'nik_almarhum', 'type' => 'text', 'placeholder' => '16 digit NIK almarhum', 'is_required' => true],
                    ['label' => 'Umur Almarhum', 'key' => 'umur_almarhum', 'type' => 'text', 'placeholder' => '65 tahun', 'is_required' => true],
                    ['label' => 'Tanggal Meninggal', 'key' => 'tanggal_meninggal', 'type' => 'date', 'placeholder' => '2026-11-20', 'is_required' => true],
                    ['label' => 'Tempat Meninggal', 'key' => 'tempat_meninggal', 'type' => 'text', 'placeholder' => 'RSUD Sinjai atau Rumah Duka', 'is_required' => true],
                    ['label' => 'Penyebab Kematian', 'key' => 'penyebab_kematian', 'type' => 'text', 'placeholder' => 'Sakit atau usia lanjut', 'is_required' => true],
                ],
                'requirement_items' => [
                    ['name' => 'Foto KTP Almarhum', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto Kartu Keluarga', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto KTP Pelapor', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                ],
                'aliases' => [
                    'Keterangan Kematian',
                    'Surat Kematian',
                ],
            ],
            'pindah' => [
                'label' => 'Surat Keterangan Pindah Penduduk',
                'title' => 'Surat Keterangan Pindah Penduduk',
                'category' => 'Kependudukan',
                'sort_order' => 6,
                'description' => 'Surat pengantar resmi perpindahan domisili warga ke luar wilayah desa.',
                'requirements' => '<ol><li>KTP asli pemohon</li><li>Kartu Keluarga asli</li><li>Alamat lengkap tujuan pindah</li></ol>',
                'letter_code' => '475',
                'letter_title' => 'SURAT KETERANGAN PINDAH PENDUDUK',
                'letter_template' => '<p>Yang bertanda tangan di bawah ini {{signer_position}} {{village_name}}, Kecamatan {{district_name}}, Kabupaten {{regency_name}}, menerangkan bahwa:</p>
<table>
    <tr><td style="width: 170px;">Nama Lengkap</td><td style="width: 14px;">:</td><td><strong>{{applicant_name}}</strong></td></tr>
    <tr><td>NIK</td><td>:</td><td>{{applicant_nik}}</td></tr>
    <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>{{place_of_birth}}, {{date_of_birth}}</td></tr>
    <tr><td>Alamat Asal</td><td>:</td><td>{{address}} RT {{rt}} RW {{rw}}, Dusun {{dusun}}, Desa {{village_name}}</td></tr>
    <tr><td>Alamat Tujuan Pindah</td><td>:</td><td>{{alamat_tujuan}}</td></tr>
    <tr><td>Alasan Pindah</td><td>:</td><td>{{alasan_pindah}}</td></tr>
    <tr><td>Jumlah Pengikut</td><td>:</td><td>{{jumlah_pengikut}}</td></tr>
</table>
<p>Surat keterangan ini diterbitkan sebagai pengantar pengurusan surat pindah resmi pada Dinas Kependudukan dan Pencatatan Sipil.</p>',
                'form_fields' => [
                    ['label' => 'Alamat Tujuan Pindah', 'key' => 'alamat_tujuan', 'type' => 'textarea', 'placeholder' => 'Jalan, Kelurahan, Kecamatan, Kabupaten tujuan', 'is_required' => true],
                    ['label' => 'Alasan Pindah', 'key' => 'alasan_pindah', 'type' => 'text', 'placeholder' => 'Pekerjaan, keluarga, atau pendidikan', 'is_required' => true],
                    ['label' => 'Jumlah Pengikut', 'key' => 'jumlah_pengikut', 'type' => 'number', 'placeholder' => '0', 'is_required' => true],
                ],
                'requirement_items' => [
                    ['name' => 'Foto KTP Pemohon', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto Kartu Keluarga', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                ],
                'aliases' => [
                    'Keterangan Pindah Domisili',
                    'Surat Keterangan Pindah',
                    'Surat Pindah Penduduk',
                ],
            ],
            'nikah' => [
                'label' => 'Surat Pengantar Nikah',
                'title' => 'Surat Pengantar Nikah',
                'category' => 'Kependudukan',
                'sort_order' => 7,
                'description' => 'Surat pengantar resmi administrasi pernikahan ke Kantor Urusan Agama atau Catatan Sipil.',
                'requirements' => '<ol><li>KTP calon pengantin</li><li>Kartu Keluarga pemohon</li><li>Akta kelahiran atau ijazah terakhir</li></ol>',
                'letter_code' => '474.2',
                'letter_title' => 'SURAT PENGANTAR NIKAH',
                'letter_template' => '<p>Yang bertanda tangan di bawah ini {{signer_position}} {{village_name}}, Kecamatan {{district_name}}, Kabupaten {{regency_name}}, menerangkan bahwa:</p>
<table>
    <tr><td style="width: 170px;">Nama Lengkap</td><td style="width: 14px;">:</td><td><strong>{{applicant_name}}</strong></td></tr>
    <tr><td>NIK</td><td>:</td><td>{{applicant_nik}}</td></tr>
    <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>{{place_of_birth}}, {{date_of_birth}}</td></tr>
    <tr><td>Status Perkawinan</td><td>:</td><td>{{marital_status}}</td></tr>
    <tr><td>Pekerjaan</td><td>:</td><td>{{job}}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{address}} RT {{rt}} RW {{rw}}, Dusun {{dusun}}, Desa {{village_name}}</td></tr>
    <tr><td>Nama Calon Pasangan</td><td>:</td><td>{{nama_pasangan}}</td></tr>
</table>
<p>Surat pengantar ini diberikan untuk keperluan pendaftaran pernikahan di <strong>{{tujuan_kua}}</strong>.</p>
<p>Demikian surat pengantar nikah ini dibuat untuk dipergunakan sebagaimana mestinya.</p>',
                'form_fields' => [
                    ['label' => 'Nama Calon Pasangan', 'key' => 'nama_pasangan', 'type' => 'text', 'placeholder' => 'Nama lengkap calon pasangan', 'is_required' => true],
                    ['label' => 'KUA Tujuan', 'key' => 'tujuan_kua', 'type' => 'text', 'placeholder' => 'KUA Kecamatan setempat', 'is_required' => true],
                ],
                'requirement_items' => [
                    ['name' => 'Foto KTP Pemohon', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto Kartu Keluarga', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto Akta Kelahiran', 'description' => 'Foto akta kelahiran atau ijazah terakhir', 'is_required' => false],
                ],
                'aliases' => [
                    'Pengantar Nikah',
                ],
            ],
            'usaha' => [
                'label' => 'Surat Keterangan Usaha',
                'title' => 'Surat Keterangan Usaha',
                'category' => 'Ekonomi dan Sosial',
                'sort_order' => 8,
                'description' => 'Surat keterangan resmi kepemilikan usaha warga untuk keperluan perbankan atau perizinan.',
                'requirements' => '<ol><li>KTP asli pemohon</li><li>Kartu Keluarga</li><li>Foto tampak depan lokasi usaha</li></ol>',
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
                    ['label' => 'Nama Usaha', 'key' => 'nama_usaha', 'type' => 'text', 'placeholder' => 'Warung Sembako Berkah', 'is_required' => true],
                    ['label' => 'Bidang Usaha', 'key' => 'bidang_usaha', 'type' => 'text', 'placeholder' => 'Perdagangan eceran', 'is_required' => true],
                    ['label' => 'Alamat Usaha', 'key' => 'alamat_usaha', 'type' => 'textarea', 'placeholder' => 'Dusun setempat', 'is_required' => true],
                    ['label' => 'Keperluan Surat', 'key' => 'keperluan', 'type' => 'text', 'placeholder' => 'Pengajuan kredit usaha rakyat', 'is_required' => true],
                ],
                'requirement_items' => [
                    ['name' => 'Foto KTP Pemohon', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto Kartu Keluarga', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto Tempat Usaha', 'description' => 'Foto tampak depan lokasi usaha', 'is_required' => true],
                ],
                'aliases' => [
                    'Keterangan Usaha',
                ],
            ],
            'tidak_mampu' => [
                'label' => 'Surat Keterangan Tidak Mampu',
                'title' => 'Surat Keterangan Tidak Mampu',
                'category' => 'Ekonomi dan Sosial',
                'sort_order' => 9,
                'description' => 'Surat keterangan kondisi ekonomi keluarga untuk pengajuan beasiswa atau layanan kesehatan.',
                'requirements' => '<ol><li>KTP asli pemohon</li><li>Kartu Keluarga</li><li>Foto tampak depan kondisi rumah</li></ol>',
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
                    ['label' => 'Keperluan Surat', 'key' => 'keperluan', 'type' => 'textarea', 'placeholder' => 'Pengajuan beasiswa pendidikan atau layanan kesehatan', 'is_required' => true],
                ],
                'requirement_items' => [
                    ['name' => 'Foto KTP Pemohon', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto Kartu Keluarga', 'description' => 'Foto asli terbaca jelas', 'is_required' => true],
                    ['name' => 'Foto Kondisi Rumah', 'description' => 'Foto tampak depan rumah tinggal', 'is_required' => false],
                ],
                'aliases' => [
                    'Surat Keterangan Kurang Mampu',
                    'Keterangan Tidak Mampu',
                    'SKTM',
                ],
            ],
            'izin_keramaian' => [
                'label' => 'Surat Pengantar Izin Keramaian',
                'title' => 'Surat Pengantar Izin Keramaian',
                'category' => 'Perizinan dan Umum',
                'sort_order' => 10,
                'description' => 'Surat pengantar resmi permohonan izin kegiatan masyarakat kepada pihak kepolisian.',
                'requirements' => '<ol><li>KTP penanggung jawab kegiatan</li><li>Jadwal dan lokasi pelaksanaan kegiatan</li></ol>',
                'letter_code' => '300',
                'letter_title' => 'SURAT PENGANTAR IZIN KERAMAIAN',
                'letter_template' => '<p>Yang bertanda tangan di bawah ini {{signer_position}} {{village_name}}, Kecamatan {{district_name}}, Kabupaten {{regency_name}}, memberikan rekomendasi pengantar izin keramaian kepada:</p>
<table>
    <tr><td style="width: 170px;">Nama Penanggung Jawab</td><td style="width: 14px;">:</td><td><strong>{{applicant_name}}</strong></td></tr>
    <tr><td>NIK</td><td>:</td><td>{{applicant_nik}}</td></tr>
    <tr><td>Alamat</td><td>:</td><td>{{address}} RT {{rt}} RW {{rw}}, Dusun {{dusun}}, Desa {{village_name}}</td></tr>
    <tr><td>Nama Kegiatan</td><td>:</td><td><strong>{{nama_kegiatan}}</strong></td></tr>
    <tr><td>Tanggal Pelaksanaan</td><td>:</td><td>{{tanggal_kegiatan}}</td></tr>
    <tr><td>Lokasi Kegiatan</td><td>:</td><td>{{lokasi_kegiatan}}</td></tr>
</table>
<p>Penanggung jawab wajib menjaga ketertiban, keamanan, dan norma masyarakat selama kegiatan berlangsung.</p>
<p>Demikian surat pengantar ini dibuat untuk diteruskan kepada pihak berwenang sebagaimana mestinya.</p>',
                'form_fields' => [
                    ['label' => 'Nama Kegiatan', 'key' => 'nama_kegiatan', 'type' => 'text', 'placeholder' => 'Resepsi pernikahan atau turnamen olahraga', 'is_required' => true],
                    ['label' => 'Tanggal Pelaksanaan', 'key' => 'tanggal_kegiatan', 'type' => 'date', 'placeholder' => '2026-12-01', 'is_required' => true],
                    ['label' => 'Lokasi Kegiatan', 'key' => 'lokasi_kegiatan', 'type' => 'textarea', 'placeholder' => 'Alamat lengkap lokasi acara', 'is_required' => true],
                ],
                'requirement_items' => [
                    ['name' => 'Foto KTP Penanggung Jawab', 'description' => 'Foto KTP asli penanggung jawab acara', 'is_required' => true],
                ],
                'aliases' => [
                    'Pengantar Izin Keramaian',
                    'Surat Izin Keramaian',
                ],
            ],
        ];
    }

    /**
     * Seluruh preset template surat untuk dropdown Filament termasuk preset pengantar umum.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        $catalog = self::standardCatalog();
        $catalog['pengantar'] = [
            'label' => 'Surat Pengantar Umum',
            'title' => 'Surat Pengantar Umum',
            'category' => 'Perizinan dan Umum',
            'sort_order' => 11,
            'description' => 'Surat pengantar umum untuk keperluan administrasi warga.',
            'requirements' => '<ol><li>KTP pemohon</li><li>Kartu Keluarga</li></ol>',
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
                ['label' => 'Keperluan Surat', 'key' => 'keperluan', 'type' => 'textarea', 'placeholder' => 'Pengurusan dokumen kependudukan atau SKCK', 'is_required' => true],
            ],
            'requirement_items' => [
                ['name' => 'Foto KTP Pemohon', 'description' => 'Foto KTP asli yang terbaca jelas', 'is_required' => true],
            ],
            'aliases' => [],
        ];

        return $catalog;
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

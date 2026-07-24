<?php

namespace App\Filament\Resources\Citizens\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Checkbox;

class CitizenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([

                        // ─── Tab 1: Identitas Warga ───────────────────────────────────────
                        Tab::make('Identitas Warga')
                            ->schema([
                                Section::make('Data Kartu Keluarga')
                                    ->description('Keterkaitan warga dengan Kartu Keluarga')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextInput::make('nik')->label('NIK')
                                                    ->required()
                                                    ->unique(ignoreRecord: true),
                                                Select::make('family_id')->label('Kartu Keluarga (KK)')
                                                    ->relationship('family', 'kk_number')
                                                    ->searchable()
                                                    ->preload()
                                                    ->helperText('Hubungkan dengan KK yang ada di database'),
                                                TextInput::make('kk_order')->label('Nomor Urut Anggota (dari KK)')
                                                    ->numeric(),
                                            ]),
                                    ]),

                                Section::make('Data Pribadi')
                                    ->description('Informasi dasar kependudukan warga')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('name')->label('Nama Lengkap')
                                                    ->required(),
                                                Select::make('gender')->label('Jenis Kelamin')
                                                    ->options([
                                                        'Laki-laki' => 'Laki-laki',
                                                        'Perempuan' => 'Perempuan',
                                                    ]),
                                                DatePicker::make('date_of_birth')->label('Tanggal Lahir'),
                                                Select::make('marital_status')->label('Status Perkawinan')
                                                    ->options([
                                                        'Belum Kawin' => 'Belum Kawin',
                                                        'Kawin' => 'Kawin',
                                                        'Cerai Hidup' => 'Cerai Hidup',
                                                        'Cerai Mati' => 'Cerai Mati',
                                                    ]),
                                                Select::make('family_relation')->label('Hubungan dengan Kepala Keluarga')
                                                    ->options([
                                                        'Kepala Keluarga' => 'Kepala Keluarga',
                                                        'Istri' => 'Istri',
                                                        'Anak' => 'Anak',
                                                        'Menantu' => 'Menantu',
                                                        'Cucu' => 'Cucu',
                                                        'Orang Tua' => 'Orang Tua',
                                                        'Mertua' => 'Mertua',
                                                        'Famili Lain' => 'Famili Lain',
                                                        'Pembantu' => 'Pembantu',
                                                        'Lainnya' => 'Lainnya',
                                                    ]),
                                            ]),
                                    ]),
                            ]),

                        // ─── Tab 2: Pendidikan & Pekerjaan ───────────────────────────────
                        Tab::make('Pendidikan & Pekerjaan')
                            ->schema([
                                Section::make('Pendidikan')
                                    ->description('Riwayat dan capaian pendidikan')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('school_participation')->label('Partisipasi Sekolah')
                                                    ->options([
                                                        'Tidak / Belum Pernah Sekolah' => 'Tidak / Belum Pernah Sekolah',
                                                        'Masih Sekolah' => 'Masih Sekolah',
                                                        'Tidak Bersekolah Lagi' => 'Tidak Bersekolah Lagi',
                                                    ]),
                                                Select::make('education_level')->label('Ijazah Tertinggi yang Dimiliki')
                                                    ->options([
                                                        'Tidak Punya Ijazah SD' => 'Tidak Punya Ijazah SD',
                                                        'SD / Sederajat' => 'SD / Sederajat',
                                                        'SMP / Sederajat' => 'SMP / Sederajat',
                                                        'SMA / Sederajat' => 'SMA / Sederajat',
                                                        'D1 / D2 / D3' => 'D1 / D2 / D3',
                                                        'D4 / S1 / Profesi' => 'D4 / S1 / Profesi',
                                                        'S2 / S3' => 'S2 / S3',
                                                    ])
                                                    ->searchable(),
                                            ]),
                                    ]),

                                Section::make('Pekerjaan')
                                    ->description('Informasi pekerjaan utama')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('job')->label('Profesi Pekerjaan Utama'),
                                                Select::make('job_status')->label('Kedudukan dalam Pekerjaan Utama')
                                                    ->options([
                                                        'Berusaha Sendiri' => 'Berusaha Sendiri',
                                                        'Buruh / Karyawan / Pegawai Swasta' => 'Buruh / Karyawan / Pegawai Swasta',
                                                        'Pekerja Bebas' => 'Pekerja Bebas',
                                                        'Pekerja Keluarga / Tidak Dibayar' => 'Pekerja Keluarga / Tidak Dibayar',
                                                        'ASN / TNI / Polri / BUMN / BUMD / Pejabat Negara' => 'ASN / TNI / Polri / BUMN / BUMD / Pejabat Negara',
                                                        'Berusaha Dibantu Buruh' => 'Berusaha Dibantu Buruh',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->searchable(),
                                            ]),
                                    ]),
                            ]),

                        // ─── Tab 3: Pendapatan & Keuangan ────────────────────────────────
                        Tab::make('Pendapatan & Keuangan')
                            ->schema([
                                Section::make('Status Pendapatan & Keuangan')
                                    ->description('Informasi umum keuangan warga')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('has_income')->label('Apakah Memiliki Pendapatan?')
                                                    ->options([1 => 'Ya', 0 => 'Tidak']),
                                                Select::make('has_digital_wallet')->label('Rekening / Dompet Digital Aktif')
                                                    ->options([
                                                        'Tidak ada' => 'Tidak Ada',
                                                        'Ya untuk pribadi' => 'Ya untuk Pribadi',
                                                        'Ya untuk usaha dan pribadi' => 'Ya untuk Usaha & Pribadi',
                                                        'Ya untuk usaha' => 'Ya untuk Usaha',
                                                    ]),
                                            ]),
                                    ]),

                                Section::make('Rincian Pendapatan Sebulan')
                                    ->description('Semua sumber pendapatan dalam satu bulan (Rp)')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('income_salary')->label('Gaji / Upah (Rp)')->numeric()->default(0),
                                                TextInput::make('income_allowance')->label('Tunjangan (Rp)')->numeric()->default(0),
                                                TextInput::make('income_food')->label('Uang Makan (Rp)')->numeric()->default(0),
                                                TextInput::make('income_honor')->label('Honor (Rp)')->numeric()->default(0),
                                                TextInput::make('income_overtime')->label('Lembur (Rp)')->numeric()->default(0),
                                                TextInput::make('income_business')->label('Pendapatan Usaha (Rp)')->numeric()->default(0),
                                                TextInput::make('income_passive')->label('Passive Income / Lainnya (Rp)')->numeric()->default(0),
                                                TextInput::make('income_other')->label('Pendapatan Lainnya (Rp)')->numeric()->default(0),
                                            ]),
                                    ]),
                            ]),

                        // ─── Tab 4: Kesehatan & Disabilitas ──────────────────────────────
                        Tab::make('Kesehatan & Disabilitas')
                            ->schema([
                                Section::make('Jaminan Kesehatan & Bantuan')
                                    ->description('Status kepesertaan jaminan sosial')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('bpjs_status')->label('Kepesertaan JKN KIS (BPJS)')
                                                    ->options([
                                                        'BPJS PBI Pemda' => 'BPJS PBI Pemda',
                                                        'BPJS Mandiri' => 'BPJS Mandiri',
                                                        'BPJS PBI Tunjangan Pemerintah Pusat' => 'BPJS PBI Tunjangan Pemerintah Pusat',
                                                        'Tidak Terdaftar' => 'Tidak Terdaftar',
                                                    ])
                                                    ->searchable(),
                                                Select::make('pip_status')->label('Menerima Bantuan PIP?')
                                                    ->options([1 => 'Ya', 0 => 'Tidak']),
                                            ]),
                                    ]),

                                Section::make('Disabilitas')
                                    ->description('Kondisi disabilitas yang dialami warga')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('disability_physical')->label('Disabilitas Fisik')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('disability_mental')->label('Disabilitas Mental')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('disability_intellectual')->label('Disabilitas Intelektual')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('disability_blind')->label('Disabilitas Sensorik Netra')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('disability_deaf')->label('Disabilitas Sensorik Rungu')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('disability_speech')->label('Disabilitas Sensorik Wicara')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                            ]),
                                    ]),

                                Section::make('Keluhan Penyakit Kronis')
                                    ->description('Riwayat penyakit kronis yang diderita')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('illness_hypertension')->label('Hipertensi')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_rheumatic')->label('Rematik')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_asthma')->label('Asma')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_heart')->label('Masalah Jantung')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_diabetes')->label('Diabetes')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_tbc')->label('TBC')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_stroke')->label('Stroke')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_cancer')->label('Kanker')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_kidney')->label('Gagal Ginjal')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_hemophilia')->label('Hemofilia')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_hiv')->label('HIV/AIDS')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_cholesterol')->label('Kolesterol')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_liver')->label('Sirosis Hati')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_thalassemia')->label('Talasemia')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_leukemia')->label('Leukemia')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_alzheimer')->label('Alzheimer')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_other')->label('Penyakit Kronis Lainnya')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                            ]),
                                    ]),
                            ]),

                        // ─── Tab 5: Kependudukan ──────────────────────────────────────────
                        Tab::make('Kependudukan')
                            ->schema([
                                Section::make('Lokasi Domisili')
                                    ->description('Tempat tinggal warga saat ini')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('dusun_id')->label('Dusun')
                                                    ->relationship('dusun', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                                TextInput::make('rt')->label('RT'),
                                                TextInput::make('rw')->label('RW'),
                                            ]),
                                        Textarea::make('address')->label('Alamat Domisili')
                                            ->columnSpanFull(),
                                        Select::make('domicile_address_type')->label('Status Kecocokan Domisili')
                                            ->options([
                                                'Sesuai KK dan KTP' => 'Sesuai KK dan KTP',
                                                'Hanya sesuai KK' => 'Hanya sesuai KK',
                                                'Hanya sesuai KTP' => 'Hanya sesuai KTP',
                                            ]),
                                    ]),

                                Section::make('Status Kependudukan')
                                    ->description('Status keberadaan dan keaktifan warga')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('citizenship_status')->label('Keberadaan Anggota Keluarga')
                                                    ->options([
                                                        'Tinggal di rumah/tempat tinggal ini' => 'Tinggal di rumah/tempat tinggal ini',
                                                        'Pindah' => 'Pindah',
                                                        'Meninggal' => 'Meninggal',
                                                        'Tidak tinggal di rumah ini' => 'Tidak tinggal di rumah ini',
                                                    ]),
                                                Select::make('status')->label('Status Keaktifan')
                                                    ->options([
                                                        'Aktif' => 'Aktif',
                                                        'Pindah' => 'Pindah',
                                                        'Meninggal' => 'Meninggal',
                                                    ])
                                                    ->required()
                                                    ->default('Aktif'),
                                            ]),
                                    ]),
                            ]),

                    ])
                    ->columnSpanFull(),
            ]);
    }
}

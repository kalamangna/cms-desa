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
use Filament\Forms\Components\CheckboxList;

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
                                                    ->placeholder('Contoh: 7306010506800001')
                                                    ->helperText('Nomor Induk Kependudukan 16 digit.')
                                                    ->required()
                                                    ->unique(ignoreRecord: true),
                                                Select::make('family_id')->label('Kartu Keluarga (KK)')
                                                    ->placeholder('Pilih Kartu Keluarga')
                                                    ->relationship('family', 'kk_number')
                                                    ->searchable()
                                                    ->preload()
                                                    ->helperText('Hubungkan dengan database KK.'),
                                                TextInput::make('kk_order')->label('Nomor Urut Anggota (dari KK)')
                                                    ->placeholder('Contoh: 1')
                                                    ->helperText('Nomor urut posisi warga di KK.')
                                                    ->numeric(),
                                            ]),
                                    ]),

                                Section::make('Data Pribadi')
                                    ->description('Informasi dasar kependudukan warga')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextInput::make('name')->label('Nama Lengkap')
                                                    ->placeholder('Contoh: Andi Muhammad')
                                                    ->helperText('Nama lengkap warga sesuai dokumen kependudukan.')
                                                    ->required(),
                                                Select::make('gender')->label('Jenis Kelamin')
                                                    ->placeholder('Pilih Jenis Kelamin')
                                                    ->helperText('Jenis kelamin warga.')
                                                    ->options([
                                                        'Laki-laki' => 'Laki-laki',
                                                        'Perempuan' => 'Perempuan',
                                                    ]),
                                                DatePicker::make('date_of_birth')->label('Tanggal Lahir')
                                                    ->placeholder('Pilih Tanggal Lahir')
                                                    ->helperText('Tanggal lahir warga.'),
                                                Select::make('marital_status')->label('Status Perkawinan')
                                                    ->placeholder('Pilih Status Perkawinan')
                                                    ->helperText('Status hubungan perkawinan saat ini.')
                                                    ->options([
                                                        'Belum Kawin' => 'Belum Kawin',
                                                        'Kawin' => 'Kawin',
                                                        'Cerai Hidup' => 'Cerai Hidup',
                                                        'Cerai Mati' => 'Cerai Mati',
                                                    ]),
                                                Select::make('family_relation')->label('Hubungan dengan Kepala Keluarga')
                                                    ->placeholder('Pilih Hubungan Keluarga')
                                                    ->helperText('Status posisi hubungan dalam susunan KK.')
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
                                                    ->placeholder('Pilih Partisipasi Sekolah')
                                                    ->helperText('Status keikutsertaan dalam pendidikan formal.')
                                                    ->options([
                                                        'Tidak / Belum Pernah Sekolah' => 'Tidak / Belum Pernah Sekolah',
                                                        'Masih Sekolah' => 'Masih Sekolah',
                                                        'Tidak Bersekolah Lagi' => 'Tidak Bersekolah Lagi',
                                                    ]),
                                                Select::make('education_level')->label('Ijazah Tertinggi yang Dimiliki')
                                                    ->placeholder('Pilih Ijazah Tertinggi')
                                                    ->helperText('Tingkat pendidikan/ijazah resmi terakhir.')
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
                                                TextInput::make('job')->label('Profesi Pekerjaan Utama')
                                                    ->placeholder('Contoh: Petani, Pedagang, atau PNS')
                                                    ->helperText('Profesi atau mata pencaharian utama.'),
                                                Select::make('job_status')->label('Kedudukan dalam Pekerjaan Utama')
                                                    ->placeholder('Pilih Kedudukan Pekerjaan')
                                                    ->helperText('Status hubungan kerja dalam profesi utama.')
                                                    ->options([
                                                        'Tidak Bekerja / Lainnya' => 'Tidak Bekerja / Lainnya',
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
                                                    ->placeholder('Pilih Kepemilikan Pendapatan')
                                                    ->helperText('Apakah warga memiliki sumber penghasilan rutin.')
                                                    ->options([1 => 'Ya', 0 => 'Tidak']),
                                                Select::make('has_digital_wallet')->label('Rekening / Dompet Digital Aktif')
                                                    ->placeholder('Pilih Akses Perbankan/E-Wallet')
                                                    ->helperText('Kepemilikan rekening bank atau dompet digital.')
                                                    ->options([
                                                        'Tidak ada' => 'Tidak Ada',
                                                        'Ya untuk pribadi' => 'Ya untuk Pribadi',
                                                        'Ya untuk usaha dan pribadi' => 'Ya untuk Usaha & Pribadi',
                                                        'Ya untuk usaha' => 'Ya untuk Usaha',
                                                    ]),
                                            ]),
                                    ]),

                                Section::make('Rincian Pendapatan Sebulan')
                                    ->description('Sumber pendapatan per bulan (Rp).')
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                TextInput::make('income_salary')->label('Gaji / Upah (Rp)')
                                                    ->placeholder('Contoh: 2500000')
                                                    ->helperText('Penghasilan rutin per bulan.')
                                                    ->numeric()->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->prefix('Rp')->default(0),
                                                TextInput::make('income_allowance')->label('Tunjangan (Rp)')
                                                    ->placeholder('Contoh: 500000')
                                                    ->helperText('Tunjangan pekerjaan.')
                                                    ->numeric()->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->prefix('Rp')->default(0),
                                                TextInput::make('income_food')->label('Uang Makan (Rp)')
                                                    ->placeholder('Contoh: 300000')
                                                    ->helperText('Uang konsumsi/makan.')
                                                    ->numeric()->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->prefix('Rp')->default(0),
                                                TextInput::make('income_honor')->label('Honor (Rp)')
                                                    ->placeholder('Contoh: 200000')
                                                    ->helperText('Honorarium kegiatan.')
                                                    ->numeric()->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->prefix('Rp')->default(0),
                                                TextInput::make('income_overtime')->label('Lembur (Rp)')
                                                    ->placeholder('Contoh: 150000')
                                                    ->helperText('Upah kerja lembur.')
                                                    ->numeric()->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->prefix('Rp')->default(0),
                                                TextInput::make('income_business')->label('Pendapatan Usaha (Rp)')
                                                    ->placeholder('Contoh: 1000000')
                                                    ->helperText('Keuntungan usaha mandiri.')
                                                    ->numeric()->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->prefix('Rp')->default(0),
                                                TextInput::make('income_passive')->label('Passive Income (Rp)')
                                                    ->placeholder('Contoh: 0')
                                                    ->helperText('Hasil sewa/investasi.')
                                                    ->numeric()->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->prefix('Rp')->default(0),
                                                TextInput::make('income_other')->label('Pendapatan Lainnya (Rp)')
                                                    ->placeholder('Contoh: 0')
                                                    ->helperText('Sumber pendapatan lain.')
                                                    ->numeric()->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->prefix('Rp')->default(0),
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
                                                    ->placeholder('Pilih Status BPJS')
                                                    ->helperText('Jenis kepesertaan jaminan kesehatan nasional.')
                                                    ->options([
                                                        'BPJS PBI Pemda' => 'BPJS PBI Pemda',
                                                        'BPJS Mandiri' => 'BPJS Mandiri',
                                                        'BPJS PBI Tunjangan Pemerintah Pusat' => 'BPJS PBI Tunjangan Pemerintah Pusat',
                                                        'Tidak Terdaftar' => 'Tidak Terdaftar',
                                                    ])
                                                    ->searchable(),
                                                Select::make('pip_status')->label('Menerima Bantuan PIP?')
                                                    ->placeholder('Pilih Status PIP')
                                                    ->helperText('Status kepesertaan Program Indonesia Pintar.')
                                                    ->options([1 => 'Ya', 0 => 'Tidak']),
                                            ]),
                                    ]),

                                Section::make('Disabilitas')
                                    ->description('Ragam disabilitas warga (jika ada).')
                                    ->schema([
                                        CheckboxList::make('disabilities')
                                            ->label('Ragam Penyandang Disabilitas')
                                            ->helperText('Centang ragam disabilitas (jika ada).')
                                            ->options([
                                                'physical' => 'Disabilitas Fisik',
                                                'mental' => 'Disabilitas Mental',
                                                'intellectual' => 'Disabilitas Intelektual',
                                                'blind' => 'Disabilitas Sensorik Netra',
                                                'deaf' => 'Disabilitas Sensorik Rungu',
                                                'speech' => 'Disabilitas Sensorik Wicara',
                                            ])
                                            ->formatStateUsing(function ($record) {
                                                if (! $record) return [];
                                                $selected = [];
                                                if ($record->disability_physical) $selected[] = 'physical';
                                                if ($record->disability_mental) $selected[] = 'mental';
                                                if ($record->disability_intellectual) $selected[] = 'intellectual';
                                                if ($record->disability_blind) $selected[] = 'blind';
                                                if ($record->disability_deaf) $selected[] = 'deaf';
                                                if ($record->disability_speech) $selected[] = 'speech';
                                                return $selected;
                                            })
                                            ->dehydrateStateUsing(function ($state, $record) {
                                                // Handling dehydration directly in mutator or via form state logic if needed
                                            })
                                            ->columns(3)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Keluhan Penyakit Kronis')
                                    ->description('Penyakit kronis yang diderita warga.')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                Select::make('illness_hypertension')->label('Hipertensi')->placeholder('Pilih Status')->helperText('Tekanan darah tinggi.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_rheumatic')->label('Rematik')->placeholder('Pilih Status')->helperText('Rematik/nyeri sendi.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_asthma')->label('Asma')->placeholder('Pilih Status')->helperText('Sesak napas/asma.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_heart')->label('Masalah Jantung')->placeholder('Pilih Status')->helperText('Penyakit jantung.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_diabetes')->label('Diabetes')->placeholder('Pilih Status')->helperText('Gula darah/kencing manis.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_tbc')->label('TBC')->placeholder('Pilih Status')->helperText('Tuberkulosis.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_stroke')->label('Stroke')->placeholder('Pilih Status')->helperText('Penyakit stroke.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_cancer')->label('Kanker')->placeholder('Pilih Status')->helperText('Penyakit kanker/tumor.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_kidney')->label('Gagal Ginjal')->placeholder('Pilih Status')->helperText('Gangguan ginjal kronis.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_hemophilia')->label('Hemofilia')->placeholder('Pilih Status')->helperText('Kelainan pembekuan darah.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_hiv')->label('HIV/AIDS')->placeholder('Pilih Status')->helperText('Infeksi HIV/AIDS.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_cholesterol')->label('Kolesterol')->placeholder('Pilih Status')->helperText('Kadar kolesterol tinggi.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_liver')->label('Sirosis Hati')->placeholder('Pilih Status')->helperText('Gangguan fungsi hati.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_thalassemia')->label('Talasemia')->placeholder('Pilih Status')->helperText('Kelainan sel darah merah.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_leukemia')->label('Leukemia')->placeholder('Pilih Status')->helperText('Kanker darah.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_alzheimer')->label('Alzheimer')->placeholder('Pilih Status')->helperText('Penurunan fungsi otak/pikun.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                                Select::make('illness_other')->label('Penyakit Kronis Lainnya')->placeholder('Pilih Status')->helperText('Penyakit menahun lainnya.')->options([1 => 'Ya', 0 => 'Tidak'])->default(0),
                                            ]),
                                    ]),
                            ]),

                        // ─── Tab 5: Kependudukan ──────────────────────────────────────────
                        Tab::make('Kependudukan')
                            ->schema([
                                Section::make('Lokasi Domisili')
                                    ->description('Tempat tinggal warga saat ini')
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                Select::make('dusun_id')->label('Dusun')
                                                    ->placeholder('Pilih Dusun')
                                                    ->helperText('Wilayah dusun domisili.')
                                                    ->relationship('dusun', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                                TextInput::make('rt')->label('RT')
                                                    ->placeholder('Contoh: 001')
                                                    ->helperText('Nomor RT.'),
                                                TextInput::make('rw')->label('RW')
                                                    ->placeholder('Contoh: 002')
                                                    ->helperText('Nomor RW.'),
                                                Select::make('domicile_address_type')->label('Status Kecocokan Domisili')
                                                    ->placeholder('Pilih Kesesuaian Domisili')
                                                    ->helperText('Kesesuaian alamat domisili dengan KK/KTP.')
                                                    ->options([
                                                        'Sesuai KK dan KTP' => 'Sesuai KK dan KTP',
                                                        'Hanya sesuai KK' => 'Hanya sesuai KK',
                                                        'Hanya sesuai KTP' => 'Hanya sesuai KTP',
                                                        'Tidak sesuai KK dan KTP' => 'Tidak sesuai KK dan KTP',
                                                    ]),
                                            ]),
                                        Textarea::make('address')->label('Alamat Domisili')
                                            ->placeholder('Contoh: Jl. Poros Desa No. 12, Dusun Karawa')
                                            ->helperText('Alamat domisili lengkap warga saat ini.')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Status Kependudukan')
                                    ->description('Status keberadaan dan keaktifan warga')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                Select::make('citizenship_status')->label('Keberadaan Anggota Keluarga')
                                                    ->placeholder('Pilih Keberadaan Warga')
                                                    ->helperText('Status tempat tinggal fisik anggota keluarga.')
                                                    ->options([
                                                        'Tinggal di Rumah Ini' => 'Tinggal di Rumah Ini',
                                                        'Sudah Pisah KK' => 'Sudah Pisah KK',
                                                        'Pindah ke Daerah Lain (Indonesia)' => 'Pindah ke Daerah Lain (Indonesia)',
                                                        'Pindah ke Luar Negeri' => 'Pindah ke Luar Negeri',
                                                        'Meninggal' => 'Meninggal',
                                                        'Pindah' => 'Pindah',
                                                    ])
                                                    ->searchable(),
                                                Select::make('status')->label('Status Keaktifan')
                                                    ->placeholder('Pilih Status Keaktifan')
                                                    ->helperText('Status keaktifan dalam database desa.')
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

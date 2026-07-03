<?php

namespace App\Filament\Resources\Citizens\Pages;

use App\Filament\Resources\Citizens\CitizenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Models\Citizen;
use App\Models\Family;
use App\Models\Dusun;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ListCitizens extends ListRecords
{
    protected static string $resource = CitizenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('importCsv')
                ->label('Import Excel / CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    Placeholder::make('info')
                        ->label('Petunjuk Penggunaan')
                        ->content('Unggah respon kuesioner Google Form (Individu) secara langsung dalam format Excel (.xlsx / .xls) atau CSV (.csv).'),
                    Select::make('dusun_id')
                        ->label('Pilih Dusun')
                        ->options(Dusun::pluck('name', 'id'))
                        ->placeholder('Pilih Dusun...')
                        ->searchable()
                        ->preload()
                        ->required(),
                    FileUpload::make('csv_file')
                        ->label('File Excel atau CSV')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                            'text/plain',
                            'application/csv'
                        ])
                        ->required()
                        ->directory('temp'),
                ])
                ->action(function (array $data) {
                    $filePath = storage_path('app/public/' . $data['csv_file']);
                    $selectedDusunId = $data['dusun_id'] ?? null;
                    
                    if (!file_exists($filePath)) {
                        Notification::make()->title('File tidak ditemukan.')->danger()->send();
                        return;
                    }

                    try {
                        $spreadsheet = IOFactory::load($filePath);
                        $worksheet = $spreadsheet->getActiveSheet();
                        $rows = $worksheet->toArray(null, true, true, false);
                    } catch (\Exception $e) {
                        Notification::make()->title('Gagal membaca file: ' . $e->getMessage())->danger()->send();
                        return;
                    }

                    if (empty($rows)) {
                        Notification::make()->title('File kosong atau tidak valid.')->danger()->send();
                        return;
                    }

                    $header = array_shift($rows);

                    // Normalize headers
                    $header = array_map(function($h) {
                        return trim(strtolower($h));
                    }, $header);

                    // Validate if it is a family sheet uploaded to citizen form
                    $isFamilySheet = $this->findColumnIndex($header, ['101. nama kepala keluarga', '201. jenis bangunan']) !== false;
                    $isCitizenSheet = $this->findColumnIndex($header, ['302. nik anggota', '306. jenis kelamin']) !== false;
                    
                    if ($isFamilySheet || !$isCitizenSheet) {
                        Notification::make()
                            ->title('Gagal: File Salah / Tertukar!')
                            ->body('Anda mengunggah file data KELUARGA (atau file dengan format salah) di form Penduduk. Harap unggah file data INDIVIDU/PENDUDUK.')
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }

                    // Map columns dynamically
                    $colNik = $this->findColumnIndex($header, ['302. nik anggota', 'nik anggota keluarga', 'nik']);
                    $colKkNumber = $this->findColumnIndex($header, ['103. nomor kartu keluarga', 'nomor kartu keluarga', 'nomor kk']);
                    $colKkOrder = $this->findColumnIndex($header, ['103.a. nomor urut', 'nomor urut anggota']);
                    $colName = $this->findColumnIndex($header, ['301. nama anggota', 'nama anggota keluarga', 'nama']);
                    $colStatus = $this->findColumnIndex($header, ['303. keberadaan', 'keberadaan anggota keluarga']);
                    $colAddress = $this->findColumnIndex($header, ['304. alamat domisili', 'alamat domisili']);
                    
                    $colGender = $this->findColumnIndex($header, ['306. jenis kelamin', 'jenis kelamin']);
                    $colDob = $this->findColumnIndex($header, ['307. tanggal lahir', 'tanggal lahir']);
                    $colMarital = $this->findColumnIndex($header, ['308. status perkawinan', 'status perkawinan']);
                    $colRelation = $this->findColumnIndex($header, ['309. hubungan dengan', 'hubungan dengan kepala keluarga']);
                    $colSchool = $this->findColumnIndex($header, ['310. partisipasi sekolah', 'partisipasi sekolah']);
                    $colEduLevel = $this->findColumnIndex($header, ['311. ijazah/sttb', 'ijazah tertinggi']);
                    $colBpjs = $this->findColumnIndex($header, ['kepesertaan jkn', 'bpjs']);
                    $colPip = $this->findColumnIndex($header, ['program indonesia pintar', 'bantuan pip']);
                    
                    $colHasIncome = $this->findColumnIndex($header, ['312. apakah anggota', 'memiliki pendapatan']);
                    $colIncSalary = $this->findColumnIndex($header, ['312.a. gaji']);
                    $colIncAllow = $this->findColumnIndex($header, ['312.b. tunjangan']);
                    $colIncFood = $this->findColumnIndex($header, ['312.c. uang makan']);
                    $colIncHonor = $this->findColumnIndex($header, ['312.d. honor']);
                    $colIncOver = $this->findColumnIndex($header, ['312.e. lembur']);
                    $colIncOther = $this->findColumnIndex($header, ['312.f. lainnya']);
                    $colIncBus = $this->findColumnIndex($header, ['312.g. pendapatan dari usaha']);
                    $colIncPass = $this->findColumnIndex($header, ['312.h. penerimaan pendapatan lain']);
                    
                    $colJob = $this->findColumnIndex($header, ['313. profesi pekerjaan', 'pekerjaan utama']);
                    $colJobStatus = $this->findColumnIndex($header, ['314. status kedudukan', 'kedudukan dalam pekerjaan']);
                    
                    // Disability Columns
                    $colDisPhys = $this->findColumnIndex($header, ['[disabilitas fisik]']);
                    $colDisMent = $this->findColumnIndex($header, ['[disabilitas mental]']);
                    $colDisIntel = $this->findColumnIndex($header, ['[disabilitas intelektual]']);
                    $colDisBlind = $this->findColumnIndex($header, ['[disabilitas sensorik netra]']);
                    $colDisDeaf = $this->findColumnIndex($header, ['[disabilitas sensorik rungu]']);
                    $colDisSpeech = $this->findColumnIndex($header, ['[disabilitas sensorik wicara]']);
                    
                    // Illness Columns
                    $colIllHyper = $this->findColumnIndex($header, ['[hipertensi']);
                    $colIllRheu = $this->findColumnIndex($header, ['[rematik]']);
                    $colIllAsthma = $this->findColumnIndex($header, ['[asma]']);
                    $colIllHeart = $this->findColumnIndex($header, ['[masalah jantung]']);
                    $colIllDiab = $this->findColumnIndex($header, ['[diabetes']);
                    $colIllTbc = $this->findColumnIndex($header, ['[tuberkulosis']);
                    $colIllStroke = $this->findColumnIndex($header, ['[stroke]']);
                    $colIllCancer = $this->findColumnIndex($header, ['[kanker']);
                    $colIllKidney = $this->findColumnIndex($header, ['[gagal ginjal]']);
                    $colIllHemo = $this->findColumnIndex($header, ['[hemofilia]']);
                    $colIllHiv = $this->findColumnIndex($header, ['[hiv/aids]']);
                    $colIllChol = $this->findColumnIndex($header, ['[kolestrol]', '[kolesterol]']);
                    $colIllLiver = $this->findColumnIndex($header, ['[sirosis']);
                    $colIllThal = $this->findColumnIndex($header, ['[talasemia]']);
                    $colIllLeuk = $this->findColumnIndex($header, ['[leukemia]']);
                    $colIllAlz = $this->findColumnIndex($header, ['[alzheimer]']);
                    $colIllOther = $this->findColumnIndex($header, ['[lainnya]']);
                    
                    $colWallet = $this->findColumnIndex($header, ['317. apakah memiliki rekening']);

                    if ($colNik === false) {
                        Notification::make()->title('Format file salah.')->body('NIK Anggota Keluarga (Kolom 302) wajib ditemukan.')->danger()->send();
                        return;
                    }

                    $rowCount = 0;
                    foreach ($rows as $row) {
                        if (count($row) <= $colNik) continue;

                        $nik = trim($row[$colNik]);
                        if (empty($nik) || strlen($nik) < 10) continue;

                        $kkNumber = $colKkNumber !== false ? trim($row[$colKkNumber]) : null;
                        
                        // Look up family and dusun
                        $familyId = null;
                        $dusunId = $selectedDusunId;
                        $rt = null;
                        $rw = null;
                        $address = $colAddress !== false ? trim($row[$colAddress]) : '';

                        if ($kkNumber) {
                            $family = Family::where('kk_number', $kkNumber)->first();
                            if ($family) {
                                $familyId = $family->id;
                                if (!$dusunId) {
                                    $dusunId = $family->dusun_id;
                                }
                                $rt = $family->rt;
                                $rw = $family->rw;
                                if (empty($address)) {
                                    $address = $family->address;
                                }
                            }
                        }

                        // Fallback matching for Dusun from address
                        if (!$dusunId && !empty($address)) {
                            $dusunList = Dusun::all();
                            foreach ($dusunList as $dus) {
                                if (stripos($address, $dus->name) !== false) {
                                    $dusunId = $dus->id;
                                    break;
                                }
                            }
                        }

                        // Parse Date of Birth correctly
                        $dob = null;
                        if ($colDob !== false && !empty($row[$colDob])) {
                            $dobStr = trim($row[$colDob]);
                            // Try standard Y-m-d format first, then d/m/Y
                            $time = strtotime(str_replace('/', '-', $dobStr));
                            if ($time) {
                                $dob = date('Y-m-d', $time);
                            }
                        }

                        $dataToSave = [
                            'kk_number' => $kkNumber,
                            'kk_order' => $colKkOrder !== false ? intval(trim($row[$colKkOrder])) : null,
                            'name' => $colName !== false ? trim($row[$colName]) : '',
                            'family_id' => $familyId,
                            'dusun_id' => $dusunId,
                            'rt' => $rt,
                            'rw' => $rw,
                            'address' => $address,
                            'gender' => $colGender !== false && !empty(trim($row[$colGender])) ? (strpos(strtolower(trim($row[$colGender])), 'perempuan') !== false || strtolower(trim($row[$colGender])) === 'p' ? 'Perempuan' : 'Laki-laki') : null,
                            'date_of_birth' => $dob,
                            'marital_status' => $colMarital !== false ? $this->parseMaritalStatus($row[$colMarital]) : null,
                            'family_relation' => $colRelation !== false ? $this->parseFamilyRelation($row[$colRelation]) : null,
                            'school_participation' => $colSchool !== false ? trim($row[$colSchool]) : null,
                            'education_level' => $colEduLevel !== false ? $this->parseEducationLevel($row[$colEduLevel]) : null,
                            'education' => $colEduLevel !== false ? $this->parseEducationLevel($row[$colEduLevel]) : null, // legacy
                            'bpjs_status' => $colBpjs !== false ? trim($row[$colBpjs]) : null,
                            'pip_status' => $colPip !== false ? trim($row[$colPip]) : null,
                            'has_income' => $colHasIncome !== false ? trim($row[$colHasIncome]) : null,
                            'job' => $colJob !== false ? $this->parseJob($row[$colJob]) : null,
                            'job_status' => $colJobStatus !== false ? trim($row[$colJobStatus]) : null,
                            
                            // Income
                            'income_salary' => $colIncSalary !== false ? $this->cleanNumeric(trim($row[$colIncSalary])) : 0,
                            'income_allowance' => $colIncAllow !== false ? $this->cleanNumeric(trim($row[$colIncAllow])) : 0,
                            'income_food' => $colIncFood !== false ? $this->cleanNumeric(trim($row[$colIncFood])) : 0,
                            'income_honor' => $colIncHonor !== false ? $this->cleanNumeric(trim($row[$colIncHonor])) : 0,
                            'income_overtime' => $colIncOver !== false ? $this->cleanNumeric(trim($row[$colIncOver])) : 0,
                            'income_other' => $colIncOther !== false ? $this->cleanNumeric(trim($row[$colIncOther])) : 0,
                            'income_business' => $colIncBus !== false ? $this->cleanNumeric(trim($row[$colIncBus])) : 0,
                            'income_passive' => $colIncPass !== false ? $this->cleanNumeric(trim($row[$colIncPass])) : 0,
                            
                            // Disabilities
                            'disability_physical' => $colDisPhys !== false ? $this->parseYesNo($row[$colDisPhys]) : null,
                            'disability_mental' => $colDisMent !== false ? $this->parseYesNo($row[$colDisMent]) : null,
                            'disability_intellectual' => $colDisIntel !== false ? $this->parseYesNo($row[$colDisIntel]) : null,
                            'disability_blind' => $colDisBlind !== false ? $this->parseYesNo($row[$colDisBlind]) : null,
                            'disability_deaf' => $colDisDeaf !== false ? $this->parseYesNo($row[$colDisDeaf]) : null,
                            'disability_speech' => $colDisSpeech !== false ? $this->parseYesNo($row[$colDisSpeech]) : null,
                            
                            // Illnesses
                            'illness_hypertension' => $colIllHyper !== false ? $this->parseYesNo($row[$colIllHyper]) : null,
                            'illness_rheumatic' => $colIllRheu !== false ? $this->parseYesNo($row[$colIllRheu]) : null,
                            'illness_asthma' => $colIllAsthma !== false ? $this->parseYesNo($row[$colIllAsthma]) : null,
                            'illness_heart' => $colIllHeart !== false ? $this->parseYesNo($row[$colIllHeart]) : null,
                            'illness_diabetes' => $colIllDiab !== false ? $this->parseYesNo($row[$colIllDiab]) : null,
                            'illness_tbc' => $colIllTbc !== false ? $this->parseYesNo($row[$colIllTbc]) : null,
                            'illness_stroke' => $colIllStroke !== false ? $this->parseYesNo($row[$colIllStroke]) : null,
                            'illness_cancer' => $colIllCancer !== false ? $this->parseYesNo($row[$colIllCancer]) : null,
                            'illness_kidney' => $colIllKidney !== false ? $this->parseYesNo($row[$colIllKidney]) : null,
                            'illness_hemophilia' => $colIllHemo !== false ? $this->parseYesNo($row[$colIllHemo]) : null,
                            'illness_hiv' => $colIllHiv !== false ? $this->parseYesNo($row[$colIllHiv]) : null,
                            'illness_cholesterol' => $colIllChol !== false ? $this->parseYesNo($row[$colIllChol]) : null,
                            'illness_liver' => $colIllLiver !== false ? $this->parseYesNo($row[$colIllLiver]) : null,
                            'illness_thalassemia' => $colIllThal !== false ? $this->parseYesNo($row[$colIllThal]) : null,
                            'illness_leukemia' => $colIllLeuk !== false ? $this->parseYesNo($row[$colIllLeuk]) : null,
                            'illness_alzheimer' => $colIllAlz !== false ? $this->parseYesNo($row[$colIllAlz]) : null,
                            'illness_other' => $colIllOther !== false ? $this->parseYesNo($row[$colIllOther]) : null,
                            
                            'has_digital_wallet' => $colWallet !== false ? $this->parseYesNo($row[$colWallet]) : null,
                            'status' => 'Aktif',
                            'citizenship_status' => $colStatus !== false ? trim($row[$colStatus]) : 'Tinggal di rumah/tempat tinggal ini',
                        ];

                        Citizen::updateOrCreate(
                            ['nik' => $nik],
                            $dataToSave
                        );
                        $rowCount++;
                    }

                    @unlink($filePath);

                    Notification::make()
                        ->title('Import Sukses!')
                        ->body("Berhasil mengimpor/memperbarui $rowCount data warga.")
                        ->success()
                        ->send();
                }),
        ];
    }

    private function parseYesNo(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;
        
        if (in_array($clean, ['ya', 'yes', 'true', '1']) || strpos($clean, 'ya') === 0) {
            return 'Ya';
        }
        return 'Tidak';
    }

    private function parseMaritalStatus(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (strpos($clean, 'belum') !== false) {
            return 'Belum Kawin';
        } elseif (strpos($clean, 'cerai hidup') !== false || strpos($clean, 'hidup') !== false) {
            return 'Cerai Hidup';
        } elseif (strpos($clean, 'cerai mati') !== false || strpos($clean, 'mati') !== false || strpos($clean, 'janda') !== false || strpos($clean, 'duda') !== false) {
            return 'Cerai Mati';
        } elseif (strpos($clean, 'kawin') !== false || strpos($clean, 'menikah') !== false || strpos($clean, 'nikah') !== false) {
            return 'Kawin';
        }
        return 'Belum Kawin';
    }

    private function parseFamilyRelation(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (strpos($clean, 'kepala') !== false || strpos($clean, 'kk') !== false) {
            return 'Kepala Keluarga';
        } elseif (strpos($clean, 'istri') !== false || strpos($clean, 'suami') !== false) {
            return 'Istri';
        } elseif (strpos($clean, 'anak') !== false) {
            return 'Anak';
        } elseif (strpos($clean, 'cucu') !== false) {
            return 'Cucu';
        } elseif (strpos($clean, 'tua') !== false || strpos($clean, 'bapak') !== false || strpos($clean, 'ibu') !== false) {
            return 'Orang Tua';
        } elseif (strpos($clean, 'mertua') !== false) {
            return 'Mertua';
        }
        return 'Lainnya';
    }

    private function parseEducationLevel(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (strpos($clean, 'tidak') !== false || strpos($clean, 'belum') !== false) {
            return 'Tidak/belum pernah sekolah';
        } elseif (strpos($clean, 'sd') !== false || strpos($clean, 'dasar') !== false || strpos($clean, 'primary') !== false) {
            return 'SD';
        } elseif (strpos($clean, 'smp') !== false || strpos($clean, 'tsanawiyah') !== false || strpos($clean, 'menengah pertama') !== false) {
            return 'SMP';
        } elseif (strpos($clean, 'sma') !== false || strpos($clean, 'smk') !== false || strpos($clean, 'aliyah') !== false || strpos($clean, 'menengah atas') !== false) {
            return 'SMA';
        } elseif (strpos($clean, 'diploma') !== false || strpos($clean, 'd1') !== false || strpos($clean, 'd2') !== false || strpos($clean, 'd3') !== false || strpos($clean, 'd4') !== false || strpos($clean, 'akademik') !== false) {
            return 'Diploma';
        } elseif (strpos($clean, 'sarjana') !== false || strpos($clean, 's1') !== false || strpos($clean, 's2') !== false || strpos($clean, 's3') !== false || strpos($clean, 'master') !== false || strpos($clean, 'doktor') !== false) {
            return 'Sarjana';
        }
        return 'Tidak/belum pernah sekolah';
    }

    private function parseJob(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (strpos($clean, 'tani') !== false || strpos($clean, 'kebun') !== false || strpos($clean, 'sawah') !== false) {
            return 'Petani';
        } elseif (strpos($clean, 'wiraswasta') !== false || strpos($clean, 'usaha') !== false || strpos($clean, 'dagang') !== false || strpos($clean, 'toko') !== false || strpos($clean, 'bisnis') !== false) {
            return 'Wiraswasta';
        } elseif (strpos($clean, 'pns') !== false || strpos($clean, 'sipil') !== false || strpos($clean, 'negeri') !== false || strpos($clean, 'asn') !== false || strpos($clean, 'pejabat') !== false) {
            return 'PNS';
        } elseif (strpos($clean, 'buruh') !== false || strpos($clean, 'pekerja harian') !== false || strpos($clean, 'tukang') !== false || strpos($clean, 'kuli') !== false) {
            return 'Buruh';
        } elseif (strpos($clean, 'tidak bekerja') !== false || strpos($clean, 'menganggur') !== false || strpos($clean, 'sekolah') !== false || strpos($clean, 'ibu rumah tangga') !== false || strpos($clean, 'irt') !== false) {
            return 'Tidak Bekerja';
        }
        return 'Lainnya';
    }

    private function findColumnIndex(array $header, array $needles): int|bool
    {
        foreach ($header as $idx => $colName) {
            foreach ($needles as $needle) {
                if (strpos($colName, $needle) !== false) {
                    return $idx;
                }
            }
        }
        return false;
    }

    private function cleanNumeric(string $val): int
    {
        $clean = preg_replace('/[^0-9]/', '', $val);
        return empty($clean) ? 0 : intval($clean);
    }
}

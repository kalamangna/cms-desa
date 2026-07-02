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
                        ->label('Pilih Dusun (Opsional)')
                        ->options(Dusun::pluck('name', 'id'))
                        ->placeholder('Semua Dusun (Deteksi Otomatis dari Alamat)')
                        ->searchable()
                        ->preload(),
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
                    $hasFamilyIndicator = $this->findColumnIndex($header, ['101. nama kepala', 'nama kepala keluarga']) !== false;
                    $hasCitizenIndicator = $this->findColumnIndex($header, ['302. nik anggota', 'nik anggota keluarga', 'nik']) !== false;
                    
                    if ($hasFamilyIndicator && !$hasCitizenIndicator) {
                        Notification::make()
                            ->title('Gagal: File Tertukar!')
                            ->body('Anda mengunggah file data KELUARGA di form Penduduk. Harap unggah file data INDIVIDU/PENDUDUK.')
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
                            'gender' => $colGender !== false ? trim($row[$colGender]) : null,
                            'date_of_birth' => $dob,
                            'marital_status' => $colMarital !== false ? trim($row[$colMarital]) : null,
                            'family_relation' => $colRelation !== false ? trim($row[$colRelation]) : null,
                            'school_participation' => $colSchool !== false ? trim($row[$colSchool]) : null,
                            'education_level' => $colEduLevel !== false ? trim($row[$colEduLevel]) : null,
                            'education' => $colEduLevel !== false ? trim($row[$colEduLevel]) : null, // legacy
                            'bpjs_status' => $colBpjs !== false ? trim($row[$colBpjs]) : null,
                            'pip_status' => $colPip !== false ? trim($row[$colPip]) : null,
                            'has_income' => $colHasIncome !== false ? trim($row[$colHasIncome]) : null,
                            'job' => $colJob !== false ? trim($row[$colJob]) : null,
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
                            'disability_physical' => $colDisPhys !== false ? trim($row[$colDisPhys]) : null,
                            'disability_mental' => $colDisMent !== false ? trim($row[$colDisMent]) : null,
                            'disability_intellectual' => $colDisIntel !== false ? trim($row[$colDisIntel]) : null,
                            'disability_blind' => $colDisBlind !== false ? trim($row[$colDisBlind]) : null,
                            'disability_deaf' => $colDisDeaf !== false ? trim($row[$colDisDeaf]) : null,
                            'disability_speech' => $colDisSpeech !== false ? trim($row[$colDisSpeech]) : null,
                            
                            // Illnesses
                            'illness_hypertension' => $colIllHyper !== false ? trim($row[$colIllHyper]) : null,
                            'illness_rheumatic' => $colIllRheu !== false ? trim($row[$colIllRheu]) : null,
                            'illness_asthma' => $colIllAsthma !== false ? trim($row[$colIllAsthma]) : null,
                            'illness_heart' => $colIllHeart !== false ? trim($row[$colIllHeart]) : null,
                            'illness_diabetes' => $colIllDiab !== false ? trim($row[$colIllDiab]) : null,
                            'illness_tbc' => $colIllTbc !== false ? trim($row[$colIllTbc]) : null,
                            'illness_stroke' => $colIllStroke !== false ? trim($row[$colIllStroke]) : null,
                            'illness_cancer' => $colIllCancer !== false ? trim($row[$colIllCancer]) : null,
                            'illness_kidney' => $colIllKidney !== false ? trim($row[$colIllKidney]) : null,
                            'illness_hemophilia' => $colIllHemo !== false ? trim($row[$colIllHemo]) : null,
                            'illness_hiv' => $colIllHiv !== false ? trim($row[$colIllHiv]) : null,
                            'illness_cholesterol' => $colIllChol !== false ? trim($row[$colIllChol]) : null,
                            'illness_liver' => $colIllLiver !== false ? trim($row[$colIllLiver]) : null,
                            'illness_thalassemia' => $colIllThal !== false ? trim($row[$colIllThal]) : null,
                            'illness_leukemia' => $colIllLeuk !== false ? trim($row[$colIllLeuk]) : null,
                            'illness_alzheimer' => $colIllAlz !== false ? trim($row[$colIllAlz]) : null,
                            'illness_other' => $colIllOther !== false ? trim($row[$colIllOther]) : null,
                            
                            'has_digital_wallet' => $colWallet !== false ? trim($row[$colWallet]) : null,
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

<?php

namespace App\Filament\Resources\Families\Pages;

use App\Filament\Resources\Families\FamilyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Models\Family;
use App\Models\Dusun;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ListFamilies extends ListRecords
{
    protected static string $resource = FamilyResource::class;

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
                        ->content('Unggah respon kuesioner Google Form (Keluarga) secara langsung dalam format Excel (.xlsx / .xls) atau CSV (.csv).'),
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

                    // Validate if it is a citizen sheet uploaded to family form
                    $hasCitizenIndicator = $this->findColumnIndex($header, ['302. nik anggota', 'nik anggota keluarga']) !== false;
                    
                    if ($hasCitizenIndicator) {
                        Notification::make()
                            ->title('Gagal: File Tertukar!')
                            ->body('Anda mengunggah file data PENDUDUK/INDIVIDU di form Keluarga. Harap unggah file data KELUARGA.')
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }

                    // Map columns dynamically by checking partial string matches
                    $colKkNumber = $this->findColumnIndex($header, ['103. nomor kk', 'nomor kk dari', 'kartu keluarga']);
                    $colHeadName = $this->findColumnIndex($header, ['101. nama kepala', 'nama kepala keluarga']);
                    $colHeadNik = $this->findColumnIndex($header, ['102. nik kepala', 'nik kepala keluarga']);
                    $colAddress = $this->findColumnIndex($header, ['104. alamat lengkap', 'alamat lengkap']);
                    $colMatchesKk = $this->findColumnIndex($header, ['105. apakah alamat', 'alamat tersebut sesuai']);
                    $colBansos = $this->findColumnIndex($header, ['jenis bantuan', 'bantuan apa yang diterima']);
                    
                    $colBuildingType = $this->findColumnIndex($header, ['201. jenis bangunan']);
                    $colOwnership = $this->findColumnIndex($header, ['202.a. status kepemilikan']);
                    $colProof = $this->findColumnIndex($header, ['202.b. bukti kepemilikan']);
                    $colFloorArea = $this->findColumnIndex($header, ['204. luas lantai']);
                    $colFloorMat = $this->findColumnIndex($header, ['205. bahan bangunan utama lantai']);
                    $colWallMat = $this->findColumnIndex($header, ['206. bahan bangunan utama dinding']);
                    $colRoofMat = $this->findColumnIndex($header, ['207. bahan bangunan utama atap']);
                    $colFloorCond = $this->findColumnIndex($header, ['kondisi lantai, dinding', '[lantai]']);
                    $colWallCond = $this->findColumnIndex($header, ['kondisi lantai, dinding', '[dinding]']);
                    $colRoofCond = $this->findColumnIndex($header, ['kondisi lantai, dinding', '[atap]']);
                    
                    $colToilet = $this->findColumnIndex($header, ['209. apakah memiliki fasilitas']);
                    $colCloset = $this->findColumnIndex($header, ['210. apa jenis kloset']);
                    $colFeces = $this->findColumnIndex($header, ['211. di manakah tempat pembuangan']);
                    $colWater = $this->findColumnIndex(['212. sumber air utama', '212. apa sumber air']); // check custom
                    // If not found, manual check from header array:
                    if ($colWater === false) {
                        foreach ($header as $idx => $h) {
                            if (strpos($h, '212.') !== false || strpos($h, 'sumber air utama') !== false) {
                                $colWater = $idx;
                                        break;
                            }
                        }
                    }
                    
                    $colLight = $this->findColumnIndex($header, ['213.a. sumber penerangan']);
                    $colPlnId = $this->findColumnIndex($header, ['213.c. id pelanggan']);
                    
                    $colPlnCost = $this->findColumnIndex($header, ['214. nilai pengeluaran listrik']);
                    $colNetCost = $this->findColumnIndex($header, ['215. nilai pengeluaran pulsa']);
                    
                    $colPhotoFront = $this->findColumnIndex($header, ['216.a. foto tampak depan']);
                    $colPhotoLiving = $this->findColumnIndex($header, ['216.b. foto ruang tamu']);
                    $colPhotoBath = $this->findColumnIndex($header, ['216.c. foto kamar mandi']);
                    $colPhotoKk = $this->findColumnIndex($header, ['foto kartu keluarga']);
                    
                    $colGas3 = $this->findColumnIndex($header, ['217.a. jumlah tabung gas 3']);
                    $colGas5 = $this->findColumnIndex($header, ['217.b. jumlah tabung gas 5']);
                    $colFridge = $this->findColumnIndex($header, ['217.c. jumlah lemari']);
                    $colAc = $this->findColumnIndex($header, ['217.d. jumlah ac']);
                    $colGold = $this->findColumnIndex($header, ['217.e. jumlah emas']);
                    $colComp = $this->findColumnIndex($header, ['217.f. jumlah komputer']);
                    $colMotor = $this->findColumnIndex($header, ['217.g. jumlah sepeda motor']);
                    $colMotorVal = $this->findColumnIndex($header, ['217.h. total nilai aset sepeda']);
                    $colCar = $this->findColumnIndex($header, ['217.i. jumlah mobil']);
                    $colCarVal = $this->findColumnIndex($header, ['217.j. total nilai aset mobil']);
                    $colLand = $this->findColumnIndex($header, ['217.k. jumlah tanah']);
                    $colLandVal = $this->findColumnIndex($header, ['217.l. total nilai harga jual tanah']);
                    $colBld = $this->findColumnIndex($header, ['217.m. jumlah rumah']);
                    $colBldVal = $this->findColumnIndex($header, ['217.n. total nilai harga jual rumah']);
                    $colNotes = $this->findColumnIndex($header, ['catatan']);

                    if ($colKkNumber === false) {
                        Notification::make()->title('Format file salah.')->body('Nomor KK (Kolom 103) wajib ditemukan.')->danger()->send();
                        return;
                    }

                    $rowCount = 0;
                    foreach ($rows as $row) {
                        if (count($row) <= $colKkNumber) continue;

                        $kkNumber = trim($row[$colKkNumber]);
                        if (empty($kkNumber) || strtolower($kkNumber) === 'none' || strlen($kkNumber) < 5) continue;

                        // Parse address details
                        $address = $colAddress !== false ? trim($row[$colAddress]) : '';
                        $rt = null; $rw = null; $dusunId = $selectedDusunId;

                        // Smart parse RT/RW/Dusun from address string
                        // e.g. "RT 005 RW 003 Dusun Data" or "005/003/Dusun Data"
                        if (!empty($address)) {
                            if (preg_match('/rt\s*(\d+)/i', $address, $matches)) {
                                $rt = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
                            }
                            if (preg_match('/rw\s*(\d+)/i', $address, $matches)) {
                                $rw = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
                            }
                            if (!$rt && !$rw && preg_match('/(\d+)\/(\d+)/', $address, $matches)) {
                                $rt = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
                                $rw = str_pad($matches[2], 3, '0', STR_PAD_LEFT);
                            }

                            // Match Dusun from address text if not selected
                            if (!$dusunId) {
                                $dusunList = Dusun::all();
                                foreach ($dusunList as $dus) {
                                    if (stripos($address, $dus->name) !== false) {
                                        $dusunId = $dus->id;
                                        break;
                                    }
                                }
                            }
                        }

                        $dataToSave = [
                            'head_name' => $colHeadName !== false ? trim($row[$colHeadName]) : null,
                            'head_nik' => $colHeadNik !== false ? trim($row[$colHeadNik]) : null,
                            'address' => $address,
                            'dusun_id' => $dusunId,
                            'rt' => $rt,
                            'rw' => $rw,
                            'address_matches_kk' => $colMatchesKk !== false ? trim($row[$colMatchesKk]) : null,
                            'assistance_type' => $colBansos !== false ? trim($row[$colBansos]) : null,
                            'building_type' => $colBuildingType !== false ? trim($row[$colBuildingType]) : null,
                            'ownership_status' => $colOwnership !== false ? trim($row[$colOwnership]) : null,
                            'ownership_proof' => $colProof !== false ? trim($row[$colProof]) : null,
                            'floor_area' => $colFloorArea !== false ? floatval(trim($row[$colFloorArea])) : null,
                            'floor_material' => $colFloorMat !== false ? trim($row[$colFloorMat]) : null,
                            'wall_material' => $colWallMat !== false ? trim($row[$colWallMat]) : null,
                            'roof_material' => $colRoofMat !== false ? trim($row[$colRoofMat]) : null,
                            'floor_condition' => $colFloorCond !== false ? trim($row[$colFloorCond]) : null,
                            'wall_condition' => $colWallCond !== false ? trim($row[$colWallCond]) : null,
                            'roof_condition' => $colRoofCond !== false ? trim($row[$colRoofCond]) : null,
                            'toilet_facility' => $colToilet !== false ? trim($row[$colToilet]) : null,
                            'closet_type' => $colCloset !== false ? trim($row[$colCloset]) : null,
                            'feces_disposal' => $colFeces !== false ? trim($row[$colFeces]) : null,
                            'water_source' => $colWater !== false ? trim($row[$colWater]) : null,
                            'lighting_source' => $colLight !== false ? trim($row[$colLight]) : null,
                            'electricity_id' => $colPlnId !== false ? trim($row[$colPlnId]) : null,
                            'electricity_cost' => $colPlnCost !== false ? $this->cleanNumeric(trim($row[$colPlnCost])) : 0,
                            'internet_cost' => $colNetCost !== false ? $this->cleanNumeric(trim($row[$colNetCost])) : 0,
                            'photo_front' => $colPhotoFront !== false ? trim($row[$colPhotoFront]) : null,
                            'photo_living_room' => $colPhotoLiving !== false ? trim($row[$colPhotoLiving]) : null,
                            'photo_bathroom' => $colPhotoBath !== false ? trim($row[$colPhotoBath]) : null,
                            'photo_kk' => $colPhotoKk !== false ? trim($row[$colPhotoKk]) : null,
                            'gas_3kg_count' => $colGas3 !== false ? intval(trim($row[$colGas3])) : 0,
                            'gas_5kg_count' => $colGas5 !== false ? intval(trim($row[$colGas5])) : 0,
                            'refrigerator_count' => $colFridge !== false ? intval(trim($row[$colFridge])) : 0,
                            'ac_count' => $colAc !== false ? intval(trim($row[$colAc])) : 0,
                            'jewelry_count' => $colGold !== false ? intval(trim($row[$colGold])) : 0,
                            'computer_count' => $colComp !== false ? intval(trim($row[$colComp])) : 0,
                            'motorcycle_count' => $colMotor !== false ? intval(trim($row[$colMotor])) : 0,
                            'motorcycle_value' => $colMotorVal !== false ? $this->cleanNumeric(trim($row[$colMotorVal])) : 0,
                            'car_count' => $colCar !== false ? intval(trim($row[$colCar])) : 0,
                            'car_value' => $colCarVal !== false ? $this->cleanNumeric(trim($row[$colCarVal])) : 0,
                            'other_land_count' => $colLand !== false ? intval(trim($row[$colLand])) : 0,
                            'other_land_value' => $colLandVal !== false ? $this->cleanNumeric(trim($row[$colLandVal])) : 0,
                            'other_building_count' => $colBld !== false ? intval(trim($row[$colBld])) : 0,
                            'other_building_value' => $colBldVal !== false ? $this->cleanNumeric(trim($row[$colBldVal])) : 0,
                            'notes' => $colNotes !== false ? trim($row[$colNotes]) : null,
                        ];

                        Family::updateOrCreate(
                            ['kk_number' => $kkNumber],
                            $dataToSave
                        );
                        $rowCount++;
                    }

                    @unlink($filePath);

                    Notification::make()
                        ->title('Import Sukses!')
                        ->body("Berhasil mengimpor/memperbarui $rowCount profil keluarga.")
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

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
            Action::make('importExcel')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    Placeholder::make('info')
                        ->label('Petunjuk Penggunaan')
                        ->content('Unggah respon kuesioner Google Form (Keluarga) secara langsung dalam format Excel (.xlsx / .xls).'),
                    Select::make('dusun_id')
                        ->label('Pilih Dusun')
                        ->options(Dusun::pluck('name', 'id'))
                        ->placeholder('Pilih Dusun...')
                        ->searchable()
                        ->preload()
                        ->required(),
                    FileUpload::make('excel_file')
                        ->label('File Excel')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->required()
                        ->directory('temp'),
                ])
                ->action(function (array $data) {
                    // BUG-C6 Fix: Proteksi memory untuk file Excel besar
                    ini_set('memory_limit', '256M');
                    set_time_limit(120);

                    $disk = config('filament.default_filesystem_disk', 'public');
                    $filePath = \Illuminate\Support\Facades\Storage::disk($disk)->path($data['excel_file']);
                    
                    if (!file_exists($filePath)) {
                        // Fallback check in storage_path('app/public/') or storage_path('app/private/') or public_path('storage/')
                        $possiblePaths = [
                            storage_path('app/public/' . $data['excel_file']),
                            storage_path('app/' . $data['excel_file']),
                            public_path('storage/' . $data['excel_file']),
                        ];
                        foreach ($possiblePaths as $path) {
                            if (file_exists($path)) {
                                $filePath = $path;
                                break;
                            }
                        }
                    }

                    $selectedDusunId = $data['dusun_id'] ?? null;
                    
                    if (!file_exists($filePath)) {
                        Notification::make()->title('File tidak ditemukan. Path: ' . $filePath)->danger()->send();
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
                    $isCitizenSheet = $this->findColumnIndex($header, ['302. nik anggota', '306. jenis kelamin']) !== false;
                    $isFamilySheet = $this->findColumnIndex($header, ['101. nama kepala keluarga', '201. jenis bangunan']) !== false;
                    
                    if ($isCitizenSheet || !$isFamilySheet) {
                        Notification::make()
                            ->title('Gagal: File Salah / Tertukar!')
                            ->body('Anda mengunggah file data PENDUDUK/INDIVIDU (atau file dengan format salah) di form Keluarga. Harap unggah file data KELUARGA.')
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
                    // Fix #3: Gunakan needle spesifik agar masing-masing kolom 208 terdeteksi dengan tepat
                    $colFloorCond = $this->findColumnIndex($header, ['[lantai]']);
                    $colWallCond = $this->findColumnIndex($header, ['[dinding]']);
                    $colRoofCond = $this->findColumnIndex($header, ['[atap]']);
                    
                    // Fix #5: Deteksi kolom sewa/kepemilikan biaya (203.a/b/c)
                    $colRentalEstimate = $this->findColumnIndex($header, ['203.a. perkiraan sewa']);
                    $colRentalFree = $this->findColumnIndex($header, ['203.b. perkiraan sewa']);
                    $colRentalContract = $this->findColumnIndex($header, ['203.c. nilai kontrak']);
                    
                    $colToilet = $this->findColumnIndex($header, ['209. apakah memiliki fasilitas']);
                    $colCloset = $this->findColumnIndex($header, ['210. apa jenis kloset']);
                    $colFeces = $this->findColumnIndex($header, ['211. di manakah tempat pembuangan']);
                    $colWater = $this->findColumnIndex($header, ['212. sumber air utama', '212. apa sumber air']); // check custom
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
                    $colMemberCount = $this->findColumnIndex($header, ['106.a. berapa jumlah keluarga', 'jumlah keluarga yang tinggal dalam 1 rumah']);
                    $colPlnPower1 = $this->findColumnIndex($header, ['[meteran 1]']);
                    $colPlnPower2 = $this->findColumnIndex($header, ['[meteran 2]']);
                    $colPlnPower3 = $this->findColumnIndex($header, ['[meteran 3]']);
                    
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
                    $colCow = $this->findColumnIndex($header, ['217.o. jumlah sapi']);
                    $colGoat = $this->findColumnIndex($header, ['217.p. jumlah kambing']);
                    $colBuffalo = $this->findColumnIndex($header, ['217.q. jumlah kerbau']);
                    $colNotes = $this->findColumnIndex($header, ['catatan']);

                    if ($colKkNumber === false) {
                        Notification::make()->title('Format file salah.')->body('Nomor KK (Kolom 103) wajib ditemukan.')->danger()->send();
                        return;
                    }

                    \Illuminate\Support\Facades\DB::beginTransaction();

                    try {
                        $rowCount = 0;
                        foreach ($rows as $index => $row) {
                            if (count($row) <= $colKkNumber) continue;

                            $kkNumber = trim($row[$colKkNumber]);
                            if (empty($kkNumber) || strtolower($kkNumber) === 'none' || strlen($kkNumber) < 5) continue;

                            // Parse address details
                            $address = $colAddress !== false ? trim($row[$colAddress]) : '';
                            $rt = null; $rw = null; $dusunId = $selectedDusunId;

                            // Smart parse RT/RW/Dusun from address string
                            // e.g. "RT 005 RW 003 Dusun Data" or "005/003/Dusun Data"
                            if (!empty($address)) {
                                if (preg_match('/rt[\.\s]*(\d+)/i', $address, $matches)) {
                                    $rt = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
                                }
                                if (preg_match('/rw[\.\s]*(\d+)/i', $address, $matches)) {
                                    $rw = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
                                }
                                if (!$rt && !$rw && preg_match('/(\d+)\/(\d+)/', $address, $matches)) {
                                    $rt = str_pad($matches[1], 3, '0', STR_PAD_LEFT);
                                    $rw = str_pad($matches[2], 3, '0', STR_PAD_LEFT);
                                }
                            }

                            $power1 = $colPlnPower1 !== false && isset($row[$colPlnPower1]) && trim($row[$colPlnPower1]) !== '' ? trim($row[$colPlnPower1]) : null;
                            $power2 = $colPlnPower2 !== false && isset($row[$colPlnPower2]) && trim($row[$colPlnPower2]) !== '' ? trim($row[$colPlnPower2]) : null;
                            $power3 = $colPlnPower3 !== false && isset($row[$colPlnPower3]) && trim($row[$colPlnPower3]) !== '' ? trim($row[$colPlnPower3]) : null;
                            $powers = array_filter([$power1, $power2, $power3]);
                            $electricityPower = count($powers) > 0 ? implode(', ', $powers) : null;

                            $dataToSave = [
                                'head_name' => $colHeadName !== false ? $this->cleanName($row[$colHeadName]) : null,
                                'head_nik' => $colHeadNik !== false ? trim($row[$colHeadNik]) : null,
                                'address' => $address,
                                'dusun_id' => $dusunId,
                                'rt' => $rt,
                                'rw' => $rw,
                                'address_matches_kk' => $colMatchesKk !== false ? $this->parseYesNo($row[$colMatchesKk]) : 0,
                                'assistance_type' => $colBansos !== false ? $this->parseAssistanceType($row[$colBansos]) : null,
                                // Kolom 106.a berisi '1' atau '>1' (bukan angka riil)
                                // '>1' → 2 (minimal lebih dari 1); akan di-overwrite saat import individu
                                'family_member_count' => $colMemberCount !== false && isset($row[$colMemberCount]) && trim($row[$colMemberCount]) !== ''
                                    ? (trim($row[$colMemberCount]) === '>1' ? 2 : max(1, intval(trim($row[$colMemberCount]))))
                                    : 1,
                                'building_type' => $colBuildingType !== false ? $this->parseBuildingType($row[$colBuildingType]) : null,
                                'ownership_status' => $colOwnership !== false ? $this->parseOwnershipStatus($row[$colOwnership]) : null,
                                'ownership_proof' => $colProof !== false ? $this->parseOwnershipProof($row[$colProof]) : null,
                                'floor_area' => $colFloorArea !== false ? floatval(trim($row[$colFloorArea])) : null,
                                'floor_material' => $colFloorMat !== false ? $this->parseFloorMaterial($row[$colFloorMat]) : null,
                                'wall_material' => $colWallMat !== false ? $this->parseWallMaterial($row[$colWallMat]) : null,
                                'roof_material' => $colRoofMat !== false ? $this->parseRoofMaterial($row[$colRoofMat]) : null,
                                'floor_condition' => $colFloorCond !== false ? $this->parseCondition($row[$colFloorCond]) : null,
                                'wall_condition' => $colWallCond !== false ? $this->parseCondition($row[$colWallCond]) : null,
                                'roof_condition' => $colRoofCond !== false ? $this->parseCondition($row[$colRoofCond]) : null,
                                'toilet_facility' => $colToilet !== false ? $this->parseToiletFacility($row[$colToilet]) : null,
                                'closet_type' => $colCloset !== false ? $this->parseClosetType($row[$colCloset]) : null,
                                'feces_disposal' => $colFeces !== false ? $this->parseFecesDisposal($row[$colFeces]) : null,
                                'water_source' => $colWater !== false ? $this->parseWaterSource($row[$colWater]) : null,
                                'lighting_source' => $colLight !== false ? $this->parseLightingSource($row[$colLight]) : null,
                                'electricity_power' => $electricityPower,
                                'electricity_power_meter_1' => $this->parseElectricityPower($power1),
                                'electricity_power_meter_2' => $this->parseElectricityPower($power2),
                                'electricity_power_meter_3' => $this->parseElectricityPower($power3),
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
                                'cow_count' => $colCow !== false ? intval(trim($row[$colCow])) : 0,
                                'goat_count' => $colGoat !== false ? intval(trim($row[$colGoat])) : 0,
                                'buffalo_count' => $colBuffalo !== false ? intval(trim($row[$colBuffalo])) : 0,
                                // Fix #5: Kolom sewa bangunan (203.a/b/c)
                                'rental_estimate' => $colRentalEstimate !== false ? $this->cleanNumeric(trim($row[$colRentalEstimate])) : null,
                                'rental_free_estimate' => $colRentalFree !== false ? $this->cleanNumeric(trim($row[$colRentalFree])) : null,
                                'rental_contract_value' => $colRentalContract !== false ? $this->cleanNumeric(trim($row[$colRentalContract])) : null,
                                'notes' => $colNotes !== false ? trim($row[$colNotes]) : null,
                            ];

                            $family = Family::withTrashed()->where('kk_number', $kkNumber)->first();
                            if ($family) {
                                $updateData = [];
                                foreach ($dataToSave as $key => $value) {
                                    if ($value !== null && $value !== '') {
                                        $updateData[$key] = $value;
                                    }
                                }
                                $family->fill($updateData);
                                if ($family->trashed()) {
                                    $family->restore();
                                } else {
                                    $family->save();
                                }
                            } else {
                                Family::create(array_merge(['kk_number' => $kkNumber], $dataToSave));
                            }
                            $rowCount++;

                        }

                        \Illuminate\Support\Facades\DB::commit();

                        foreach (\App\Models\StatisticCategory::where('mapping_table', 'families')->get() as $cat) {
                            $cat->save();
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\DB::rollBack();

                        $rowNumber = isset($index) ? ($index + 2) : 'Tidak diketahui';
                        $errorMessage = $e->getMessage();

                        @unlink($filePath);

                        Notification::make()
                            ->title('Import Gagal (Rollback)')
                            ->body("Terjadi kesalahan pada Baris #{$rowNumber}: {$errorMessage}. Seluruh transaksi dibatalkan.")
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
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

    private function parseBuildingType(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'tunggal')) return 'Rumah Tinggal Tunggal';
        if (str_contains($clean, 'deret')) return 'Rumah Tinggal Tunggal'; // tidak ada opsi 'deret' di form, fallback
        if (str_contains($clean, 'rusun') || str_contains($clean, 'apartemen')) return 'Lainnya';
        if (str_contains($clean, 'ruko') || str_contains($clean, 'komersial')) return 'Lainnya';

        return 'Rumah Tinggal Tunggal'; // default
    }

    private function parseOwnershipProof(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'shm') || str_contains($clean, 'sertifikat hak milik')) return 'SHM';
        if (str_contains($clean, 'tidak') || str_contains($clean, 'belum') || str_contains($clean, 'none') || $clean === '-') return 'Tidak Punya';

        return 'Tidak Punya'; // default
    }

    private function parseFloorMaterial(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'keramik') || str_contains($clean, 'kramik')) return 'Keramik';
        if (str_contains($clean, 'ubin') || str_contains($clean, 'tegel') || str_contains($clean, 'teraso')) return 'Ubin / Tegel / Teraso';
        if (str_contains($clean, 'parket') || str_contains($clean, 'vinil') || str_contains($clean, 'karpet')) return 'Parket / Vinil / Karpet';
        if (str_contains($clean, 'kayu') || str_contains($clean, 'papan') || str_contains($clean, 'bambu')) return 'Kayu / Papan';
        if (str_contains($clean, 'tanah')) return 'Tanah';
        if (str_contains($clean, 'semen') || str_contains($clean, 'bata') || str_contains($clean, 'plester') || str_contains($clean, 'cor')) return 'Semen / Bata Merah';

        return trim($val);
    }

    private function parseWallMaterial(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'tembok') || str_contains($clean, 'bata') || str_contains($clean, 'beton')) return 'Tembok';
        if (str_contains($clean, 'seng') || str_contains($clean, 'zink')) return 'Seng';
        if (
            str_contains($clean, 'kayu') || str_contains($clean, 'papan') ||
            str_contains($clean, 'gipsum') || str_contains($clean, 'grc') ||
            str_contains($clean, 'calci') || str_contains($clean, 'triplek') ||
            str_contains($clean, 'bambu')
        ) return 'Kayu / Papan / Gipsum / GRC / Calciboard';

        return trim($val);
    }

    private function parseRoofMaterial(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'genteng') || str_contains($clean, 'keramik atap')) return 'Genteng';
        if (str_contains($clean, 'seng') || str_contains($clean, 'zink') || str_contains($clean, 'spandek')) return 'Seng';
        if (str_contains($clean, 'asbes') || str_contains($clean, 'asb')) return 'Asbes';

        return trim($val);
    }

    private function parseCondition(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'rusak berat') || str_contains($clean, 'parah')) return 'Rusak Berat';
        if (str_contains($clean, 'rusak sedang') || str_contains($clean, 'sedang')) return 'Rusak Sedang';
        if (str_contains($clean, 'rusak ringan') || str_contains($clean, 'ringan')) return 'Rusak Ringan';
        if (str_contains($clean, 'baik') || str_contains($clean, 'bagus') || str_contains($clean, 'layak')) return 'Baik';
        // Cek kata 'rusak' generic terakhir
        if (str_contains($clean, 'rusak')) return 'Rusak Ringan';

        return 'Baik'; // default
    }

    private function parseToiletFacility(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'tidak') || str_contains($clean, 'tidak ada')) return 'Tidak Ada';
        if (str_contains($clean, 'bersama') || str_contains($clean, 'beberapa rumah')) {
            return 'Ada, digunakan bersama oleh anggota keluarga dari beberapa rumah';
        }
        // default: ada dan digunakan sendiri
        return 'Ada, digunakan oleh anggota keluarga dalam satu rumah';
    }

    private function parseClosetType(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'leher angsa') || str_contains($clean, 'angsa')) return 'Leher Angsa';
        if (str_contains($clean, 'plengsengan') || str_contains($clean, 'tutup')) return 'Plengsengan dengan Tutup';
        if (str_contains($clean, 'cemplung') || str_contains($clean, 'cubluk')) return 'Cemplung / Cubluk';
        if (str_contains($clean, 'tidak') || str_contains($clean, 'tidak ada') || $clean === '-') return 'Tidak Ada';

        return 'Tidak Ada'; // default
    }

    private function parseFecesDisposal(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'tangki') || str_contains($clean, 'septik') || str_contains($clean, 'septic')) return 'Tangki Septik';
        if (str_contains($clean, 'kolam') || str_contains($clean, 'sawah') || str_contains($clean, 'sungai') || str_contains($clean, 'danau')) return 'Kolam / Sawah / Sungai / Danau';
        if (str_contains($clean, 'lubang') || str_contains($clean, 'tanah')) return 'Lubang Tanah';
        if (str_contains($clean, 'pantai') || str_contains($clean, 'lapang')) return 'Pantai / Tanah Lapang';

        return 'Lainnya';
    }

    private function parseWaterSource(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'kemasan') || str_contains($clean, 'galon') || str_contains($clean, 'aqua') || str_contains($clean, 'bermerek')) return 'Air kemasan bermerek';
        if (str_contains($clean, 'leding') || str_contains($clean, 'ledeng') || str_contains($clean, 'pdam') || str_contains($clean, 'pam')) return 'Leding';
        if (str_contains($clean, 'bor') || str_contains($clean, 'pompa') || str_contains($clean, 'artesis')) return 'Sumur Bor / Pompa';
        if (str_contains($clean, 'terlindung') || str_contains($clean, 'terlindungi')) return 'Sumur Terlindung';
        if (str_contains($clean, 'mata air')) return 'Mata Air';
        if (str_contains($clean, 'sumur')) return 'Sumur Terlindung'; // fallback sumur tanpa keterangan

        return 'Lainnya';
    }

    private function parseLightingSource(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'non-pln') || str_contains($clean, 'non pln') || str_contains($clean, 'bukan pln') || str_contains($clean, 'genset') || str_contains($clean, 'solar sel')) return 'Listrik Non-PLN';
        if (str_contains($clean, 'tanpa meteran') || str_contains($clean, 'numpang') || str_contains($clean, 'tanpa meter')) return 'Listrik PLN Tanpa Meteran';
        if (str_contains($clean, 'pln') || str_contains($clean, 'meteran') || str_contains($clean, 'meter')) return 'Listrik PLN Dengan Meteran';
        if (str_contains($clean, 'bukan listrik') || str_contains($clean, 'tidak ada listrik') || str_contains($clean, 'lampu minyak') || str_contains($clean, 'lilin')) return 'Bukan Listrik';
        if (str_contains($clean, 'listrik')) return 'Listrik PLN Dengan Meteran'; // fallback listrik generic

        return 'Bukan Listrik';
    }

    private function parseElectricityPower(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        // Normalize numeric-only values (e.g. "900" → "900 Watt")
        $numClean = preg_replace('/[^0-9]/', '', $clean);
        $validWatts = ['450', '900', '1300', '2200', '3500', '4400', '5500', '6600'];
        $formattedMap = [
            '450'  => '450 Watt',
            '900'  => '900 Watt',
            '1300' => '1.300 Watt',
            '2200' => '2.200 Watt',
            '3500' => '3.500 Watt',
            '4400' => '4.400 Watt',
            '5500' => '5.500 Watt',
            '6600' => '6.600 Watt',
        ];

        if (isset($formattedMap[$numClean])) {
            return $formattedMap[$numClean];
        }

        // Handle existing formatted values like "1.300 Watt"
        $numNoDots = str_replace(['.', ','], '', $numClean);
        if (isset($formattedMap[$numNoDots])) {
            return $formattedMap[$numNoDots];
        }

        if (str_contains($clean, '>') || str_contains($clean, 'lebih') || (is_numeric($numClean) && intval($numClean) > 6600)) {
            return '> 6.600 Watt';
        }

        return 'Lainnya';
    }

    private function parseYesNo(?string $val): int
    {
        if ($val === null) return 0;
        $clean = strtolower(trim($val));
        if (empty($clean)) return 0;
        
        $isYes = in_array($clean, ['ya', 'yes', 'true', '1', 'sesuai', 'ada']) 
            || strpos($clean, 'ya') === 0 
            || strpos($clean, 'sesuai') === 0;

        return $isYes ? 1 : 0;
    }

    private function parseOwnershipStatus(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (strpos($clean, 'milik sendiri') !== false || strpos($clean, 'sendiri') !== false) {
            return 'Milik Sendiri';
        } elseif (strpos($clean, 'sewa') !== false && strpos($clean, 'bebas') === false) {
            return 'Sewa / Kontrak';
        }
        return 'Bebas Sewa';
    }

    private function parseAssistanceType(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean) || in_array($clean, ['tidak ada', 'tidak', 'none', '-'])) {
            return 'Tidak Ada';
        }
        return trim($val);
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

    private function cleanName(?string $name): ?string
    {
        if ($name === null) return null;
        $name = trim($name);
        if (empty($name)) return null;

        // Fix spacing around dots (e.g., "A.ismail" -> "A. Ismail")
        $name = preg_replace('/([a-zA-Z])\.(?=[a-zA-Z])/', '$1. ', $name);

        // Convert to UPPERCASE
        return mb_strtoupper($name);
    }

    private function cleanNumeric(?string $val): int
    {
        // BUG-C4 Fix: handle null agar tidak crash saat kolom kosong di Excel
        if ($val === null) return 0;
        $val = strtolower(trim($val));
        if (empty($val) || in_array($val, ['tidak ada', 'none', '-', '?', 'n/a'])) return 0;

        // Support shortcuts like "4,8jt" or "4.8 jt"
        if (strpos($val, 'jt') !== false) {
            $numPart = preg_replace('/[^0-9\.,]/', '', str_replace('jt', '', $val));
            $numPart = str_replace(',', '.', $numPart);
            return intval(floatval($numPart) * 1000000);
        }

        // Clean normal formatted currency like 3,000,000.00 or 3.000.000
        $clean = preg_replace('/[^0-9]/', '', explode('.', $val)[0]);
        return empty($clean) ? 0 : intval($clean);
    }
}

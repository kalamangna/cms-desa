<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Family;
use App\Models\Citizen;
use App\Models\StatisticCategory;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::beginTransaction();

        try {
            // === 1. MERAPIKAN DATA FAMILIES YANG SUDAH TERANJUR DIUPLOAD ===
            Family::withTrashed()->chunk(100, function ($families) {
                foreach ($families as $family) {
                    $updates = [];

                    $bt = $this->parseBuildingType($family->building_type);
                    if ($bt !== $family->building_type) $updates['building_type'] = $bt;

                    $op = $this->parseOwnershipProof($family->ownership_proof);
                    if ($op !== $family->ownership_proof) $updates['ownership_proof'] = $op;

                    $fm = $this->parseFloorMaterial($family->floor_material);
                    if ($fm !== $family->floor_material) $updates['floor_material'] = $fm;

                    $wm = $this->parseWallMaterial($family->wall_material);
                    if ($wm !== $family->wall_material) $updates['wall_material'] = $wm;

                    $rm = $this->parseRoofMaterial($family->roof_material);
                    if ($rm !== $family->roof_material) $updates['roof_material'] = $rm;

                    $fc = $this->parseCondition($family->floor_condition);
                    if ($fc !== $family->floor_condition) $updates['floor_condition'] = $fc;

                    $wc = $this->parseCondition($family->wall_condition);
                    if ($wc !== $family->wall_condition) $updates['wall_condition'] = $wc;

                    $rc = $this->parseCondition($family->roof_condition);
                    if ($rc !== $family->roof_condition) $updates['roof_condition'] = $rc;

                    $tf = $this->parseToiletFacility($family->toilet_facility);
                    if ($tf !== $family->toilet_facility) $updates['toilet_facility'] = $tf;

                    $ct = $this->parseClosetType($family->closet_type);
                    if ($ct !== $family->closet_type) $updates['closet_type'] = $ct;

                    $fd = $this->parseFecesDisposal($family->feces_disposal);
                    if ($fd !== $family->feces_disposal) $updates['feces_disposal'] = $fd;

                    $ws = $this->parseWaterSource($family->water_source);
                    if ($ws !== $family->water_source) $updates['water_source'] = $ws;

                    $ls = $this->parseLightingSource($family->lighting_source);
                    if ($ls !== $family->lighting_source) $updates['lighting_source'] = $ls;

                    $p1 = $this->parseElectricityPower($family->electricity_power_meter_1);
                    if ($p1 !== $family->electricity_power_meter_1) $updates['electricity_power_meter_1'] = $p1;

                    $p2 = $this->parseElectricityPower($family->electricity_power_meter_2);
                    if ($p2 !== $family->electricity_power_meter_2) $updates['electricity_power_meter_2'] = $p2;

                    $p3 = $this->parseElectricityPower($family->electricity_power_meter_3);
                    if ($p3 !== $family->electricity_power_meter_3) $updates['electricity_power_meter_3'] = $p3;

                    if (!empty($updates)) {
                        $family->timestamps = false;
                        $family->update($updates);
                    }
                }
            });

            // === 2. MERAPIKAN DATA CITIZENS YANG SUDAH TERLANJUR DIUPLOAD ===
            Citizen::withTrashed()->chunk(200, function ($citizens) {
                foreach ($citizens as $citizen) {
                    $updates = [];

                    $dt = $this->parseDomicileAddressType($citizen->domicile_address_type);
                    if ($dt !== $citizen->domicile_address_type) $updates['domicile_address_type'] = $dt;

                    $dw = $this->parseHasDigitalWallet($citizen->has_digital_wallet);
                    if ($dw !== $citizen->has_digital_wallet) $updates['has_digital_wallet'] = $dw;

                    $cs = $this->parseCitizenshipStatus($citizen->citizenship_status);
                    if ($cs !== $citizen->citizenship_status) $updates['citizenship_status'] = $cs;

                    $bp = $this->parseBpjsStatus($citizen->bpjs_status);
                    if ($bp !== $citizen->bpjs_status) $updates['bpjs_status'] = $bp;

                    $fr = $this->parseFamilyRelation($citizen->family_relation);
                    if ($fr !== $citizen->family_relation) $updates['family_relation'] = $fr;

                    if (!empty($updates)) {
                        $citizen->timestamps = false;
                        $citizen->update($updates);
                    }
                }
            });

            DB::commit();

            foreach (StatisticCategory::all() as $cat) {
                $cat->save();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function down(): void
    {
        // No-op demi integritas data
    }

    private function parseBuildingType(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'tunggal')) return 'Rumah Tinggal Tunggal';
        if (str_contains($clean, 'deret')) return 'Rumah Tinggal Tunggal';
        if (str_contains($clean, 'rusun') || str_contains($clean, 'apartemen')) return 'Lainnya';
        if (str_contains($clean, 'ruko') || str_contains($clean, 'komersial')) return 'Lainnya';

        return 'Rumah Tinggal Tunggal';
    }

    private function parseOwnershipProof(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'shm') || str_contains($clean, 'sertifikat hak milik')) return 'SHM';
        if (str_contains($clean, 'tidak') || str_contains($clean, 'belum') || str_contains($clean, 'none') || $clean === '-') return 'Tidak Punya';

        return 'Tidak Punya';
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
        if (str_contains($clean, 'rusak')) return 'Rusak Ringan';

        return 'Baik';
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

        return 'Tidak Ada';
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
        if (str_contains($clean, 'sumur')) return 'Sumur Terlindung';

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
        if (str_contains($clean, 'listrik')) return 'Listrik PLN Dengan Meteran';

        return 'Bukan Listrik';
    }

    private function parseElectricityPower(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        $numClean = preg_replace('/[^0-9]/', '', $clean);
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

        $numNoDots = str_replace(['.', ','], '', $numClean);
        if (isset($formattedMap[$numNoDots])) {
            return $formattedMap[$numNoDots];
        }

        if (str_contains($clean, '>') || str_contains($clean, 'lebih') || (is_numeric($numClean) && intval($numClean) > 6600)) {
            return '> 6.600 Watt';
        }

        return 'Lainnya';
    }

    private function parseDomicileAddressType(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'kk dan ktp') || str_contains($clean, 'ktp dan kk') || str_contains($clean, 'keduanya')) return 'Sesuai KK dan KTP';
        if (str_contains($clean, 'hanya') && str_contains($clean, 'kk')) return 'Hanya sesuai KK';
        if (str_contains($clean, 'hanya') && str_contains($clean, 'ktp')) return 'Hanya sesuai KTP';
        if (str_contains($clean, 'tidak sesuai') || str_contains($clean, 'tidak cocok')) return 'Tidak sesuai KK dan KTP';
        if (str_contains($clean, 'kk')) return 'Sesuai KK dan KTP';

        return 'Sesuai KK dan KTP';
    }

    private function parseHasDigitalWallet(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean) || in_array($clean, ['tidak', 'tidak ada', 'none', '-'])) return 'Tidak ada';

        if (str_contains($clean, 'usaha dan pribadi') || str_contains($clean, 'pribadi dan usaha')) return 'Ya untuk usaha dan pribadi';
        if (str_contains($clean, 'usaha')) return 'Ya untuk usaha';
        if (str_contains($clean, 'pribadi')) return 'Ya untuk pribadi';
        if (str_contains($clean, 'ya')) return 'Ya untuk pribadi';

        return 'Tidak ada';
    }

    private function parseCitizenshipStatus(?string $val): string
    {
        if ($val === null) return 'Tinggal di Rumah Ini';
        $clean = strtolower(trim($val));
        if (empty($clean)) return 'Tinggal di Rumah Ini';

        if (str_contains($clean, 'luar negeri')) return 'Pindah ke Luar Negeri';
        if (str_contains($clean, 'daerah lain') || str_contains($clean, 'indonesia')) return 'Pindah ke Daerah Lain (Indonesia)';
        if (str_contains($clean, 'pisah kk') || str_contains($clean, 'pisah')) return 'Sudah Pisah KK';
        if (str_contains($clean, 'meninggal')) return 'Meninggal';
        if (str_contains($clean, 'pindah')) return 'Pindah';
        if (str_contains($clean, 'tinggal') || str_contains($clean, 'rumah ini')) return 'Tinggal di Rumah Ini';

        return 'Tinggal di Rumah Ini';
    }

    private function parseBpjsStatus(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (str_contains($clean, 'tidak') || str_contains($clean, 'non bpjs') || $clean === '-') return 'Tidak Terdaftar';
        if (str_contains($clean, 'pbi') && (str_contains($clean, 'pusat') || str_contains($clean, 'tunjangan') || str_contains($clean, 'apbd') === false)) {
            if (str_contains($clean, 'pusat') || str_contains($clean, 'kemensos') || str_contains($clean, 'iuran pemerintah')) {
                return 'BPJS PBI Tunjangan Pemerintah Pusat';
            }
            return 'BPJS PBI Pemda';
        }
        if (str_contains($clean, 'pemda') || str_contains($clean, 'daerah') || str_contains($clean, 'apbd')) return 'BPJS PBI Pemda';
        if (str_contains($clean, 'mandiri') || str_contains($clean, 'bayar sendiri') || str_contains($clean, 'non pbi')) return 'BPJS Mandiri';
        if (str_contains($clean, 'pbi')) return 'BPJS PBI Pemda';
        if (str_contains($clean, 'bpjs') || str_contains($clean, 'jkn') || str_contains($clean, 'kis')) return 'BPJS Mandiri';

        return 'Tidak Terdaftar';
    }

    private function parseFamilyRelation(?string $val): ?string
    {
        if ($val === null) return null;
        $clean = strtolower(trim($val));
        if (empty($clean)) return null;

        if (strpos($clean, 'kepala') !== false || $clean === 'kk') return 'Kepala Keluarga';
        if (strpos($clean, 'istri') !== false || strpos($clean, 'suami') !== false) return 'Istri';
        if (strpos($clean, 'menantu') !== false) return 'Menantu';
        if (strpos($clean, 'pembantu') !== false || strpos($clean, 'asisten') !== false) return 'Pembantu';
        if (strpos($clean, 'anak') !== false) return 'Anak';
        if (strpos($clean, 'cucu') !== false) return 'Cucu';
        if (strpos($clean, 'mertua') !== false) return 'Mertua';
        if (
            strpos($clean, 'orang tua') !== false ||
            strpos($clean, 'ortu') !== false ||
            strpos($clean, 'bapak') !== false ||
            $clean === 'ibu' ||
            strpos($clean, 'ayah') !== false
        ) return 'Orang Tua';
        if (strpos($clean, 'famili') !== false || strpos($clean, 'saudara') !== false || strpos($clean, 'keponakan') !== false) return 'Famili Lain';

        return 'Lainnya';
    }
};

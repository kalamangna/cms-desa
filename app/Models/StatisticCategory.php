<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'is_active', 'mapping_table', 'mapping_column', 'secondary_columns',
        'comparison_column', 'comparison_value_a', 'comparison_value_b', 'comparison_label_a', 'comparison_label_b'
    ];

    protected $casts = [
        'mapping_column' => 'array',
        'secondary_columns' => 'array',
    ];

    public function indicators()
    {
        return $this->hasMany(StatisticIndicator::class);
    }

    /** Daftar tabel yang diizinkan untuk mapping dinamis. */
    const ALLOWED_TABLES = ['citizens', 'families'];

    /** Daftar kolom yang diizinkan per tabel. */
    const ALLOWED_COLUMNS = [
        'citizens' => [
            'gender', 'education_level', 'job', 'job_status', 'marital_status', 'family_relation',
            'school_participation', 'disability_physical', 'disability_mental',
            'disability_intellectual', 'disability_blind', 'disability_deaf',
            'disability_speech', 'illness_hypertension', 'illness_rheumatic',
            'illness_asthma', 'illness_heart', 'illness_diabetes', 'illness_tbc',
            'illness_stroke', 'illness_cancer', 'illness_kidney', 'illness_hemophilia',
            'illness_hiv', 'illness_cholesterol', 'illness_liver', 'illness_thalassemia',
            'illness_leukemia', 'illness_alzheimer', 'illness_other',
            'has_digital_wallet', 'has_income', 'pip_status', 'bpjs_status', 'citizenship_status',
        ],
        'families' => [
            'assistance_type', 'ownership_status', 'building_type', 'ownership_proof',
            'water_source', 'lighting_source', 'floor_material', 'wall_material',
            'roof_material', 'toilet_facility', 'closet_type', 'address_matches_kk',
        ],
    ];

    protected static function booted()
    {
        static::saving(function ($category) {
            if (empty($category->slug) || $category->isDirty('name')) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });

        static::saved(function ($category) {
            if ($category->mapping_table && !empty($category->mapping_column)) {
                $columns = is_array($category->mapping_column) 
                    ? $category->mapping_column 
                    : (json_decode($category->mapping_column, true) ?: [$category->mapping_column]);

                if (! in_array($category->mapping_table, self::ALLOWED_TABLES, true)) {
                    return;
                }
                $allowedColumns = self::ALLOWED_COLUMNS[$category->mapping_table] ?? [];
                $labels = self::getColumnLabels();

                // Delete old indicators
                $category->indicators()->delete();

                foreach ($columns as $col) {
                    if (! in_array($col, $allowedColumns, true)) {
                        continue;
                    }

                    $unit = $category->mapping_table === 'families' ? 'Keluarga' : 'Jiwa';

                    if (self::isBooleanColumn($col)) {
                        $indicatorName = $labels[$col] ?? ucwords(str_replace('_', ' ', $col));
                        $category->indicators()->create([
                            'name' => $indicatorName,
                            'unit' => $unit,
                            'mapping_column' => $col,
                            'mapping_operator' => '=',
                            'mapping_value' => '1',
                        ]);
                    } elseif ($col === 'education_level') {
                        $eduItems = [
                            ['name' => 'Tidak Punya Ijazah SD', 'mapping_value' => 'Tidak Punya Ijazah SD'],
                            ['name' => 'SD / Sederajat', 'mapping_value' => 'SD / Sederajat'],
                            ['name' => 'SMP / Sederajat', 'mapping_value' => 'SMP / Sederajat'],
                            ['name' => 'SMA / Sederajat', 'mapping_value' => 'SMA / Sederajat'],
                            ['name' => 'D1 / D2 / D3', 'mapping_value' => 'D1 / D2 / D3'],
                            ['name' => 'D4 / S1 / Profesi', 'mapping_value' => 'D4 / S1 / Profesi'],
                            ['name' => 'S2 / S3', 'mapping_value' => 'S2 / S3'],
                        ];
                        foreach ($eduItems as $idx => $item) {
                            $category->indicators()->create([
                                'name' => $item['name'],
                                'unit' => 'Jiwa',
                                'mapping_column' => 'education_level',
                                'mapping_operator' => '=',
                                'mapping_value' => $item['mapping_value'],
                                'order' => $idx + 1,
                                'is_active' => true,
                            ]);
                        }
                    } elseif ($col === 'job_status') {
                        $jobStatusItems = [
                            ['name' => 'Berusaha Sendiri', 'mapping_value' => 'Berusaha Sendiri'],
                            ['name' => 'Buruh / Karyawan / Pegawai Swasta', 'mapping_value' => 'Buruh / Karyawan / Pegawai Swasta'],
                            ['name' => 'Pekerja Bebas', 'mapping_value' => 'Pekerja Bebas'],
                            ['name' => 'Pekerja Keluarga / Tidak Dibayar', 'mapping_value' => 'Pekerja Keluarga / Tidak Dibayar'],
                            ['name' => 'ASN / TNI / Polri / BUMN / BUMD / Pejabat Negara', 'mapping_value' => 'ASN / TNI / Polri / BUMN / BUMD / Pejabat Negara'],
                            ['name' => 'Berusaha Dibantu Buruh', 'mapping_value' => 'Berusaha Dibantu Buruh'],
                            ['name' => 'Lainnya', 'mapping_value' => 'Lainnya'],
                        ];
                        foreach ($jobStatusItems as $idx => $item) {
                            $category->indicators()->create([
                                'name' => $item['name'],
                                'unit' => 'Jiwa',
                                'mapping_column' => 'job_status',
                                'mapping_operator' => '=',
                                'mapping_value' => $item['mapping_value'],
                                'order' => $idx + 1,
                                'is_active' => true,
                            ]);
                        }
                    } elseif ($col === 'has_digital_wallet') {
                        $walletItems = [
                            ['name' => 'Tidak Ada', 'mapping_value' => 'Tidak ada'],
                            ['name' => 'Ya untuk Pribadi', 'mapping_value' => 'Ya untuk pribadi'],
                            ['name' => 'Ya untuk Usaha & Pribadi', 'mapping_value' => 'Ya untuk usaha dan pribadi'],
                            ['name' => 'Ya untuk Usaha', 'mapping_value' => 'Ya untuk usaha'],
                        ];
                        foreach ($walletItems as $idx => $item) {
                            $category->indicators()->create([
                                'name' => $item['name'],
                                'unit' => 'Jiwa',
                                'mapping_column' => 'has_digital_wallet',
                                'mapping_operator' => '=',
                                'mapping_value' => $item['mapping_value'],
                                'order' => $idx + 1,
                                'is_active' => true,
                            ]);
                        }
                    } elseif ($col === 'bpjs_status') {
                        $bpjsItems = [
                            ['name' => 'BPJS PBI Pemda', 'mapping_value' => 'BPJS PBI Pemda'],
                            ['name' => 'BPJS Mandiri', 'mapping_value' => 'BPJS Mandiri'],
                            ['name' => 'BPJS PBI Tunjangan Pemerintah Pusat', 'mapping_value' => 'BPJS PBI Tunjangan Pemerintah Pusat'],
                            ['name' => 'Tidak Terdaftar', 'mapping_value' => 'Tidak Terdaftar'],
                        ];
                        foreach ($bpjsItems as $idx => $item) {
                            $category->indicators()->create([
                                'name' => $item['name'],
                                'unit' => 'Jiwa',
                                'mapping_column' => 'bpjs_status',
                                'mapping_operator' => '=',
                                'mapping_value' => $item['mapping_value'],
                                'order' => $idx + 1,
                                'is_active' => true,
                            ]);
                        }
                    } elseif ($col === 'assistance_type') {
                        $bansosItems = [
                            ['name' => 'PKH', 'operator' => 'LIKE', 'value' => '%PKH%'],
                            ['name' => 'BPNT / Sembako', 'operator' => 'LIKE', 'value' => '%BPNT%'],
                            ['name' => 'BLT Desa', 'operator' => 'LIKE', 'value' => '%BLT%'],
                            ['name' => 'Subsidi Listrik', 'operator' => 'LIKE', 'value' => '%Subsidi Listrik%'],
                            ['name' => 'Bedah Rumah', 'operator' => 'LIKE', 'value' => '%Bedah Rumah%'],
                            ['name' => 'Bantuan Lainnya', 'operator' => 'LIKE', 'value' => '%Bantuan Lainnya%'],
                            ['name' => 'Tidak Menerima Bantuan', 'operator' => '=', 'value' => 'Tidak Ada'],
                        ];
                        foreach ($bansosItems as $idx => $item) {
                            $category->indicators()->create([
                                'name' => $item['name'],
                                'unit' => 'Keluarga',
                                'mapping_column' => 'assistance_type',
                                'mapping_operator' => $item['operator'],
                                'mapping_value' => $item['value'],
                                'order' => $idx + 1,
                                'is_active' => true,
                            ]);
                        }
                    } else {
                        $query = \Illuminate\Support\Facades\DB::table($category->mapping_table)
                            ->whereNull('deleted_at')
                            ->whereNotNull($col)
                            ->where($col, '!=', '');

                        if ($category->mapping_table === 'citizens') {
                            $query->where('status', 'Aktif');
                        }

                        $values = $query->distinct()->pluck($col)->toArray();
                        $normalizedValues = [];

                        foreach ($values as $val) {
                            $valStr = trim((string)$val);
                            if ($valStr === '') continue;

                            // Normalisasi spasi ganda
                            $valStr = preg_replace('/\s+/', ' ', $valStr);

                            // Ubah ke Title Case
                            $normalized = ucwords(strtolower($valStr));

                            // Penanganan singkatan umum agar tetap UPPERCASE
                            $abbreviations = [
                                'Pns', 'Sd', 'Smp', 'Sma', 'Bkkbn', 'Kk', 'Bpjs', 'Rt', 'Rw', 
                                'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3', 'Pip', 'Pkh', 
                                'Bnt', 'Blt', 'Kps', 'Kip', 'Kis', 'Sktm', 'Pbi', 'Non Pbi'
                            ];
                            foreach ($abbreviations as $abbr) {
                                // Match exact word bounds or whole string
                                if (strtolower($normalized) === strtolower($abbr)) {
                                    $normalized = strtoupper($abbr);
                                }
                            }

                            $normalizedValues[$normalized][] = $valStr;
                        }

                        foreach ($normalizedValues as $indicatorName => $originalValues) {
                            $mappingValue = $originalValues[0];

                            $category->indicators()->create([
                                'name' => $indicatorName,
                                	'unit' => $unit,
                                'mapping_column' => $col,
                                'mapping_operator' => '=',
                                'mapping_value' => $mappingValue,
                            ]);
                        }
                    }
                }
            }
        });
    }

    public static function getColumnLabels(): array
    {
        return [
            // citizens
            'gender' => 'Jenis Kelamin',
            'education_level' => 'Tingkat Pendidikan Terakhir',
            'job' => 'Pekerjaan/Profesi',
            'disability_physical' => 'Disabilitas Fisik',
            'disability_mental' => 'Disabilitas Mental',
            'disability_intellectual' => 'Disabilitas Intelektual',
            'disability_blind' => 'Disabilitas Sensorik Netra',
            'disability_deaf' => 'Disabilitas Sensorik Rungu',
            'disability_speech' => 'Disabilitas Sensorik Wicara',
            'illness_hypertension' => 'Penyakit Hipertensi',
            'illness_rheumatic' => 'Penyakit Rematik',
            'illness_asthma' => 'Penyakit Asma',
            'illness_heart' => 'Penyakit Jantung',
            'illness_diabetes' => 'Penyakit Diabetes',
            'illness_tbc' => 'Penyakit TBC',
            'illness_stroke' => 'Penyakit Stroke',
            'illness_cancer' => 'Penyakit Kanker',
            'illness_kidney' => 'Penyakit Gagal Ginjal',
            'illness_cholesterol' => 'Penyakit Kolesterol',
            'illness_other' => 'Penyakit Lainnya',
            'has_digital_wallet' => 'Kepemilikan Dompet Digital/Rekening',
            
            // families
            'assistance_type' => 'Jenis Bantuan Sosial',
            'ownership_status' => 'Status Kepemilikan Rumah',
            'building_type' => 'Jenis Bangunan',
            'ownership_proof' => 'Bukti Kepemilikan Rumah',
            'water_source' => 'Sumber Air Minum',
            'lighting_source' => 'Sumber Penerangan',
        ];
    }

    public static function isBooleanColumn(?string $column): bool
    {
        if (empty($column)) return false;
        return str_starts_with($column, 'disability_') ||
               str_starts_with($column, 'illness_') ||
               str_starts_with($column, 'has_') ||
               $column === 'address_matches_kk' ||
               $column === 'pip_status';
    }
}

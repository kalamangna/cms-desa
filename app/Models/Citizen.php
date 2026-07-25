<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Citizen extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nik',
        'dusun_id',
        'family_id',
        'kk_order',
        'name',
        'place_of_birth',
        'date_of_birth',
        'gender',
        'religion',
        'education',
        'job',
        'blood_type',
        'marital_status',
        'family_relation',
        'school_participation',
        'education_level',
        'bpjs_status',
        'pip_status',
        'has_income',
        'job_status',
        'income_salary',
        'income_allowance',
        'income_food',
        'income_honor',
        'income_overtime',
        'income_other',
        'income_business',
        'income_passive',
        'disability_physical',
        'disability_mental',
        'disability_intellectual',
        'disability_blind',
        'disability_deaf',
        'disability_speech',
        'illness_hypertension',
        'illness_rheumatic',
        'illness_asthma',
        'illness_heart',
        'illness_diabetes',
        'illness_tbc',
        'illness_stroke',
        'illness_cancer',
        'illness_kidney',
        'illness_hemophilia',
        'illness_hiv',
        'illness_cholesterol',
        'illness_liver',
        'illness_thalassemia',
        'illness_leukemia',
        'illness_alzheimer',
        'illness_other',
        'has_digital_wallet',
        'address',
        'rt',
        'rw',
        'status',
        'citizenship_status',
        'domicile_address_type',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'kk_order' => 'integer',
        'income_salary' => 'integer',
        'income_allowance' => 'integer',
        'income_food' => 'integer',
        'income_honor' => 'integer',
        'income_overtime' => 'integer',
        'income_other' => 'integer',
        'income_business' => 'integer',
        'income_passive' => 'integer',
        'pip_status' => 'integer',
        'has_income' => 'integer',
        'disability_physical' => 'integer',
        'disability_mental' => 'integer',
        'disability_intellectual' => 'integer',
        'disability_blind' => 'integer',
        'disability_deaf' => 'integer',
        'disability_speech' => 'integer',
        'illness_hypertension' => 'integer',
        'illness_rheumatic' => 'integer',
        'illness_asthma' => 'integer',
        'illness_heart' => 'integer',
        'illness_diabetes' => 'integer',
        'illness_tbc' => 'integer',
        'illness_stroke' => 'integer',
        'illness_cancer' => 'integer',
        'illness_kidney' => 'integer',
        'illness_hemophilia' => 'integer',
        'illness_hiv' => 'integer',
        'illness_cholesterol' => 'integer',
        'illness_liver' => 'integer',
        'illness_thalassemia' => 'integer',
        'illness_leukemia' => 'integer',
        'illness_alzheimer' => 'integer',
        'illness_other' => 'integer',
    ];

    public function getGenderAttribute($value)
    {
        if (! empty($value)) {
            return $value;
        }

        if ($this->nik && strlen($this->nik) >= 12) {
            $day = (int) substr($this->nik, 6, 2);
            if ($day > 40) {
                return 'Perempuan';
            } elseif ($day >= 1 && $day <= 31) {
                return 'Laki-laki';
            }
        }

        return '-';
    }

    public function getEducationLevelAttribute($value)
    {
        if (! empty($value)) {
            return $value;
        }

        return 'Tidak Punya Ijazah SD';
    }

    public function getSchoolParticipationAttribute($value)
    {
        if (empty($value)) {
            return 'Tidak / Belum Pernah Sekolah';
        }

        $valLower = strtolower(trim($value));
        if (str_contains($valLower, 'masih')) {
            return 'Masih Sekolah';
        }
        if (str_contains($valLower, 'lagi')) {
            return 'Tidak Bersekolah Lagi';
        }
        if (str_contains($valLower, 'tidak') || str_contains($valLower, 'belum')) {
            return 'Tidak / Belum Pernah Sekolah';
        }

        return $value;
    }

    public function getBpjsStatusAttribute($value)
    {
        if (! empty($value)) {
            return $value;
        }

        return 'Tidak Terdaftar';
    }

    public function getCitizenshipStatusAttribute($value)
    {
        if (empty($value)) {
            return 'Tinggal di Rumah Ini';
        }

        $valLower = strtolower(trim($value));
        if (str_contains($valLower, 'luar negeri')) {
            return 'Pindah ke Luar Negeri';
        }
        if (str_contains($valLower, 'indonesia') || str_contains($valLower, 'wilayah') || str_contains($valLower, 'daerah')) {
            return 'Pindah ke Daerah Lain (Indonesia)';
        }
        if (str_contains($valLower, 'pisah')) {
            return 'Sudah Pisah KK';
        }
        if (str_contains($valLower, 'meninggal')) {
            return 'Meninggal';
        }
        if (str_contains($valLower, 'tinggal') || str_contains($valLower, 'rumah')) {
            return 'Tinggal di Rumah Ini';
        }

        return $value;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($citizen) {
            if ($citizen->education_level && !$citizen->education) {
                $citizen->education = $citizen->education_level;
            } elseif ($citizen->education && !$citizen->education_level) {
                $citizen->education_level = $citizen->education;
            }
        });

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('home_total_penduduk_real');
            \Illuminate\Support\Facades\Cache::forget('home_job_stats');
            \Illuminate\Support\Facades\Cache::forget('home_edu_stats');
            \Illuminate\Support\Facades\Cache::forget('home_religion_stats');
            \Illuminate\Support\Facades\Cache::forget('home_laki_laki_count');
            \Illuminate\Support\Facades\Cache::forget('home_perempuan_count');
            \Illuminate\Support\Facades\Cache::forget('home_disabilitas_count');
            \Illuminate\Support\Facades\Cache::forget('profil_total_penduduk');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('home_total_penduduk_real');
            \Illuminate\Support\Facades\Cache::forget('home_job_stats');
            \Illuminate\Support\Facades\Cache::forget('home_edu_stats');
            \Illuminate\Support\Facades\Cache::forget('home_religion_stats');
            \Illuminate\Support\Facades\Cache::forget('home_laki_laki_count');
            \Illuminate\Support\Facades\Cache::forget('home_perempuan_count');
            \Illuminate\Support\Facades\Cache::forget('home_disabilitas_count');
            \Illuminate\Support\Facades\Cache::forget('profil_total_penduduk');
        });
    }

    public function dusun()
    {
        return $this->belongsTo(Dusun::class);
    }

    public function family()
    {
        return $this->belongsTo(Family::class);
    }
}

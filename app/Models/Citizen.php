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

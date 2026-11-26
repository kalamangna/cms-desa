<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Service extends Model
{
    use HasSlug;

    public const CATEGORIES = [
        'Kependudukan' => 'Kependudukan',
        'Ekonomi dan Sosial' => 'Ekonomi dan Sosial',
        'Perizinan dan Umum' => 'Perizinan dan Umum',
        'Layanan Lainnya' => 'Layanan Lainnya',
    ];

    protected $fillable = [
        'title',
        'slug',
        'category',
        'sort_order',
        'description',
        'requirements',
        'letter_code',
        'letter_title',
        'letter_template',
        'form_fields',
        'is_tte_enabled',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'form_fields' => 'array',
            'is_tte_enabled' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('services_list'));
        static::deleted(fn () => Cache::forget('services_list'));
    }

    public function requirementItems(): HasMany
    {
        return $this->hasMany(ServiceRequirement::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * Ikon FontAwesome ditentukan otomatis berdasarkan kata kunci judul layanan.
     * Fallback: fa-file-lines
     */
    protected function icon(): Attribute
    {
        return Attribute::make(
            get: function () {
                $title = strtolower($this->title ?? '');

                $map = [
                    // Kependudukan & Identitas
                    'ktp' => 'fa-id-card',
                    'kartu keluarga' => 'fa-people-roof',
                    'akte' => 'fa-certificate',
                    'akta' => 'fa-certificate',
                    'lahir' => 'fa-baby',
                    'kematian' => 'fa-cross',
                    'nikah' => 'fa-heart',
                    'pindah' => 'fa-truck-moving',
                    'domisili' => 'fa-house-circle-check',
                    'tinggal' => 'fa-house-user',

                    // Sosial & Ekonomi
                    'usaha' => 'fa-store',
                    'umkm' => 'fa-store',
                    'izin' => 'fa-file-circle-check',
                    'perdagangan' => 'fa-shop',
                    'tanah' => 'fa-map',
                    'waris' => 'fa-scroll',
                    'ahli waris' => 'fa-scroll',
                    'tidak mampu' => 'fa-hand-holding-heart',
                    'miskin' => 'fa-hand-holding-heart',
                    'bantuan' => 'fa-hand-holding-dollar',
                    'skck' => 'fa-shield-halved',
                    'catatan polisi' => 'fa-shield-halved',

                    // Administrasi Umum
                    'pengantar' => 'fa-file-pen',
                    'keterangan' => 'fa-file-lines',
                    'rekomendasi' => 'fa-thumbs-up',
                    'legalisir' => 'fa-stamp',
                    'surat' => 'fa-envelope',
                ];

                foreach ($map as $keyword => $icon) {
                    if (str_contains($title, $keyword)) {
                        return $icon;
                    }
                }

                return 'fa-file-lines';
            }
        );
    }
}

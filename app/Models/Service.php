<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'requirements',
    ];

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

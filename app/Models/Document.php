<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasSlug, SoftDeletes;

    public const CATEGORY_OPTIONS = [
        'perdes' => 'Peraturan Desa',
        'perkades' => 'Peraturan Kepala Desa',
        'sk_kades' => 'Keputusan Kepala Desa',
        'perencanaan' => 'Dokumen Perencanaan',
        'laporan' => 'Laporan Penyelenggaraan',
        'lainnya' => 'Dokumen Lainnya',
    ];

    public const INFORMATION_CLASSIFICATION_OPTIONS = [
        'berkala' => 'Informasi Berkala',
        'serta_merta' => 'Informasi Serta-Merta',
        'setiap_saat' => 'Informasi Setiap Saat',
    ];

    protected $fillable = [
        'title',
        'slug',
        'file',
        'description',
        'category',
        'information_classification',
        'document_number',
        'year',
        'published_date',
    ];

    protected $casts = [
        'year' => 'integer',
        'published_date' => 'date',
    ];

    public function getCategoryLabelAttribute(): ?string
    {
        return self::CATEGORY_OPTIONS[$this->category] ?? null;
    }

    public function getInformationClassificationLabelAttribute(): ?string
    {
        return self::INFORMATION_CLASSIFICATION_OPTIONS[$this->information_classification] ?? null;
    }
}

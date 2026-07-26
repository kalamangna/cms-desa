<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class VisitorLog extends Model
{
    use Prunable;

    const UPDATED_AT = null;

    protected $fillable = [
        'ip_hash',
        'url',
        'user_agent',
        'city',
        'region',
        'country',
        'visit_date',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    /**
     * Get the prunable model query.
     * Membersihkan log statistik pengunjung yang usianya lebih dari 365 hari (1 tahun).
     */
    public function prunable(): Builder
    {
        return static::where('visit_date', '<', now()->subDays(365));
    }
}

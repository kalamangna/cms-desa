<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $fillable = [
        'ip_hash',
        'url',
        'user_agent',
        'visit_date',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];
}

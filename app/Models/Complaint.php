<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = ['ticket_number', 'name', 'phone', 'title', 'content', 'status', 'response'];

    protected static function booted()
    {
        static::creating(function ($complaint) {
            $date = now()->format('Ymd');
            $random = strtoupper(bin2hex(random_bytes(2)));
            $complaint->ticket_number = 'ADV-' . $date . '-' . $random;
        });
    }
}

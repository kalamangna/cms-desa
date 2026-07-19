<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = ['ticket_number', 'nik', 'name', 'phone', 'service_id', 'status', 'admin_response'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    protected static function booted()
    {
        static::creating(function ($request) {
            $date = now()->format('Ymd');
            $random = strtoupper(bin2hex(random_bytes(2)));
            $request->ticket_number = 'SRV-' . $date . '-' . $random;
        });
    }
}

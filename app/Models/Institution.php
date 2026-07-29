<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'motto',
    ];

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($institution) {
            if (empty($institution->slug)) {
                $institution->slug = Str::slug($institution->name);
            }
            if ($institution->logo) {
                $institution->logo = \App\Helpers\ImageHelper::convertToWebp($institution->logo, 'institutions');
            }
        });

        static::updating(function ($institution) {
            if ($institution->isDirty('name') && empty($institution->slug)) {
                $institution->slug = Str::slug($institution->name);
            }
            if ($institution->isDirty('logo') && $institution->logo) {
                $institution->logo = \App\Helpers\ImageHelper::convertToWebp($institution->logo, 'institutions');
            }
        });
    }
}

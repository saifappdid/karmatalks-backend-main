<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstroService extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function astro()
    {
        return $this->belongsTo(Astro::class);
    }
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}

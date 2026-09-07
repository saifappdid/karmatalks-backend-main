<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstroWorkingHour extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function workingDay()
    {
        return $this->belongsTo(AstroWorkingDay::class, 'astro_working_day_id');
    }
    
}

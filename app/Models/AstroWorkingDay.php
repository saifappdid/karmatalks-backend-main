<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstroWorkingDay extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function workingHours()
    {
        return $this->hasMany(AstroWorkingHour::class);
    }
}

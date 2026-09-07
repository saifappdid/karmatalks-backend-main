<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function scopeUser($query)
    {
        return $query->where('type', 'user');
    }
    public function scopeAstro($query)
    {
        return $query->where('type', 'astro');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstroLeadSuggestion extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function astro()
    {
        return $this->belongsTo(Astro::class);
    }
}

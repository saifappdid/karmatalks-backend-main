<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstroGallery extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function getImageAttribute($value)
    {
        if ($value) {
            if (strpos($value, 'https://karmatalks.blr1.digitaloceanspaces.com') === 0) {
                return str_replace('https://karmatalks.blr1.digitaloceanspaces.com', 'https://karmatalks.blr1.cdn.digitaloceanspaces.com', $value);
            }
        }
        return $value;
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

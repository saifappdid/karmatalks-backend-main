<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function scopeProduct($query)
    {
        return $query->where('type', 'product');
    }
    public function scopeService($query)
    {
        return $query->where('type', 'service');
    }
}

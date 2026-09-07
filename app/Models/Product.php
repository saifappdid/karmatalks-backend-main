<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function getImageAttribute($value)
    {
        if ($value) {
            if (strpos($value, 'https://karmatalks.blr1.digitaloceanspaces.com') === 0) {
                return str_replace('https://karmatalks.blr1.digitaloceanspaces.com', 'https://karmatalks.blr1.cdn.digitaloceanspaces.com', $value);
            }
        }
        return $value;
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function scopeSearch($query, array $search)
    {
        if ($search['search'] ?? false) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search['search']}%")
                    ->orWhere('description', 'like', "%{$search['search']}%")
                    ->orWhere('price', 'like', "%{$search['search']}%");
            });
        }
    }
}

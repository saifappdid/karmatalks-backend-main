<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $table = 'states';
    protected $guarded = [];
    public function scopeIndia($q)
    {
        return $q->where('country_id', '101');
    }
    public function appuser()
    {
        return $this->hasMany(AppUser::class);
    }
}

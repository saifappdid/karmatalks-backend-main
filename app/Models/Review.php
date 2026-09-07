<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function astro()
    {
        return $this->belongsTo(Astro::class, 'astro_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function instantConsultationHistory()
    {
        return $this->belongsTo(InstantConsultationHistory::class, 'instant_consultation_history_id');
    }
}

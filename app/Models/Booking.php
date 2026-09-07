<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function transaction()
    {
        return $this->hasOne(BookingTransaction::class, 'booking_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function astro()
    {
        return $this->belongsTo(Astro::class, 'astro_id', 'id');
    }
    public function scheduleConsultationLog()
    {
        return $this->hasMany(ScheduleConsultationCallHistory::class, 'booking_id', 'id')->orderBy('id', 'desc');
    }
    public function media()
    {
        return $this->hasMany(ScheduleConsultationSharedMedia::class, 'booking_id', 'id')->orderBy('id', 'desc');
    }
    public function review()
    {
        return $this->hasMany(Review::class, 'booking_id', 'id');
    }
}

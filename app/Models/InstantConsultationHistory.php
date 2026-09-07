<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstantConsultationHistory extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'amount' => 'float',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function astro()
    {
        return $this->belongsTo(Astro::class, 'astro_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function chats()
    {
        return $this->hasMany(Chat::class, 'conversation_id');
    }
    public function media()
    {
        return $this->hasMany(ScheduleConsultationSharedMedia::class, 'instant_call_id');
    }
    public function consultationRequest()
    {
        return $this->belongsTo(InstantConnectionRequest::class, 'connection_request_id');
    }
    public function review()
    {
        return $this->hasOne(Review::class, 'instant_consultation_history_id', 'id');
    }
}

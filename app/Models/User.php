<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $guarded = [];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'first_name',
    //     'last_name',
    //     'email',
    //     'password',
    //     'phone',
    //     'image',
    //     'address',
    //     'gender',
    //     'firebase_uid',
    //     'status',
    //     'device_id',
    // ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'isAdmin',
        'firebase_uid',
        // 'device_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithoutAdmin($query)
    {
        return $query;
    }

    // public function getNameAttribute()
    // {
    //     return $this->first_name . ' ' . $this->last_name;
    // }
    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }
    public function followedAstros()
    {
        return $this->belongsToMany(Astro::class, 'astro_followers', 'user_id', 'astro_id');
    }
    public function astroSuggestion()
    {
        return $this->belongsToMany(Astro::class, 'astro_lead_suggestions', 'user_id', 'astro_id');
    }
    public function walletTransaction()
    {
        return $this->hasMany(WalletTransaction::class, 'user_id', 'id');
    }
    public function instantConsultationHistory()
    {
        return $this->hasMany(InstantConsultationHistory::class, 'user_id', 'id');
    }
}

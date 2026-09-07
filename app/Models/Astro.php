<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Astro extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasApiTokens;
    protected $guarded = [];
    public function getProfileAttribute($value)
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
    public function price()
    {
        return $this->hasMany(AstroPrice::class, 'astro_id', 'id');
    }
    public function scopeSearch($query, array $search)
    {
        if ($search['search'] ?? false) {
            $searchValue = trim($search['search']);
            $tokens      = preg_split('/\s+/', $searchValue);

            $query->where(function ($query) use ($tokens) {
                foreach ($tokens as $token) {
                    $token = trim($token);
                    if ($token === '') {
                        continue;
                    }

                    $query->where(function ($q) use ($token) {
                        $q->whereRaw("CONCAT_WS(' ', first_name, middle_name, last_name) LIKE ?", ["%{$token}%"])
                            ->orWhere('first_name', 'LIKE', "%{$token}%")
                            ->orWhere('middle_name', 'LIKE', "%{$token}%")
                            ->orWhere('last_name', 'LIKE', "%{$token}%")
                            ->orWhere('email', 'LIKE', "%{$token}%")
                            ->orWhere('mobile', 'LIKE', "%{$token}%")
                            ->orWhere('gender', 'LIKE', "%{$token}%");
                    });
                }
            });
        }
    }
    public function review()
    {
        return $this->hasMany(Review::class, 'astro_id', 'id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'astro_services', 'astro_id', 'service_id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'astro_skills', 'astro_id', 'skill_id');
    }

    public function followers()
    {
        return $this->hasMany(AstroFollower::class, 'astro_id', 'id');
    }
    public function getIsFollowingAttribute()
    {
        return $this->followers()->where('user_id', auth('sanctum')->id())->exists();
    }
    public function workingDays()
    {
        return $this->hasMany(AstroWorkingDay::class, 'astro_id', 'id');
    }

    public function astroSuggestion()
    {
        return $this->belongsToMany(User::class, 'astro_lead_suggestions', 'astro_id', 'user_id');
    }

    public function instantConsultationHistories()
    {
        return $this->hasMany(InstantConsultationHistory::class, 'astro_id', 'id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'astro_id', 'id');
    }
}

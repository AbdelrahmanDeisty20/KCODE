<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'phone', 'birth_date', 'quote', 'type', 'image', 'status', 'skin_type_id', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date:Y-m-d',
        ];
    }
    public function setBirthDateAttribute($value)
    {
        if ($value) {
            try {
                $this->attributes['birth_date'] = \Illuminate\Support\Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                $this->attributes['birth_date'] = $value;
            }
        } else {
            $this->attributes['birth_date'] = null;
        }
    }
    public function assessment()
    {
        return $this->hasOne(Assessment::class);
    }
    public function skin_type()
    {
        return $this->belongsTo(SkinType::class);
    }
    public function otps()
    {
        return $this->hasMany(Otp::class);
    }
    public function getImagePathAttribute()
    {
        $value = $this->image;
        if (!$value)
            return null;

        // If it's already a full URL (social login), return it as is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        $path = $value;
        if (!str_starts_with(strtolower($path), 'users/')) {
            $path = 'users/' . $path;
        }

        $base = 'storage/';
        if (!is_link(public_path('storage')) && request() && request()->getHost() !== '127.0.0.1' && request()->getHost() !== 'localhost') {
            $base = 'storage/app/public/';
        }

        return asset($base . $path);
    }
    public function refresh_tokens()
    {
        return $this->hasMany(RefreshToken::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favourite::class);
    }
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function loyaltyLedger()
    {
        return $this->hasMany(LoyaltyPointsLedger::class);
    }
    public function getLoyaltyPointsBalanceAttribute(): int
    {
        return (int) $this->loyaltyLedger()->sum('points');
    }

    /**
     * المستوى الحالي للمستخدم
     */
    public function getLoyaltyLevelAttribute(): ?LoyaltyLevel
    {
        return LoyaltyLevel::forPoints($this->loyalty_points_balance);
    }

    /**
     * المستوى التالي (الهدف القادم)
     */
    public function getNextLoyaltyLevelAttribute(): ?LoyaltyLevel
    {
        $currentLevel = $this->loyalty_level;
        if (!$currentLevel) {
            return LoyaltyLevel::active()->orderBy('min_points', 'asc')->first();
        }
        return LoyaltyLevel::nextAfter($currentLevel->min_points);
    }

    /**
     * نسبة التقدم نحو المستوى التالي (0-100)
     */
    public function getLoyaltyProgressAttribute(): float
    {
        $current = $this->loyalty_level;
        $next    = $this->next_loyalty_level;

        if (!$next) {
            return 100.0; // وصل لأعلى مستوى
        }

        $start   = $current ? $current->min_points : 0;
        $end     = $next->min_points;
        $points  = $this->loyalty_points_balance;

        if ($end <= $start) return 0.0;

        return min(100, round((($points - $start) / ($end - $start)) * 100, 2));
    }
}

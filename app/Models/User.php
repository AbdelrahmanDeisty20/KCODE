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

#[Fillable(['name', 'email', 'password', 'phone', 'type', 'image', 'status', 'skin_type_id', 'email_verified_at'])]
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
        ];
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
        return asset('storage/' . $path);
    }
    public function refresh_tokens()
    {
        return $this->hasMany(RefreshToken::class);
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}

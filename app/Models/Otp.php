<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Otp extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'expires_at',
        'email',
        'type',
        'reset_token',
        'verified_at'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

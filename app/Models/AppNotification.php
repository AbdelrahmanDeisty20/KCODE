<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title_ar',
        'title_en',
        'message_ar',
        'message_en',
        'type',
        'target_audience',
        'channels',
        'action_url',
        'data',
        'is_read',
    ];

    protected $casts = [
        'channels' => 'array',
        'data'     => 'array',
        'is_read'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

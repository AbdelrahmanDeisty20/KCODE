<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotificationUserStatus extends Model
{
    protected $fillable = [
        'user_id',
        'user_fcm_token_id',
        'app_notification_id',
        'is_read',
        'is_deleted',
        'read_at',
        'deleted_at',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'is_deleted' => 'boolean',
        'read_at'    => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userFcmToken()
    {
        return $this->belongsTo(UserFcmToken::class, 'user_fcm_token_id');
    }

    public function notification()
    {
        return $this->belongsTo(AppNotification::class, 'app_notification_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    protected $table = 'newsletter_subscriptions';

    protected $fillable = [
        'email',
        'device_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizEngineConfig extends Model
{
    protected $fillable = [
        'key',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}

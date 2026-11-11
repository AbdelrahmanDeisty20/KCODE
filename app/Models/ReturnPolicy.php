<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnPolicy extends Model
{
    protected $table = 'return_policies';

    protected $fillable = [
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'content_ar' => 'array',
        'content_en' => 'array',
    ];

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getContentAttribute(): array|string|null
    {
        return app()->getLocale() === 'ar' ? $this->content_ar : $this->content_en;
    }
}

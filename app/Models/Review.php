<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'comment',
        'tag_ar',
        'tag_en',
        'featured',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];

    public function getTagAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->tag_ar : $this->tag_en;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

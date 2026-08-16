<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    use HasFactory;

    protected $table = 'blog_categories';

    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'image',
    ];

    /**
     * Accessor for image matching Product model storage pattern.
     */
    public function getImageAttribute($value): ?string
    {
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;

        $path = ltrim(preg_replace('/^storage\//', '', $value), '/');
        if (!str_starts_with($path, 'blog-categories/')) {
            $path = 'blog-categories/' . $path;
        }

        if (request() && request()->is('admin*')) {
            return $path;
        }

        return asset('storage/' . $path);
    }

    /**
     * Accessor for locale-based name.
     */
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->name_ar ?? $this->name_en) : ($this->name_en ?? $this->name_ar);
    }

    /**
     * Get all of the blogs for the category.
     */
    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}

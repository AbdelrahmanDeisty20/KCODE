<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'blogs';

    protected $fillable = [
        'name_ar',
        'name_en',
        'name',
        'title_ar',
        'title_en',
        'slug',
        'excerpt_ar',
        'excerpt_en',
        'content_ar',
        'content_en',
        'featured_image',
        'category_id',
        'author_id',
        'status',
        'is_featured',
        'reading_time',
        'views',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'reading_time' => 'integer',
        'views' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Accessor for featured_image matching Product model storage pattern.
     */
    public function getFeaturedImageAttribute($value): ?string
    {
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;

        $path = ltrim(preg_replace('/^storage\//', '', $value), '/');
        if (!str_starts_with($path, 'blogs/')) {
            $path = 'blogs/' . $path;
        }

        if (request() && request()->is('admin*')) {
            return $path;
        }

        return asset('storage/' . $path);
    }

    /**
     * Accessor for locale-based name.
     */
    public function getNameAttribute(): ?string
    {
        return app()->getLocale() === 'ar' 
            ? ($this->name_ar ?? $this->name_en ?? $this->title_ar ?? $this->title_en) 
            : ($this->name_en ?? $this->name_ar ?? $this->title_en ?? $this->title_ar);
    }

    /**
     * Accessor for locale-based title.
     */
    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->title_ar ?? $this->title_en) : ($this->title_en ?? $this->title_ar);
    }

    /**
     * Accessor for locale-based excerpt.
     */
    public function getExcerptAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->excerpt_ar ?? $this->excerpt_en) : ($this->excerpt_en ?? $this->excerpt_ar);
    }

    /**
     * Accessor for locale-based content.
     */
    public function getContentAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->content_ar ?? $this->content_en) : ($this->content_en ?? $this->content_ar);
    }

    /**
     * Get the category that owns the blog.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Get the author that owns the blog.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * The tags that belong to the blog.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_tag', 'blog_id', 'tag_id');
    }

    /**
     * Get the SEO record associated with the blog.
     */
    public function seo(): HasOne
    {
        return $this->hasOne(BlogSeo::class, 'blog_id');
    }

    /**
     * Scope a query to only include published blogs.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include featured blogs.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order by popular (most views).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('views');
    }
}

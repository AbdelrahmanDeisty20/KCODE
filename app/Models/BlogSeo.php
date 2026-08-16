<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogSeo extends Model
{
    use HasFactory;

    protected $table = 'blog_seo';

    protected $fillable = [
        'blog_id',
        'meta_title_ar',
        'meta_title_en',
        'meta_description_ar',
        'meta_description_en',
        'meta_keywords_ar',
        'meta_keywords_en',
        'canonical_url',
        'og_title_ar',
        'og_title_en',
        'og_description_ar',
        'og_description_en',
        'og_image',
    ];

    /**
     * Accessor for locale-based meta_title.
     */
    public function getMetaTitleAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->meta_title_ar ?? $this->meta_title_en) : ($this->meta_title_en ?? $this->meta_title_ar);
    }

    /**
     * Accessor for locale-based meta_description.
     */
    public function getMetaDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->meta_description_ar ?? $this->meta_description_en) : ($this->meta_description_en ?? $this->meta_description_ar);
    }

    /**
     * Accessor for locale-based meta_keywords.
     */
    public function getMetaKeywordsAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->meta_keywords_ar ?? $this->meta_keywords_en) : ($this->meta_keywords_en ?? $this->meta_keywords_ar);
    }

    /**
     * Accessor for locale-based og_title.
     */
    public function getOgTitleAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->og_title_ar ?? $this->og_title_en) : ($this->og_title_en ?? $this->og_title_ar);
    }

    /**
     * Accessor for locale-based og_description.
     */
    public function getOgDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->og_description_ar ?? $this->og_description_en) : ($this->og_description_en ?? $this->og_description_ar);
    }

    /**
     * Accessor for og_image matching Product model storage pattern.
     */
    public function getOgImageAttribute($value): ?string
    {
        if (!$value) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;

        $path = ltrim(preg_replace('/^(storage\/)?(app\/public\/)?/', '', $value), '/');

        return asset('storage/' . $path);
    }

    /**
     * Get the blog that owns the SEO record.
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }
}

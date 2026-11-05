<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresetRoutine extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title_ar',
        'title_en',
        'subtitle_ar',
        'subtitle_en',
        'description_ar',
        'description_en',
        'card_note_ar',
        'card_note_en',
        'audience_ar',
        'audience_en',
        'timing_json',
        'cover_image',
        'cover_json',
        'badge_ar',
        'badge_en',
        'skin_type_ar',
        'skin_type_en',
        'goal_ar',
        'goal_en',
        'skin_type_id',
        'goal_id',
        'is_for_men',
        'status',
        'benefits_json',
        'what_it_offers_json',
        'usage_guide_json',
        'how_to_use_ar',
        'how_to_use_en',
        'complementary_products_json',
    ];

    protected $casts = [
        'is_for_men' => 'boolean',
        'benefits_json' => 'array',
        'what_it_offers_json' => 'array',
        'usage_guide_json' => 'array',
        'timing_json' => 'array',
        'cover_json' => 'array',
        'complementary_products_json' => 'array',
    ];

    protected $appends = [
        'cover_image_url',
        'cover',
    ];

    public function getCoverImageUrlAttribute(): ?string
    {
        if (!$this->cover_image) {
            return null;
        }

        if (filter_var($this->cover_image, FILTER_VALIDATE_URL)) {
            return $this->cover_image;
        }

        return url('storage/' . ltrim($this->cover_image, '/'));
    }

    public function getCoverAttribute(): ?array
    {
        $data = $this->cover_json;
        if (!$data || !is_array($data)) {
            if ($this->cover_image_url) {
                return [
                    'src' => $this->cover_image_url,
                    'srcset' => $this->cover_image_url,
                    'alt' => $this->title_ar,
                    'width' => 1024,
                    'height' => 1536,
                ];
            }
            return null;
        }

        $baseUrl = url('storage/routines');

        // Transform src
        $src = $data['src'] ?? null;
        if ($src && !filter_var($src, FILTER_VALIDATE_URL)) {
            $srcClean = preg_replace('/^KCODE_ASSET_/', '', $src);
            $srcClean = preg_replace('/\.webp$/', '', $srcClean) . '.webp';
            $src = $baseUrl . '/' . $srcClean;
        }

        // Transform srcset
        $srcset = $data['srcset'] ?? null;
        if ($srcset) {
            $srcset = preg_replace_callback('/KCODE_ASSET_([a-f0-9]+)/', function ($matches) use ($baseUrl) {
                return $baseUrl . '/' . $matches[1] . '.webp';
            }, $srcset);
        }

        return [
            'src' => $src ?: $this->cover_image_url,
            'srcset' => $srcset ?: ($src ?: $this->cover_image_url),
            'alt' => $data['alt'] ?? ($this->title_ar ?? ''),
            'alt_en' => $data['alt_en'] ?? ($this->title_en ?? ''),
            'width' => $data['width'] ?? 1024,
            'height' => $data['height'] ?? 1536,
        ];
    }

    public function scopeForMen($query)
    {
        return $query->where('is_for_men', true);
    }

    public function scopeForWomen($query)
    {
        return $query->where('is_for_men', false);
    }

    public function items()
    {
        return $this->hasMany(PresetRoutineProduct::class, 'preset_routine_id')->orderBy('display_order', 'asc');
    }

    public function skinType()
    {
        return $this->belongsTo(SkinType::class);
    }

    public function goal()
    {
        return $this->belongsTo(RoutineGoal::class, 'goal_id');
    }
}

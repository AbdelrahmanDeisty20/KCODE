const fs = require('fs');

let code = fs.readFileSync('app/Models/Product.php', 'utf8');

const startMarker = '    public function detectRoutineStepNumber(): int';
const endMarker = '    public function images()';
const sIdx = code.indexOf(startMarker);
const eIdx = code.indexOf(endMarker);

if (sIdx !== -1 && eIdx !== -1) {
    code = code.slice(0, sIdx) + code.slice(eIdx);
    console.log('Successfully cut presentation accessors from Product.php');
} else {
    console.error('Could not find slice markers', { sIdx, eIdx });
    process.exit(1);
}

const oldRating = `    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1) ?? 0.0;
    }

    public function getNumReviewsAttribute()
    {
        return $this->reviews()->count();
    }`;

const newRating = `    public function getAverageRatingAttribute()
    {
        if (array_key_exists('reviews_avg_rating', $this->attributes) && $this->attributes['reviews_avg_rating'] !== null) {
            return round((float) $this->attributes['reviews_avg_rating'], 1);
        }

        if ($this->relationLoaded('reviews')) {
            $avg = $this->reviews->avg('rating');
            return $avg !== null ? round((float) $avg, 1) : 0.0;
        }

        return round((float) ($this->reviews()->avg('rating') ?? 0.0), 1);
    }

    public function getNumReviewsAttribute()
    {
        if (array_key_exists('reviews_count', $this->attributes)) {
            return (int) $this->attributes['reviews_count'];
        }

        if ($this->relationLoaded('reviews')) {
            return $this->reviews->count();
        }

        return (int) $this->reviews()->count();
    }`;

const normalizedCode = code.replace(/\r\n/g, '\n');
const normalizedOld = oldRating.replace(/\r\n/g, '\n');
const normalizedNew = newRating.replace(/\r\n/g, '\n');

if (!normalizedCode.includes(normalizedOld)) {
    console.error('Could not find oldRating');
    process.exit(1);
}

const replaced = normalizedCode.replace(normalizedOld, normalizedNew);
fs.writeFileSync('app/Models/Product.php', replaced, 'utf8');
console.log('Product.php refactored successfully! Total lines:', replaced.split('\n').length);

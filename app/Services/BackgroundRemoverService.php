<?php

namespace App\Services;

use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackgroundRemoverService
{
    /**
     * Remove background from an image stored in Laravel Storage (or URL) using Remove.bg API.
     * Saves the result as transparent PNG under target directory with a UUID filename,
     * deletes the old file if stored locally, and returns the new relative path.
     *
     * @param string $imagePath Relative storage path (e.g. 'products/item.jpg') or full URL
     * @param string $disk Storage disk ('public' by default)
     * @param string|null $targetDirectory Optional directory to save output in (defaults to dirname or 'products')
     * @return string New relative file path (e.g. 'products/uuid.png')
     * @throws \Exception
     */
    public function removeFromStorage(string $imagePath, string $disk = 'public', ?string $targetDirectory = null): string
    {
        $apiKey = config('services.remove_bg.key');

        if (empty($apiKey)) {
            throw new \Exception('مفتاح Remove.bg API Key غير معرّف. يرجى إضافة REMOVE_BG_API_KEY في ملف .env أولاً.');
        }

        $imagePath = trim($imagePath);
        if (empty($imagePath)) {
            throw new \InvalidArgumentException('مسار الصورة فارغ ولا يمكن معالجته.');
        }

        $isUrl = filter_var($imagePath, FILTER_VALIDATE_URL) !== false;
        $cleanPath = null;
        $isLocalFile = false;
        $fileBytes = null;
        $filename = 'image.jpg';

        if ($isUrl) {
            // Fetch remote image bytes
            $download = Http::timeout(30)->get($imagePath);
            if (!$download->successful()) {
                throw new \Exception("تعذر تحميل الصورة من الرابط الخارجي: {$imagePath}");
            }
            $fileBytes = $download->body();
            $urlPath = parse_url($imagePath, PHP_URL_PATH);
            $filename = $urlPath ? basename($urlPath) : 'image.jpg';
        } else {
            // Clean local storage path
            $cleanPath = ltrim(preg_replace('/^(storage\/)?(app\/public\/)?/', '', $imagePath), '/');

            if (!Storage::disk($disk)->exists($cleanPath)) {
                throw new \Exception("ملف الصورة غير موجود في التخزين: {$cleanPath}");
            }

            $fileBytes = Storage::disk($disk)->get($cleanPath);
            $filename = basename($cleanPath);
            $isLocalFile = true;
        }

        // Determine destination folder
        if ($targetDirectory) {
            $dir = trim($targetDirectory, '/');
        } elseif ($cleanPath && dirname($cleanPath) !== '.' && dirname($cleanPath) !== '') {
            $dir = dirname($cleanPath);
        } else {
            $dir = 'products';
        }

        // Call Remove.bg API
        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $apiKey,
            ])
            ->timeout(60)
            ->asMultipart()
            ->attach('image_file', $fileBytes, $filename)
            ->post('https://api.remove.bg/v1.0/removebg', [
                'size' => 'auto',
            ]);
        } catch (\Throwable $e) {
            Log::error('Remove.bg connection error: ' . $e->getMessage());
            throw new \Exception('فشل الاتصال بخوادم Remove.bg: ' . $e->getMessage());
        }

        if (!$response->successful()) {
            $errorTitle = $response->json('errors.0.title');
            $errorDetail = $response->json('errors.0.detail');
            $errorMessage = $errorDetail ?: ($errorTitle ?: ($response->json('error') ?: 'كود الخطأ: ' . $response->status()));
            Log::error('Remove.bg API failed: ' . $response->body());
            throw new \Exception("خطأ من Remove.bg: {$errorMessage}");
        }

        // Generate unique UUID PNG filename
        $newFilename = Str::uuid() . '.png';
        $newPath = "{$dir}/{$newFilename}";

        // Save transparent PNG to storage
        Storage::disk($disk)->put($newPath, $response->body());

        // Delete old local file to save storage space
        if ($isLocalFile && $cleanPath && Storage::disk($disk)->exists($cleanPath) && $cleanPath !== $newPath) {
            try {
                Storage::disk($disk)->delete($cleanPath);
            } catch (\Throwable $e) {
                Log::warning("Could not delete old image {$cleanPath}: " . $e->getMessage());
            }
        }

        return $newPath;
    }

    /**
     * Remove background for an Eloquent model's single image field and save the model.
     *
     * @param Model $record
     * @param string $imageField (e.g. 'image', 'featured_image')
     * @param string $disk
     * @return string New image path
     */
    public function removeForModel(Model $record, string $imageField = 'image', string $disk = 'public'): string
    {
        $currentValue = $record->{$imageField};
        if (empty($currentValue)) {
            throw new \Exception('لا توجد صورة مسجلة لهذا العنصر لمعالجتها.');
        }

        $newPath = $this->removeFromStorage($currentValue, $disk);

        $record->update([
            $imageField => $newPath,
        ]);

        return $newPath;
    }

    /**
     * Remove background for a ProductImage gallery record and update it.
     */
    public function removeForGalleryImage(ProductImage $productImage, string $disk = 'public'): string
    {
        $currentValue = $productImage->images;
        if (empty($currentValue)) {
            throw new \Exception('لا توجد صورة فرعية لهذا السجل.');
        }

        $newPath = $this->removeFromStorage($currentValue, $disk, 'product_images');

        $productImage->update([
            'images' => $newPath,
        ]);

        return $newPath;
    }

    /**
     * Remove background for selected indices of a JSON array gallery column on a model.
     *
     * @param Model $record
     * @param string $arrayField Column name holding JSON array (e.g. 'images')
     * @param array $selectedIndices Array of integer indices to process (e.g. [0, 2])
     * @param string $disk
     * @return array Updated images array
     */
    public function removeForJsonArray(Model $record, string $arrayField, array $selectedIndices = [], string $disk = 'public'): array
    {
        $images = $record->{$arrayField};

        if (is_string($images)) {
            $images = json_decode($images, true) ?: [];
        }

        if (!is_array($images) || empty($images)) {
            throw new \Exception('لا توجد صور في المعرض لمعالجتها.');
        }

        // If no indices specified, process all
        $targetIndices = !empty($selectedIndices) ? $selectedIndices : array_keys($images);

        foreach ($targetIndices as $idx) {
            if (isset($images[$idx]) && !empty($images[$idx])) {
                $images[$idx] = $this->removeFromStorage($images[$idx], $disk);
            }
        }

        $record->update([
            $arrayField => $images,
        ]);

        return $images;
    }
}

<?php

namespace App\Helpers;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Database\Seeders\ImageDownloader;

class FilamentImageHelper
{
    /**
     * Create a table column displaying the image filename (e.g. toner.png or No Image).
     */
    public static function makeImageFilenameColumn(
        string $imageField = 'image',
        string $labelAr = 'اسم الصورة',
        string $labelEn = 'Image Filename'
    ): TextColumn {
        $isEn = app()->getLocale() === 'en';

        return TextColumn::make($imageField . '_filename')
            ->label($isEn ? $labelEn : $labelAr)
            ->getStateUsing(function ($record) use ($imageField, $isEn) {
                $value = $record->{$imageField} ?? null;
                if (!$value) {
                    return $isEn ? 'No Image' : 'لا توجد صورة';
                }
                $path = parse_url($value, PHP_URL_PATH);
                return basename($path);
            })
            ->badge()
            ->color(fn ($state) => in_array($state, ['No Image', 'لا توجد صورة']) ? 'gray' : 'info');
    }

    /**
     * Create a row action button for quick image upload OR Image URL pasting in a modal.
     */
    public static function makeUpdateImageAction(
        string $directory,
        string $imageField = 'image',
        string $labelAr = 'تعديل الصورة',
        string $labelEn = 'Update Image'
    ): Action {
        $isEn = app()->getLocale() === 'en';

        return Action::make('update_' . $imageField)
            ->label($isEn ? $labelEn : $labelAr)
            ->icon('heroicon-o-camera')
            ->color('info')
            ->modalHeading(fn ($record) => ($isEn ? 'Update Image: ' : 'تحديث صورة: ') . ($record->name_ar ?? $record->name_en ?? $record->title_ar ?? $record->title_en ?? $record->name ?? ''))
            ->modalSubmitActionLabel($isEn ? 'Save Image' : 'حفظ الصورة')
            ->schema([
                FileUpload::make($imageField)
                    ->label($isEn ? '1. Upload Image File' : '1. اختر/ارفع ملف الصورة من الجهاز')
                    ->image()
                    ->directory($directory)
                    ->nullable()
                    ->formatStateUsing(fn ($state) => $state ? (str_starts_with($state, $directory . '/') ? $state : $directory . '/' . ltrim($state, '/')) : null),

                TextInput::make($imageField . '_url')
                    ->label($isEn ? '2. OR Paste Direct Image URL' : '2. أو أدخل رابط الصورة المباشر (URL)')
                    ->placeholder('https://example.com/image.jpg')
                    ->url()
                    ->nullable(),
            ])
            ->action(function ($record, array $data) use ($directory, $imageField, $isEn) {
                $urlInput = $data[$imageField . '_url'] ?? null;
                $fileInput = $data[$imageField] ?? null;

                $finalPath = null;

                if (!empty($urlInput)) {
                    $slug = Str::slug($record->name_en ?: $record->name_ar) ?: 'item-' . $record->id;
                    $filename = ImageDownloader::downloadAndSave($urlInput, $directory, "{$slug}.jpg");
                    $finalPath = "{$directory}/{$filename}";

                    // Copy to downloads
                    $src = storage_path("app/public/{$finalPath}");
                    $dst = "C:/Users/Dell/Downloads/kcode-images/{$finalPath}";
                    if (File::exists($src)) {
                        File::ensureDirectoryExists(dirname($dst));
                        File::copy($src, $dst);
                    }
                } elseif (!empty($fileInput)) {
                    $finalPath = $fileInput;
                }

                if ($finalPath !== null) {
                    $record->update([
                        $imageField => $finalPath,
                    ]);

                    Notification::make()
                        ->title($isEn ? 'Image updated successfully!' : 'تم تحديث الصورة بنجاح!')
                        ->success()
                        ->send();
                }
            });
    }

    /**
     * Create a header action for uploading multiple images / pasting image URLs with both Auto-Matching & Manual Mapping options.
     */
    public static function makeBulkUploadHeaderAction(
        string $directory,
        string $modelClass,
        string $imageField = 'image',
        string $labelAr = 'رفع صور / روابط / إسناد يدوي',
        string $labelEn = 'Bulk Upload & Map Images'
    ): Action {
        $isEn = app()->getLocale() === 'en';

        return Action::make('bulk_upload_' . $directory . '_images')
            ->label($isEn ? $labelEn : $labelAr)
            ->icon('heroicon-o-cloud-arrow-up')
            ->color('success')
            ->modalHeading($isEn ? 'Bulk Upload & Image Link Assignment' : 'رفع وإسناد الصور بالملفات أو الروابط (URLs)')
            ->modalDescription($isEn
                ? 'Option 1: Upload an image file OR paste a direct Image URL link for any item. Option 2: Bulk upload files for auto-matching.'
                : 'الخيار 1: إسناد ملف صورة أو إضافة رابط صورة مباشر (URL) لأي عنصر. الخيار 2: رفع ملفات متعددة لمطابقتها تلقائياً.')
            ->modalSubmitActionLabel($isEn ? 'Save & Assign Images' : 'حفظ وإسناد الصور')
            ->schema([
                Tabs::make('upload_mode_tabs')
                    ->tabs([
                        Tab::make('manual_mapping')
                            ->label($isEn ? '🎯 Manual Mapping (Files & Links)' : '🎯 التخصيص اليدوي (ملفات أو روابط URLs)')
                            ->schema([
                                Placeholder::make('manual_desc')
                                    ->hiddenLabel()
                                    ->content($isEn
                                        ? 'Upload any image file OR paste a direct Image URL (e.g. Unsplash/Google image link) and select the target item.'
                                        : 'ارفع ملف صورة أو انسخ رابط صورة مباشر (Image URL) من الإنترنت واختر العنصر المراد إسناد الصورة له بنقرة واحدة.'),
                                Repeater::make('manual_mappings')
                                    ->label($isEn ? 'Image to Record Mappings' : 'قائمة إسناد الصور والروابط للعناصر')
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label($isEn ? 'Image File (Upload)' : 'ملف الصورة (رفع)')
                                            ->image()
                                            ->directory($directory)
                                            ->nullable(),
                                        TextInput::make('image_url')
                                            ->label($isEn ? 'OR Image URL (Link)' : 'أو رابط الصورة (URL)')
                                            ->placeholder('https://images.unsplash.com/photo-1556228720...')
                                            ->url()
                                            ->nullable(),
                                        Select::make('record_id')
                                            ->label($isEn ? 'Target Record' : 'العنصر المستهدف')
                                            ->options(function () use ($modelClass) {
                                                return $modelClass::all()->pluck(
                                                    app()->getLocale() === 'en' ? 'name_en' : 'name_ar',
                                                    'id'
                                                )->map(function ($name, $id) use ($modelClass) {
                                                    if (!$name) {
                                                        $record = $modelClass::find($id);
                                                        $name = $record->name_ar ?? $record->name_en ?? $record->title_ar ?? $record->title_en ?? "Record #{$id}";
                                                    }
                                                    return "{$name} (ID: {$id})";
                                                })->toArray();
                                            })
                                            ->searchable()
                                            ->required(),
                                    ])
                                    ->columns(3)
                                    ->addActionLabel($isEn ? '+ Add Another Image or Link' : '+ إضافة إسناد صورة أو رابط لعنصر آخر')
                                    ->collapsible()
                                    ->defaultItems(1),
                            ]),

                        Tab::make('auto_match')
                            ->label($isEn ? '⚡ Auto-Match by Filename' : '⚡ مطابقة تلقائية باسم الملف')
                            ->schema([
                                Placeholder::make('auto_desc')
                                    ->hiddenLabel()
                                    ->content($isEn
                                        ? 'Upload files named after items (e.g. toner.png, cleanser.jpg, SKU, or ID).'
                                        : 'قم برفع مجموعة صور مسمّاة بأسماء العناصر (مثل: toner.png أو غسول.jpg أو الكود). سيقوم النظام بمطابقتها تلقائياً.'),
                                FileUpload::make('images')
                                    ->label($isEn ? 'Select Image Files' : 'اختر ملفات الصور')
                                    ->image()
                                    ->multiple()
                                    ->directory($directory)
                                    ->storeFileNamesIn('original_filenames')
                                    ->nullable(),
                            ]),
                    ]),
            ])
            ->action(function (array $data) use ($modelClass, $directory, $imageField, $isEn) {
                $matchedCount = 0;
                $matchedDetails = [];

                // 1. Process Manual Mappings (Files & URLs)
                $manualMappings = $data['manual_mappings'] ?? [];
                foreach ($manualMappings as $mapping) {
                    $recordId = $mapping['record_id'] ?? null;
                    $filePath = $mapping['image'] ?? null;
                    $imageUrl = $mapping['image_url'] ?? null;

                    if ($recordId && (!empty($filePath) || !empty($imageUrl))) {
                        $record = $modelClass::find($recordId);
                        if ($record) {
                            $finalPath = null;

                            if (!empty($imageUrl)) {
                                $slug = Str::slug($record->name_en ?: $record->name_ar) ?: 'item-' . $record->id;
                                $filename = ImageDownloader::downloadAndSave($imageUrl, $directory, "{$slug}.jpg");
                                $finalPath = "{$directory}/{$filename}";

                                // Copy to downloads
                                $src = storage_path("app/public/{$finalPath}");
                                $dst = "C:/Users/Dell/Downloads/kcode-images/{$finalPath}";
                                if (File::exists($src)) {
                                    File::ensureDirectoryExists(dirname($dst));
                                    File::copy($src, $dst);
                                }
                            } else {
                                $finalPath = $filePath;
                            }

                            if ($finalPath) {
                                $record->update([
                                    $imageField => $finalPath,
                                ]);
                                $matchedCount++;
                                $recordName = $record->name_ar ?? $record->name_en ?? $record->title_ar ?? $record->id;
                                $label = !empty($imageUrl) ? 'رابط URL' : basename($finalPath);
                                $matchedDetails[] = "🎯 [يدوي] {$label} ➔ {$recordName}";
                            }
                        }
                    }
                }

                // 2. Process Auto-Matched Bulk Images
                $images = $data['images'] ?? [];
                if (!empty($images)) {
                    foreach ($images as $key => $filePath) {
                        $originalName = $data['original_filenames'][$key] ?? basename($filePath);
                        $cleanName = pathinfo($originalName, PATHINFO_FILENAME);
                        $cleanNameNorm = trim(preg_replace('/[_\-]+/', ' ', $cleanName));

                        $record = null;

                        if (is_numeric($cleanNameNorm)) {
                            $record = $modelClass::find((int) $cleanNameNorm);
                        }

                        if (!$record) {
                            $query = $modelClass::query();
                            $query->where(function ($q) use ($modelClass, $cleanNameNorm, $cleanName) {
                                $table = (new $modelClass)->getTable();
                                if (Schema::hasColumn($table, 'sku')) {
                                    $q->orWhere('sku', 'LIKE', "%{$cleanName}%")
                                      ->orWhere('sku', 'LIKE', "%{$cleanNameNorm}%");
                                }
                                if (Schema::hasColumn($table, 'name_ar')) {
                                    $q->orWhere('name_ar', 'LIKE', "%{$cleanNameNorm}%");
                                }
                                if (Schema::hasColumn($table, 'name_en')) {
                                    $q->orWhere('name_en', 'LIKE', "%{$cleanNameNorm}%");
                                }
                                if (Schema::hasColumn($table, 'title_ar')) {
                                    $q->orWhere('title_ar', 'LIKE', "%{$cleanNameNorm}%");
                                }
                                if (Schema::hasColumn($table, 'title_en')) {
                                    $q->orWhere('title_en', 'LIKE', "%{$cleanNameNorm}%");
                                }
                            });
                            $record = $query->first();
                        }

                        if ($record) {
                            $record->update([
                                $imageField => $filePath,
                            ]);
                            $matchedCount++;
                            $recordName = $record->name_ar ?? $record->name_en ?? $record->title_ar ?? $record->id;
                            $matchedDetails[] = "⚡ [تلقائي] {$originalName} ➔ {$recordName}";
                        }
                    }
                }

                if ($matchedCount > 0) {
                    Notification::make()
                        ->title($isEn
                            ? "Successfully saved and assigned {$matchedCount} images!"
                            : "تم حفظ وإسناد صور {$matchedCount} عنصر بنجاح!")
                        ->body(implode('<br>', array_slice($matchedDetails, 0, 8)))
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title($isEn ? 'No images were processed or matched.' : 'لم يتم معالجة أو إسناد صور.')
                        ->warning()
                        ->send();
                }
            });
    }
}

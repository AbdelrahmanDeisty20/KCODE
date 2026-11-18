<?php

namespace App\Helpers;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
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
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Database\Seeders\ImageDownloader;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Services\BackgroundRemoverService;

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

    /**
     * Create a row action button to remove background using Remove.bg API.
     */
    public static function makeRemoveBackgroundAction(
        string $imageField = 'image',
        string $disk = 'public'
    ): Action {
        $isEn = app()->getLocale() === 'en';

        return Action::make('removeBackground')
            ->label($isEn ? 'Remove Background' : 'إزالة الخلفية')
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading(fn ($record) => $isEn ? 'Remove Image Background' : 'إزالة خلفية صورة ' . ($record instanceof Product ? 'المنتج' : 'العنصر'))
            ->modalDescription($isEn
                ? 'The current image background will be removed and replaced with a transparent PNG. Are you sure?'
                : 'سيتم قص خلفية الصورة الحالية واستبدالها بصورة شفافة PNG، هل أنت متأكد؟')
            ->modalSubmitActionLabel($isEn ? 'Remove Background' : 'قص وإزالة الخلفية')
            ->schema(function ($record) use ($imageField, $disk, $isEn) {
                $schema = [];

                if (!$record) {
                    return $schema;
                }

                // If Product model with possible gallery sub-images
                if ($record instanceof Product) {
                    $hasSubImages = method_exists($record, 'images') && $record->images()->exists();
                    $hasJsonImages = is_array($record->images) && !empty($record->images);

                    // Main image preview + toggle
                    if (!empty($record->{$imageField})) {
                        $mainSrc = filter_var($record->{$imageField}, FILTER_VALIDATE_URL)
                            ? $record->{$imageField}
                            : \Illuminate\Support\Facades\Storage::disk($disk)->url(ltrim(preg_replace('/^(storage\/)?(app\/public\/)?/', '', $record->{$imageField}), '/'));

                        $schema[] = Placeholder::make('main_preview')
                            ->label($isEn ? 'Main Image Preview' : 'معاينة الصورة الرئيسية')
                            ->content(new HtmlString('<div style="margin-bottom:8px;"><img src="' . e($mainSrc) . '" style="max-height: 140px; border-radius: 8px; border: 2px solid #eab308; object-fit: contain; background: repeating-conic-gradient(#eee 0% 25%, white 0% 50%) 50% / 16px 16px;" /></div>'));

                        $schema[] = Checkbox::make('process_main')
                            ->label($isEn ? 'Process Main Image' : 'إزالة خلفية الصورة الرئيسية')
                            ->default(true);
                    }

                    // Gallery sub-images options
                    if ($hasSubImages || $hasJsonImages) {
                        $galleryOptions = [];
                        if ($hasSubImages) {
                            foreach ($record->images as $subImg) {
                                $galleryOptions[$subImg->id] = ($isEn ? 'Sub-Image #' : 'صورة فرعية رقم ') . $subImg->id . ' (' . basename($subImg->images) . ')';
                            }
                        }
                        if ($hasJsonImages) {
                            foreach ($record->images as $idx => $p) {
                                $galleryOptions["json_{$idx}"] = ($isEn ? 'Gallery Image #' : 'صورة المعرض رقم ') . ($idx + 1) . ' (' . basename($p) . ')';
                            }
                        }

                        if (!empty($galleryOptions)) {
                            $schema[] = CheckboxList::make('gallery_images')
                                ->label($isEn ? 'Select Gallery Images to Process' : 'حدد الصور الفرعية من المعرض المراد إزالة خلفيتها')
                                ->options($galleryOptions)
                                ->helperText($isEn
                                    ? 'Leave unselected to only process the main image.'
                                    : 'اختر صور المعرض المراد قصها، أو اتركها فارغة لمعالجة الصورة الرئيسية فقط.');
                        }
                    }
                } else {
                    // For any other model (Category, Brand, Concern, etc.)
                    $currentValue = $record->{$imageField} ?? null;
                    if (!empty($currentValue)) {
                        $src = filter_var($currentValue, FILTER_VALIDATE_URL)
                            ? $currentValue
                            : \Illuminate\Support\Facades\Storage::disk($disk)->url(ltrim(preg_replace('/^(storage\/)?(app\/public\/)?/', '', $currentValue), '/'));

                        $schema[] = Placeholder::make('record_preview')
                            ->label($isEn ? 'Current Image' : 'الصورة الحالية')
                            ->content(new HtmlString('<div style="margin-bottom:8px;"><img src="' . e($src) . '" style="max-height: 140px; border-radius: 8px; border: 2px solid #eab308; object-fit: contain; background: repeating-conic-gradient(#eee 0% 25%, white 0% 50%) 50% / 16px 16px;" /></div>'));
                    }
                }

                return $schema;
            })
            ->action(function ($record, array $data = []) use ($imageField, $disk, $isEn) {
                try {
                    $service = app(BackgroundRemoverService::class);
                    $processed = 0;

                    if ($record instanceof Product) {
                        $processMain = $data['process_main'] ?? true;
                        $gallerySelected = $data['gallery_images'] ?? [];

                        if ($processMain && !empty($record->{$imageField})) {
                            $service->removeForModel($record, $imageField, $disk);
                            $processed++;
                        }

                        if (!empty($gallerySelected)) {
                            foreach ($gallerySelected as $selId) {
                                if (str_starts_with($selId, 'json_')) {
                                    $idx = (int) str_replace('json_', '', $selId);
                                    $service->removeForJsonArray($record, 'images', [$idx], $disk);
                                    $processed++;
                                } else {
                                    $subImg = ProductImage::find($selId);
                                    if ($subImg) {
                                        $service->removeForGalleryImage($subImg, $disk);
                                        $processed++;
                                    }
                                }
                            }
                        }

                        // If neither was selected or form submitted directly, fallback to main image
                        if ($processed === 0 && !empty($record->{$imageField})) {
                            $service->removeForModel($record, $imageField, $disk);
                            $processed++;
                        }
                    } else {
                        $service->removeForModel($record, $imageField, $disk);
                        $processed++;
                    }

                    Notification::make()
                        ->title($isEn ? 'Background removed successfully' : 'تمت إزالة الخلفية بنجاح')
                        ->body($isEn
                            ? "Processed {$processed} image(s) and converted to transparent PNG."
                            : "تم قص الخلفية بنجاح وحفظ الصورة بصيغة PNG شفافة.")
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title($isEn ? 'Failed to remove background' : 'فشلت إزالة الخلفية')
                        ->body($e->getMessage())
                        ->danger()
                        ->persistent()
                        ->send();
                }
            });
    }

    /**
     * Create a bulk action to remove backgrounds from all selected records in any table.
     */
    public static function makeRemoveBackgroundBulkAction(
        string $imageField = 'image',
        string $disk = 'public'
    ): BulkAction {
        $isEn = app()->getLocale() === 'en';

        return BulkAction::make('removeBackgroundBulk')
            ->label($isEn ? 'Remove Background (Selected)' : 'إزالة خلفيات المحدد')
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading($isEn ? 'Remove Backgrounds for Selected Items' : 'إزالة خلفيات العناصر المحددة')
            ->modalDescription($isEn
                ? 'Remove.bg will be called to process and convert images for all selected records to transparent PNGs. Are you sure?'
                : 'سيتم استدعاء Remove.bg وقص خلفيات الصور لجميع العناصر المحددة وتحديثها بصيغة PNG شفافة. هل أنت متأكد؟')
            ->modalSubmitActionLabel($isEn ? 'Confirm & Process' : 'تأكيد وقص الخلفيات')
            ->action(function (\Illuminate\Support\Collection $records) use ($imageField, $disk, $isEn) {
                $service = app(BackgroundRemoverService::class);
                $successCount = 0;
                $failCount = 0;
                $errors = [];

                foreach ($records as $record) {
                    try {
                        $field = property_exists($record, 'featured_image') || isset($record->featured_image) ? 'featured_image' : $imageField;
                        if (!empty($record->{$field})) {
                            $service->removeForModel($record, $field, $disk);
                            $successCount++;
                        }
                    } catch (\Throwable $e) {
                        $failCount++;
                        $name = $record->name_ar ?? $record->name_en ?? $record->title_ar ?? $record->id;
                        $errors[] = "{$name}: " . $e->getMessage();
                    }
                }

                if ($successCount > 0) {
                    Notification::make()
                        ->title($isEn ? 'Backgrounds removed successfully' : 'تمت إزالة الخلفيات بنجاح')
                        ->body($isEn
                            ? "Successfully processed {$successCount} record(s)." . ($failCount > 0 ? " ({$failCount} failed)" : '')
                            : "تم بنجاح معالجة وتحديث {$successCount} عنصر." . ($failCount > 0 ? " (وفشل {$failCount})" : ''))
                        ->success()
                        ->send();
                }

                if ($failCount > 0) {
                    Notification::make()
                        ->title($isEn ? "Failed to process {$failCount} item(s)" : "تعذر معالجة بعض العناصر ({$failCount})")
                        ->body(implode("\n", array_slice($errors, 0, 3)))
                        ->danger()
                        ->persistent()
                        ->send();
                }
            });
    }

    /**
     * Create a header action to remove backgrounds in bulk by Category or by multi-selecting items.
     */
    public static function makeBulkRemoveBackgroundHeaderAction(
        string $modelClass,
        string $imageField = 'image',
        string $disk = 'public'
    ): Action {
        $isEn = app()->getLocale() === 'en';
        $isProduct = $modelClass === Product::class;

        return Action::make('bulk_remove_background_header')
            ->label($isEn ? 'Bulk Remove Backgrounds' : '⚡ إزالة خلفيات (بالقسم أو التحديد)')
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->modalHeading($isEn ? 'Bulk Remove Image Backgrounds' : 'إزالة خلفيات الصور دفعة واحدة')
            ->modalDescription($isEn
                ? 'Select a category or choose items to remove backgrounds using Remove.bg.'
                : 'حدد قسماً لمعالجة كافة منتجاته دفعة واحدة، أو اختر عناصر معينة لقص وتفريغ خلفيات صورها.')
            ->modalSubmitActionLabel($isEn ? 'Start Processing' : 'بدء معالجة الخلفيات')
            ->schema(function () use ($isProduct, $modelClass, $imageField, $isEn) {
                if ($isProduct) {
                    return [
                        Select::make('scope')
                            ->label($isEn ? 'Selection Scope' : 'نطاق التحديد')
                            ->options([
                                'by_category' => $isEn ? 'By Category (All products in selected category)' : 'حسب القسم (معالجة جميع منتجات قسم كامل)',
                                'by_products' => $isEn ? 'Select Specific Products' : 'تحديد منتجات معينة (اختيار متعدد)',
                                'all'         => $isEn ? 'All Products with Images' : 'جميع المنتجات التي لها صور',
                            ])
                            ->default('by_category')
                            ->reactive(),

                        Select::make('category_id')
                            ->label($isEn ? 'Category' : 'القسم المطلوب')
                            ->options(function () use ($isEn) {
                                return Category::all()->pluck(
                                    $isEn ? 'name_en' : 'name_ar',
                                    'id'
                                )->map(fn ($name, $id) => ($name ?: "Category #{$id}"))->toArray();
                            })
                            ->searchable()
                            ->visible(fn ($get) => ($get('scope') ?? 'by_category') === 'by_category')
                            ->required(fn ($get) => ($get('scope') ?? 'by_category') === 'by_category'),

                        Select::make('product_ids')
                            ->label($isEn ? 'Select Products' : 'اختر المنتجات')
                            ->multiple()
                            ->searchable()
                            ->options(function () use ($isEn) {
                                return Product::whereNotNull('image')->pluck(
                                    $isEn ? 'name_en' : 'name_ar',
                                    'id'
                                )->map(fn ($name, $id) => ($name ?: "Product #{$id}"))->toArray();
                            })
                            ->visible(fn ($get) => $get('scope') === 'by_products')
                            ->required(fn ($get) => $get('scope') === 'by_products'),

                        Checkbox::make('process_sub_images')
                            ->label($isEn ? 'Also process gallery sub-images' : 'معالجة الصور الفرعية في المعرض أيضاً إن وجدت')
                            ->default(false),
                    ];
                }

                return [
                    Select::make('scope')
                        ->label($isEn ? 'Selection Scope' : 'نطاق التحديد')
                        ->options([
                            'by_items' => $isEn ? 'Select Specific Items' : 'تحديد عناصر معينة (اختيار متعدد)',
                            'all'      => $isEn ? 'All Items with Images' : 'جميع العناصر التي لها صور',
                        ])
                        ->default('by_items')
                        ->reactive(),

                    Select::make('item_ids')
                        ->label($isEn ? 'Select Items' : 'اختر العناصر')
                        ->multiple()
                        ->searchable()
                        ->options(function () use ($modelClass, $imageField, $isEn) {
                            return $modelClass::whereNotNull($imageField)->get()->pluck(
                                $isEn ? 'name_en' : 'name_ar',
                                'id'
                            )->map(function ($name, $id) use ($modelClass) {
                                if (!$name) {
                                    $rec = $modelClass::find($id);
                                    $name = $rec->name_ar ?? $rec->name_en ?? $rec->title_ar ?? "Item #{$id}";
                                }
                                return $name;
                            })->toArray();
                        })
                        ->visible(fn ($get) => ($get('scope') ?? 'by_items') === 'by_items')
                        ->required(fn ($get) => ($get('scope') ?? 'by_items') === 'by_items'),
                ];
            })
            ->action(function (array $data) use ($modelClass, $isProduct, $imageField, $disk, $isEn) {
                $service = app(BackgroundRemoverService::class);
                $records = collect();

                if ($isProduct) {
                    $scope = $data['scope'] ?? 'by_category';
                    if ($scope === 'by_category' && !empty($data['category_id'])) {
                        $records = Product::where('category_id', $data['category_id'])
                            ->whereNotNull('image')
                            ->get();
                    } elseif ($scope === 'by_products' && !empty($data['product_ids'])) {
                        $records = Product::whereIn('id', $data['product_ids'])->get();
                    } elseif ($scope === 'all') {
                        $records = Product::whereNotNull('image')->get();
                    }
                } else {
                    $scope = $data['scope'] ?? 'by_items';
                    if ($scope === 'by_items' && !empty($data['item_ids'])) {
                        $records = $modelClass::whereIn('id', $data['item_ids'])->get();
                    } elseif ($scope === 'all') {
                        $records = $modelClass::whereNotNull($imageField)->get();
                    }
                }

                if ($records->isEmpty()) {
                    Notification::make()
                        ->title($isEn ? 'No records found to process' : 'لم يتم العثور على عناصر لمعالجتها')
                        ->warning()
                        ->send();
                    return;
                }

                $successCount = 0;
                $failCount = 0;
                $errors = [];

                foreach ($records as $record) {
                    try {
                        $field = property_exists($record, 'featured_image') || isset($record->featured_image) ? 'featured_image' : $imageField;
                        if (!empty($record->{$field})) {
                            $service->removeForModel($record, $field, $disk);
                            $successCount++;
                        }

                        if ($isProduct && !empty($data['process_sub_images']) && method_exists($record, 'images')) {
                            foreach ($record->images as $subImg) {
                                try {
                                    $service->removeForGalleryImage($subImg, $disk);
                                } catch (\Throwable $subE) {
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        $failCount++;
                        $name = $record->name_ar ?? $record->name_en ?? $record->title_ar ?? $record->id;
                        $errors[] = "{$name}: " . $e->getMessage();
                    }
                }

                if ($successCount > 0) {
                    Notification::make()
                        ->title($isEn ? 'Backgrounds removed successfully' : 'تمت إزالة الخلفيات بنجاح')
                        ->body($isEn
                            ? "Successfully processed {$successCount} item(s)." . ($failCount > 0 ? " ({$failCount} failed)" : '')
                            : "تم بنجاح معالجة وتحديث صور {$successCount} عنصر." . ($failCount > 0 ? " (وفشل {$failCount})" : ''))
                        ->success()
                        ->send();
                }

                if ($failCount > 0) {
                    Notification::make()
                        ->title($isEn ? "Failed to process {$failCount} item(s)" : "تعذر معالجة بعض العناصر ({$failCount})")
                        ->body(implode("\n", array_slice($errors, 0, 3)))
                        ->danger()
                        ->persistent()
                        ->send();
                }
            });
    }
}


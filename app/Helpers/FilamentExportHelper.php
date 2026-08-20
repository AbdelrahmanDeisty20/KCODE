<?php

namespace App\Helpers;

use Filament\Actions\BulkAction;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FilamentExportHelper
{
    /**
     * Define known field schemas per resource for database fields mapping.
     */
    public static function getResourceFields(string $resourceName): array
    {
        return match ($resourceName) {
            'products' => [
                'sku'                   => ['label_ar' => 'رمز المنتج (SKU)', 'label_en' => 'Product SKU', 'required' => false, 'aliases' => ['sku', 'كود', 'الرمز', 'code', 'barcode']],
                'name_ar'               => ['label_ar' => 'الاسم بالعربي', 'label_en' => 'Arabic Name', 'required' => true, 'aliases' => ['name_ar', 'namear', 'arabic_name', 'الاسم_بالعربي', 'الاسم', 'اسم_المنتج', 'name']],
                'name_en'               => ['label_ar' => 'الاسم بالإنجليزي', 'label_en' => 'English Name', 'required' => false, 'aliases' => ['name_en', 'nameen', 'english_name', 'الاسم_بالإنجليزي', 'title_en']],
                'price'                 => ['label_ar' => 'السعر', 'label_en' => 'Price', 'required' => true, 'aliases' => ['price', 'السعر', 'سعر', 'cost', 'unit_price', 'amount']],
                'stock'                 => ['label_ar' => 'المخزون / الكمية', 'label_en' => 'Stock Quantity', 'required' => false, 'aliases' => ['stock', 'qty', 'quantity', 'المخزون', 'الكمية', 'count']],
                'image'                 => ['label_ar' => 'رابط/مسار الصورة الرئيسية', 'label_en' => 'Main Image URL/Path', 'required' => false, 'aliases' => ['image', 'img', 'picture', 'photo', 'الصورة', 'رابط_الصورة', 'image_url']],
                'category_name'         => ['label_ar' => 'اسم القسم / التصنيف', 'label_en' => 'Category Name', 'required' => false, 'aliases' => ['category_name', 'category', 'cat', 'القسم', 'التصنيف', 'قسم']],
                'brand_name'            => ['label_ar' => 'اسم الماركة / البراند', 'label_en' => 'Brand Name', 'required' => false, 'aliases' => ['brand_name', 'brand', 'الماركة', 'البراند', 'العلامة_التجارية']],
                'description_ar'        => ['label_ar' => 'الوصف بالعربي', 'label_en' => 'Arabic Description', 'required' => false, 'aliases' => ['description_ar', 'description', 'desc', 'الوصف', 'تفاصيل']],
                'description_en'        => ['label_ar' => 'الوصف بالإنجليزي', 'label_en' => 'English Description', 'required' => false, 'aliases' => ['description_en', 'desc_en', 'الوصف_بالإنجليزي']],
                'short_name_ar'         => ['label_ar' => 'الاسم القصير بالعربي', 'label_en' => 'Short Name (AR)', 'required' => false, 'aliases' => ['short_name_ar', 'short_name', 'الاسم_القصير']],
                'short_name_en'         => ['label_ar' => 'الاسم القصير بالإنجليزي', 'label_en' => 'Short Name (EN)', 'required' => false, 'aliases' => ['short_name_en']],
                'ingredients_ar'        => ['label_ar' => 'المكونات بالعربي', 'label_en' => 'Ingredients (AR)', 'required' => false, 'aliases' => ['ingredients_ar', 'ingredients', 'المكونات']],
                'ingredients_en'        => ['label_ar' => 'المكونات بالإنجليزي', 'label_en' => 'Ingredients (EN)', 'required' => false, 'aliases' => ['ingredients_en']],
                'how_to_use_ar'         => ['label_ar' => 'طريقة الاستخدام بالعربي', 'label_en' => 'How to Use (AR)', 'required' => false, 'aliases' => ['how_to_use_ar', 'how_to_use', 'طريقة_الاستخدام']],
                'how_to_use_en'         => ['label_ar' => 'طريقة الاستخدام بالإنجليزي', 'label_en' => 'How to Use (EN)', 'required' => false, 'aliases' => ['how_to_use_en']],
                'final_url_slug'        => ['label_ar' => 'رابط المنتج (SEO Slug)', 'label_en' => 'URL Slug', 'required' => false, 'aliases' => ['final_url_slug', 'slug', 'url_slug', 'الرابط']],
                'seo_meta_title_ar'     => ['label_ar' => 'عنوان Meta Title (SEO)', 'label_en' => 'Meta Title (AR)', 'required' => false, 'aliases' => ['seo_meta_title_ar', 'meta_title', 'ar_product_title_seo', 'عنوان_السيو']],
                'meta_description_ar'   => ['label_ar' => 'وصف Meta Description بالعربي', 'label_en' => 'Meta Description (AR)', 'required' => false, 'aliases' => ['meta_description_ar', 'meta_desc_ar', 'وصف_السيو']],
                'meta_description_en'   => ['label_ar' => 'وصف Meta Description بالإنجليزي', 'label_en' => 'Meta Description (EN)', 'required' => false, 'aliases' => ['meta_description_en', 'meta_desc_en']],
                'primary_keyword_ar'    => ['label_ar' => 'الكلمة المفتاحية الرئيسية', 'label_en' => 'Primary Keyword (AR)', 'required' => false, 'aliases' => ['primary_keyword_ar', 'primary_keyword', 'الكلمة_الرئيسية']],
                'secondary_keywords_ar' => ['label_ar' => 'الكلمات المفتاحية الفرعية', 'label_en' => 'Secondary Keywords (AR)', 'required' => false, 'aliases' => ['secondary_keywords_ar', 'secondary_keywords', 'الكلمات_الفرعية']],
                'image_alt_ar'          => ['label_ar' => 'النص البديل للصورة Alt (AR)', 'label_en' => 'Image Alt (AR)', 'required' => false, 'aliases' => ['image_alt_ar', 'image_alt', 'نص_الصورة']],
                'keywords'              => ['label_ar' => 'الكلمات الدلالية (Keywords)', 'label_en' => 'Keywords', 'required' => false, 'aliases' => ['keywords', 'tags', 'الكلمات_الدلالية']],
            ],
            'categories' => [
                'name_ar' => ['label_ar' => 'الاسم بالعربي', 'label_en' => 'Arabic Name', 'required' => true, 'aliases' => ['name_ar', 'namear', 'arabic_name', 'الاسم_بالعربي', 'الاسم', 'اسم_القسم', 'name']],
                'name_en' => ['label_ar' => 'الاسم بالإنجليزي', 'label_en' => 'English Name', 'required' => false, 'aliases' => ['name_en', 'nameen', 'english_name', 'الاسم_بالإنجليزي']],
            ],
            'brands' => [
                'name_ar' => ['label_ar' => 'الاسم بالعربي', 'label_en' => 'Arabic Name', 'required' => true, 'aliases' => ['name_ar', 'namear', 'arabic_name', 'الاسم_بالعربي', 'الاسم', 'اسم_البراند', 'name']],
                'name_en' => ['label_ar' => 'الاسم بالإنجليزي', 'label_en' => 'English Name', 'required' => false, 'aliases' => ['name_en', 'nameen', 'english_name', 'الاسم_بالإنجليزي']],
            ],
            'blog_authors' => [
                'name'     => ['label_ar' => 'الاسم الكامل', 'label_en' => 'Full Name', 'required' => true, 'aliases' => ['name', 'full_name', 'الاسم', 'الاسم_الكامل', 'اسم_الكاتب']],
                'email'    => ['label_ar' => 'البريد الإلكتروني', 'label_en' => 'Email Address', 'required' => true, 'aliases' => ['email', 'mail', 'البريد', 'البريد_الإلكتروني', 'البريد_الالكتروني']],
                'phone'    => ['label_ar' => 'رقم الهاتف', 'label_en' => 'Phone Number', 'required' => false, 'aliases' => ['phone', 'mobile', 'الهاتف', 'الجوال', 'رقم_الهاتف']],
                'password' => ['label_ar' => 'كلمة المرور', 'label_en' => 'Password', 'required' => false, 'aliases' => ['password', 'pass', 'كلمة_المرور', 'الباسورد']],
            ],
            'product_reviews' => [
                'product_id' => ['label_ar' => 'معرف المنتج (Product ID)', 'label_en' => 'Product ID', 'required' => true, 'aliases' => ['product_id', 'product', 'المنتج', 'رقم_المنتج']],
                'user_id'    => ['label_ar' => 'معرف المستخدم (User ID)', 'label_en' => 'User ID', 'required' => false, 'aliases' => ['user_id', 'user', 'المستخدم', 'رقم_المستخدم']],
                'rating'     => ['label_ar' => 'التقييم (1-5)', 'label_en' => 'Rating (1-5)', 'required' => true, 'aliases' => ['rating', 'rate', 'التقييم', 'النجوم']],
                'comment'    => ['label_ar' => 'التعليق / الملاحظة', 'label_en' => 'Comment', 'required' => false, 'aliases' => ['comment', 'review', 'التعليق', 'الرأي']],
            ],
            'coupons' => [
                'code'     => ['label_ar' => 'كود الكوبون', 'label_en' => 'Coupon Code', 'required' => true, 'aliases' => ['code', 'coupon', 'الكود', 'كود_الكوبون']],
                'type'     => ['label_ar' => 'النوع (fixed/percentage)', 'label_en' => 'Type', 'required' => true, 'aliases' => ['type', 'النوع']],
                'value'    => ['label_ar' => 'القيمة', 'label_en' => 'Value', 'required' => true, 'aliases' => ['value', 'amount', 'القيمة', 'خصم']],
                'max_uses' => ['label_ar' => 'أقصى عدد للاستخدام', 'label_en' => 'Max Uses', 'required' => false, 'aliases' => ['max_uses', 'max', 'عدد_المرات']],
            ],
            default => [
                'name_ar' => ['label_ar' => 'الاسم بالعربي', 'label_en' => 'Arabic Name', 'required' => true, 'aliases' => ['name_ar', 'name', 'الاسم']],
                'name_en' => ['label_ar' => 'الاسم بالإنجليزي', 'label_en' => 'English Name', 'required' => false, 'aliases' => ['name_en', 'nameen']],
            ],
        };
    }

    /**
     * Smart auto-match algorithm to find matching Excel header for a database field.
     */
    public static function findBestMatch(string $fieldKey, array $fieldDef, array $rawHeaders): ?string
    {
        // Direct match
        foreach ($rawHeaders as $header) {
            $cleanHeader = strtolower(trim(str_replace(['_', ' '], '', $header)));
            $cleanKey = strtolower(trim(str_replace(['_', ' '], '', $fieldKey)));
            if ($cleanHeader === $cleanKey) {
                return $header;
            }
        }

        // Alias match
        $aliases = $fieldDef['aliases'] ?? [];
        foreach ($aliases as $alias) {
            $cleanAlias = strtolower(trim(str_replace(['_', ' '], '', $alias)));
            foreach ($rawHeaders as $header) {
                $cleanHeader = strtolower(trim(str_replace(['_', ' '], '', $header)));
                if ($cleanHeader === $cleanAlias) {
                    return $header;
                }
            }
        }

        // Substring match
        foreach ($rawHeaders as $header) {
            $cleanHeader = strtolower(trim($header));
            if (str_contains($cleanHeader, strtolower($fieldKey))) {
                return $header;
            }
        }

        return null;
    }

    /**
     * Extract header column names from CSV file.
     */
    public static function extractFileHeaders(string $filePath): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return [];
        }

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $firstLine = fgets($handle);
        rewind($handle);
        if ($bom === "\xEF\xBB\xBF") {
            fread($handle, 3);
        }

        $delimiter = ',';
        if ($firstLine !== false) {
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                $delimiter = "\t";
            }
        }

        $headers = fgetcsv($handle, 0, $delimiter);
        fclose($handle);

        if (!$headers || !is_array($headers)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($h) {
            return trim(str_replace(['"', "'", "\xEF\xBB\xBF"], '', $h));
        }, $headers)));
    }

    /**
     * Read CSV rows dynamically mapped to database field keys.
     */
    public static function readRowsWithMapping(string $filePath, array $mapping, array $resourceFields): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return [];
        }

        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $firstLine = fgets($handle);
        rewind($handle);
        if ($bom === "\xEF\xBB\xBF") {
            fread($handle, 3);
        }

        $delimiter = ',';
        if ($firstLine !== false) {
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                $delimiter = "\t";
            }
        }

        $rawHeaders = fgetcsv($handle, 0, $delimiter);
        if (!$rawHeaders) {
            fclose($handle);
            return [];
        }

        $cleanHeaders = array_map(function ($h) {
            return trim(str_replace(['"', "'", "\xEF\xBB\xBF"], '', $h));
        }, $rawHeaders);

        // Build lower-case lookup map for exact/clean headers
        $headerIndexMap = [];
        foreach ($cleanHeaders as $idx => $headerName) {
            $keyClean = strtolower(trim(str_replace(['_', ' '], '', $headerName)));
            $headerIndexMap[$keyClean] = $idx;
        }

        // Match target fields to column indices
        $finalMapping = [];
        foreach ($resourceFields as $fieldKey => $fieldDef) {
            $userSpecifiedHeader = trim($mapping[$fieldKey] ?? '');
            
            // 1. Try user specified header name
            if ($userSpecifiedHeader !== '') {
                $userClean = strtolower(trim(str_replace(['_', ' '], '', $userSpecifiedHeader)));
                if (isset($headerIndexMap[$userClean])) {
                    $finalMapping[$fieldKey] = $headerIndexMap[$userClean];
                    continue;
                }
            }

            // 2. Try default fieldKey name
            $defaultClean = strtolower(trim(str_replace(['_', ' '], '', $fieldKey)));
            if (isset($headerIndexMap[$defaultClean])) {
                $finalMapping[$fieldKey] = $headerIndexMap[$defaultClean];
                continue;
            }

            // 3. Try smart auto-match (aliases, Arabic terms, etc.)
            $bestMatchedHeader = self::findBestMatch($fieldKey, $fieldDef, $cleanHeaders);
            if ($bestMatchedHeader !== null) {
                $bestClean = strtolower(trim(str_replace(['_', ' '], '', $bestMatchedHeader)));
                if (isset($headerIndexMap[$bestClean])) {
                    $finalMapping[$fieldKey] = $headerIndexMap[$bestClean];
                    continue;
                }
            }

            $finalMapping[$fieldKey] = null;
        }

        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $mappedRow = [];
            foreach ($finalMapping as $targetFieldKey => $colIdx) {
                if ($colIdx === null || !isset($row[$colIdx])) {
                    $mappedRow[$targetFieldKey] = null;
                    continue;
                }

                $val = $row[$colIdx];
                $mappedRow[$targetFieldKey] = is_string($val) ? trim($val) : $val;
            }

            if (array_filter($mappedRow, fn($v) => !is_null($v) && $v !== '')) {
                $rows[] = $mappedRow;
            }
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Reusable bulk action to export selected table records to CSV.
     */
    public static function makeExportBulkAction(string $fileName, array $headers, callable $rowCallback): BulkAction
    {
        $labelAr = 'تصدير المحددة إلى إكسيل';
        $labelEn = 'Export Selected to Excel';
        $label = app()->getLocale() == 'ar' ? $labelAr : $labelEn;

        return BulkAction::make('export_excel')
            ->label($label)
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function (Collection $records) use ($fileName, $headers, $rowCallback): StreamedResponse {
                $response = new StreamedResponse(function () use ($records, $headers, $rowCallback) {
                    $handle = fopen('php://output', 'w');
                    fwrite($handle, "\xEF\xBB\xBF");
                    fputcsv($handle, $headers);
                    foreach ($records as $record) {
                        $row = $rowCallback($record);
                        fputcsv($handle, $row);
                    }
                    fclose($handle);
                });

                $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '_' . date('Y-m-d_H-i-s') . '.csv"');
                
                return $response;
            });
    }

    /**
     * Reusable header action to export ALL table records directly to CSV.
     */
    public static function makeExportHeaderAction(string $fileName, array $headers, callable $rowCallback, string $modelClass): Action
    {
        $labelAr = 'تصدير الكل إلى إكسيل';
        $labelEn = 'Export All to Excel';
        $label = app()->getLocale() == 'ar' ? $labelAr : $labelEn;

        return Action::make('export_excel_all')
            ->label($label)
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function () use ($fileName, $headers, $rowCallback, $modelClass): StreamedResponse {
                $records = $modelClass::all();

                $response = new StreamedResponse(function () use ($records, $headers, $rowCallback) {
                    $handle = fopen('php://output', 'w');
                    fwrite($handle, "\xEF\xBB\xBF");
                    fputcsv($handle, $headers);
                    foreach ($records as $record) {
                        $row = $rowCallback($record);
                        fputcsv($handle, $row);
                    }
                    fclose($handle);
                });

                $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '_all_' . date('Y-m-d_H-i-s') . '.csv"');
                
                return $response;
            });
    }

    /**
     * Reusable header action to import records from Excel/CSV with DB Fields View & Custom Header Mapping.
     */
    public static function makeImportHeaderAction(string $resourceName, callable $importCallback): Action
    {
        $isAr = app()->getLocale() == 'ar';
        $label = $isAr ? 'استيراد من إكسيل / CSV' : 'Import from Excel / CSV';

        $instructionsHtml = '<div style="background-color: #1e1e2e; padding: 14px; border-radius: 8px; border-left: 4px solid #10b981; margin-bottom: 12px; color: #d1d1d6; font-size: 13px;">'
            . ($isAr 
                ? 'قم برفع ملف Excel أو CSV. يمكنك ترك أسماء مفاتيح الأعمدة الافتراضية كما هي، أو كتابة أسماء الأعمدة الخاصة بك لتطابق شيت الإكسيل المرفوع.'
                : 'Upload your Excel or CSV file. You can leave default column keys or customize header names to match your uploaded Excel file.')
            . '</div>';

        $fields = self::getResourceFields($resourceName);

        return Action::make('import_excel')
            ->label($label)
            ->icon('heroicon-o-document-arrow-up')
            ->color('info')
            ->form([
                Placeholder::make('import_instructions')
                    ->label('')
                    ->content(new HtmlString($instructionsHtml)),

                FileUpload::make('file')
                    ->label($isAr ? 'ملف Excel / CSV' : 'Excel / CSV File')
                    ->acceptedFileTypes([
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ])
                    ->required()
                    ->disk('public')
                    ->directory('imports'),

                Section::make($isAr ? 'عرض وتحديد المفاتيح/الأعمدة في الإكسيل (Excel Header Keys)' : 'Excel Header Keys Mapping')
                    ->description($isAr 
                        ? 'تظهر هنا حقول قاعدة البيانات مع مفاتيحها الافتراضية. اتركها كما هي لاستخدام المفتاح الافتراضي، أو اكتب اسم العمود الموجود في ملفك:' 
                        : 'Database fields with default header keys are shown below. Leave as default or type custom column headers from your file:')
                    ->schema(function () use ($fields, $isAr) {
                        $schema = [];

                        foreach ($fields as $fieldKey => $fieldDef) {
                            $labelName = $isAr ? $fieldDef['label_ar'] : $fieldDef['label_en'];
                            if (!empty($fieldDef['required'])) {
                                $labelName .= ' *';
                            }

                            $schema[] = TextInput::make("mapping.{$fieldKey}")
                                ->label($labelName)
                                ->default($fieldKey)
                                ->placeholder($fieldKey)
                                ->helperText($isAr 
                                    ? "اسم العمود في شيت الإكسيل (افتراضي: {$fieldKey})"
                                    : "Header name in row 1 of Excel sheet (default: {$fieldKey})");
                        }

                        return $schema;
                    })
                    ->columns(2)
                    ->collapsible(),
            ])
            ->action(function (array $data) use ($resourceName, $importCallback, $fields) {
                $isAr = app()->getLocale() == 'ar';
                $filePath = Storage::disk('public')->path($data['file']);
                $mapping = $data['mapping'] ?? [];

                $rows = self::readRowsWithMapping($filePath, $mapping, $fields);

                if (empty($rows)) {
                    Notification::make()
                        ->title($isAr ? 'ملف فارغ أو لم يتم مطابقة أي بيانات' : 'Empty File or No Data Mapped')
                        ->danger()
                        ->send();
                    @unlink($filePath);
                    return;
                }

                $successCount = 0;
                $errorCount = 0;

                foreach ($rows as $rowData) {
                    try {
                        $importCallback($rowData);
                        $successCount++;
                    } catch (\Exception $e) {
                        Log::error('Import error: ' . $e->getMessage());
                        $errorCount++;
                    }
                }

                @unlink($filePath);

                Notification::make()
                    ->title($isAr ? 'اكتمل الاستيراد' : 'Import Completed')
                    ->body($isAr 
                        ? "تم استيراد {$successCount} سجل بنجاح. (فشل: {$errorCount})." 
                        : "Successfully imported {$successCount} records. (Failed: {$errorCount}).")
                    ->success()
                    ->send();
            });
    }
}

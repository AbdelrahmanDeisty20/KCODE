<?php

namespace App\Helpers;

use Filament\Actions\BulkAction;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FilamentExportHelper
{
    /**
     * Reusable bulk action to export selected table records to CSV (fully Excel compatible UTF-8 with BOM).
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
                    
                    // Prepend BOM to force Excel to read Arabic text (UTF-8) correctly
                    fwrite($handle, "\xEF\xBB\xBF");
                    
                    // Add CSV Headers
                    fputcsv($handle, $headers);
                    
                    // Add CSV Rows
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
     * Reusable header action to export ALL table records directly to CSV / Excel.
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
                    
                    // Prepend BOM to force Excel to read Arabic text (UTF-8) correctly
                    fwrite($handle, "\xEF\xBB\xBF");
                    
                    // Add CSV Headers
                    fputcsv($handle, $headers);
                    
                    // Add CSV Rows
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
     * Reusable header action to import records from a CSV / Excel file.
     */
    public static function makeImportHeaderAction(string $resourceName, callable $importCallback): Action
    {
        $labelAr = 'استيراد من إكسيل / CSV';
        $labelEn = 'Import from Excel / CSV';
        $label = app()->getLocale() == 'ar' ? $labelAr : $labelEn;

        $instructionsHtml = '';
        if (app()->getLocale() == 'ar') {
            $instructionsHtml .= '<div style="background-color: #1e1e2e; padding: 16px; border-radius: 8px; border-left: 4px solid #10b981; margin-bottom: 12px;">';
            $instructionsHtml .= '<h4 style="font-weight: bold; color: #10b981; margin: 0 0 8px 0; font-size: 14px;">تعليمات شيت الإكسيل / CSV للاستيراد:</h4>';
            $instructionsHtml .= '<p style="font-size: 12px; color: #d1d1d6; margin: 0 0 10px 0;">يرجى رفع ملف بصيغة <strong>CSV</strong> أو <strong>Excel</strong> يحتوي على أسماء الأعمدة التالية في الصف الأول:</p>';
            
            if ($resourceName === 'products') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #34d399; font-size: 11px; display: block; direction: ltr; text-align: left;">sku, name_ar, name_en, price, stock, category_name, brand_name, description_ar</code>';
            } elseif ($resourceName === 'categories') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #34d399; font-size: 11px; display: block; direction: ltr; text-align: left;">name_ar, name_en</code>';
            } elseif ($resourceName === 'brands') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #34d399; font-size: 11px; display: block; direction: ltr; text-align: left;">name_ar, name_en</code>';
            } elseif ($resourceName === 'coupons') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #34d399; font-size: 11px; display: block; direction: ltr; text-align: left;">code, type, value, max_uses</code>';
            }
            $instructionsHtml .= '</div>';
        } else {
            $instructionsHtml .= '<div style="background-color: #1e1e2e; padding: 16px; border-radius: 8px; border-left: 4px solid #3b82f6; margin-bottom: 12px;">';
            $instructionsHtml .= '<h4 style="font-weight: bold; color: #3b82f6; margin: 0 0 8px 0; font-size: 14px;">Excel / CSV Import Instructions:</h4>';
            $instructionsHtml .= '<p style="font-size: 12px; color: #d1d1d6; margin: 0 0 10px 0;">Please upload a <strong>CSV</strong> or <strong>Excel</strong> file with these header column names in row 1:</p>';
            
            if ($resourceName === 'products') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #60a5fa; font-size: 11px; display: block;">sku, name_ar, name_en, price, stock, category_name, brand_name, description_ar</code>';
            } elseif ($resourceName === 'categories') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #60a5fa; font-size: 11px; display: block;">name_ar, name_en</code>';
            } elseif ($resourceName === 'brands') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #60a5fa; font-size: 11px; display: block;">name_ar, name_en</code>';
            } elseif ($resourceName === 'coupons') {
                $instructionsHtml .= '<code style="background: #2d2d3f; padding: 6px 10px; border-radius: 6px; color: #60a5fa; font-size: 11px; display: block;">code, type, value, max_uses</code>';
            }
            $instructionsHtml .= '</div>';
        }

        return Action::make('import_excel')
            ->label($label)
            ->icon('heroicon-o-document-arrow-up')
            ->color('info')
            ->form([
                Placeholder::make('import_instructions')
                    ->label('')
                    ->content(new HtmlString($instructionsHtml)),

                FileUpload::make('file')
                    ->label(app()->getLocale() == 'ar' ? 'ملف Excel / CSV' : 'Excel / CSV File')
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
            ])
            ->action(function (array $data) use ($importCallback) {
                $filePath = Storage::disk('public')->path($data['file']);
                
                if (!file_exists($filePath)) {
                    Notification::make()
                        ->title(app()->getLocale() == 'ar' ? 'خطأ في الملف' : 'File Error')
                        ->body(app()->getLocale() == 'ar' ? 'لم يتم العثور على الملف المرفوع.' : 'The uploaded file was not found.')
                        ->danger()
                        ->send();
                    return;
                }

                $rows = [];
                $handle = fopen($filePath, 'r');
                if ($handle !== false) {
                    // Remove BOM if present
                    $bom = fread($handle, 3);
                    if ($bom !== "\xEF\xBB\xBF") {
                        rewind($handle);
                    }

                    // Detect delimiter
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

                    $headers = null;
                    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                        if (!$headers) {
                            $headers = array_map(function ($h) {
                                $h = trim(str_replace(['"', "'"], '', $h));
                                return strtolower(preg_replace('/[()\s\-\?؟]+/', '_', trim($h, '_')));
                            }, $row);
                            continue;
                        }

                        if (count($row) < count($headers)) {
                            $row = array_pad($row, count($headers), '');
                        }
                        $rowData = array_combine($headers, array_slice($row, 0, count($headers)));
                        $rows[] = $rowData;
                    }
                    fclose($handle);
                }

                if (empty($rows)) {
                    Notification::make()
                        ->title(app()->getLocale() == 'ar' ? 'ملف فارغ أو غير صالح' : 'Empty or Invalid File')
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

                $successTitle = app()->getLocale() == 'ar' ? 'اكتمل الاستيراد' : 'Import Completed';
                $successBody = app()->getLocale() == 'ar' 
                    ? "تم استيراد {$successCount} سجل بنجاح. (فشل: {$errorCount})." 
                    : "Successfully imported {$successCount} records. (Failed: {$errorCount}).";

                Notification::make()
                    ->title($successTitle)
                    ->body($successBody)
                    ->success()
                    ->send();
            });
    }
}

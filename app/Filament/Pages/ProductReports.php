<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductReports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected string $view = 'filament.pages.product-reports';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'en' ? 'Reports & Exporter' : 'التقارير وتصدير البيانات';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Product Master Exporter' : 'تقرير وتصدير المنتجات';
    }

    public function getTitle(): string
    {
        return app()->getLocale() === 'en' 
            ? 'Product Master Reports & Exporter' 
            : 'تقارير وتصدير المنتجات المخصصة (Master CSV Exporter)';
    }

    /**
     * Complete list of all 95 columns from KCODE_FINAL_10_10_POLISHED - Product_Master.csv
     */
    public array $allHeaders = [
        'product_id',
        'sku_slug',
        'brand_en',
        'brand_ar',
        'display_en_name',
        'display_ar_name',
        'short_ar_name',
        'category',
        'sub_category',
        'product_step',
        'layer_order',
        'routine_time',
        'is_core_routine_step',
        'is_add_on',
        'primary_concern',
        'secondary_concern',
        'tertiary_concern',
        'skin_type_fit',
        'best_for_ar',
        'routine_tags',
        'quiz_skin_type_tags',
        'quiz_concern_tags',
        'skin_code_tags',
        'default_priority_score',
        'same_step_choice_group',
        'am_default',
        'pm_default',
        'selection_rule_ar',
        'ar_product_title_seo',
        'en_product_title_seo',
        'en_short_hook',
        'ar_long_description',
        'en_long_description',
        'ar_key_benefits',
        'en_key_benefits',
        'texture_ar',
        'texture_en',
        'why_kcode_ar',
        'why_kcode_en',
        'how_to_use_ar',
        'how_to_use_en',
        'usage_frequency_ar',
        'active_strength_level Low / Medium / High',
        'safety_notes_ar',
        'safety_notes_en',
        'avoid_pairing_same_routine',
        'developer_output_rule',
        'show_alternatives_button',
        'remove_if_customer_has_it',
        'source_url',
        'data_confidence',
        'needs_manual_check',
        'seo_meta_title_ar',
        'meta_description_en',
        'meta_description_ar',
        'primary_keyword_en',
        'primary_keyword_ar',
        'secondary_keywords_en',
        'secondary_keywords_ar',
        'final_url_slug',
        'image_alt_en',
        'image_alt_ar',
        'og_title_ar',
        'og_description_en',
        'og_description_ar',
        'pdp_headline_en',
        'above_fold_hook_en',
        'primary_badge_en',
        'primary_badge_ar',
        'result_promise_en',
        'result_promise_ar',
        'objection_answer_en',
        'objection_answer_ar',
        'routine_reason_en',
        'routine_reason_ar',
        'bundle_cta_en',
        'bundle_cta_ar',
        'add_to_cart_microcopy_en',
        'add_to_cart_microcopy_ar',
        'max_default_products_per_step',
        'selection_weight_formula_note',
        'selection_priority_tie_breaker',
        'exclusion_rule',
        'conflict_rule_strictness',
        'pairing_rule',
        'alternative_button_rule',
        'add_on_display_rule',
        'routine_builder_note',
        'fallback_product_rule',
        'show_in_default_quiz_result',
        'Keywords',
        'Problem Tag',
        'Result Tag',
        'Conflict Rules',
        'Routine Role',
    ];

    public array $selectedColumns = [];

    // Filters
    public string $search = '';
    public string $selectedCategory = '';
    public string $selectedBrand = '';

    public function mount(): void
    {
        $this->selectedColumns = $this->allHeaders;
    }

    public function selectAllColumns(): void
    {
        $this->selectedColumns = $this->allHeaders;
    }

    public function deselectAllColumns(): void
    {
        $this->selectedColumns = [];
    }

    public function toggleColumnGroup(array $columns): void
    {
        $allPresent = count(array_intersect($columns, $this->selectedColumns)) === count($columns);
        if ($allPresent) {
            $this->selectedColumns = array_values(array_diff($this->selectedColumns, $columns));
        } else {
            $this->selectedColumns = array_values(array_unique(array_merge($this->selectedColumns, $columns)));
        }
    }

    /**
     * Reads and parses Master CSV rows with filtering
     */
    public function getMasterCsvRows(): array
    {
        $filePath = base_path('exicel/KCODE_FINAL_10_10_POLISHED - Product_Master.csv');
        if (!file_exists($filePath)) {
            return [];
        }

        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle);
            if ($headers === false) {
                fclose($handle);
                return [];
            }

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) !== count($headers)) {
                    continue;
                }
                $item = array_combine($headers, $row);

                if (trim($item['product_id'] ?? '') === 'product_id') {
                    continue;
                }

                // Apply Search Filter
                if (!empty($this->search)) {
                    $searchTerm = mb_strtolower(trim($this->search));
                    $matchesId = str_contains(mb_strtolower($item['product_id'] ?? ''), $searchTerm);
                    $matchesEn = str_contains(mb_strtolower($item['display_en_name'] ?? ''), $searchTerm);
                    $matchesAr = str_contains(mb_strtolower($item['display_ar_name'] ?? ''), $searchTerm);
                    $matchesBrand = str_contains(mb_strtolower($item['brand_ar'] ?? ''), $searchTerm) || str_contains(mb_strtolower($item['brand_en'] ?? ''), $searchTerm);
                    
                    if (!$matchesId && !$matchesEn && !$matchesAr && !$matchesBrand) {
                        continue;
                    }
                }

                // Apply Category Filter
                if (!empty($this->selectedCategory)) {
                    if (mb_strtolower(trim($item['category'] ?? '')) !== mb_strtolower(trim($this->selectedCategory))) {
                        continue;
                    }
                }

                // Apply Brand Filter
                if (!empty($this->selectedBrand)) {
                    $brand = mb_strtolower(trim($this->selectedBrand));
                    $brandAr = mb_strtolower(trim($item['brand_ar'] ?? ''));
                    $brandEn = mb_strtolower(trim($item['brand_en'] ?? ''));
                    if ($brandAr !== $brand && $brandEn !== $brand) {
                        continue;
                    }
                }

                $rows[] = $item;
            }
            fclose($handle);
        }

        return $rows;
    }

    public function getCategoriesListProperty(): array
    {
        $filePath = base_path('exicel/KCODE_FINAL_10_10_POLISHED - Product_Master.csv');
        if (!file_exists($filePath)) return [];
        $cats = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) === count($headers)) {
                    $item = array_combine($headers, $row);
                    if (!empty($item['category']) && trim($item['product_id'] ?? '') !== 'product_id') {
                        $cats[] = trim($item['category']);
                    }
                }
            }
            fclose($handle);
        }
        $cats = array_unique($cats);
        sort($cats);
        return $cats;
    }

    public function getBrandsListProperty(): array
    {
        $filePath = base_path('exicel/KCODE_FINAL_10_10_POLISHED - Product_Master.csv');
        if (!file_exists($filePath)) return [];
        $brands = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) === count($headers)) {
                    $item = array_combine($headers, $row);
                    if (!empty($item['brand_ar']) && trim($item['product_id'] ?? '') !== 'product_id') {
                        $brands[] = trim($item['brand_ar']);
                    }
                }
            }
            fclose($handle);
        }
        $brands = array_unique($brands);
        sort($brands);
        return $brands;
    }

    public function exportReport(): StreamedResponse
    {
        $selectedCols = array_values(array_intersect($this->allHeaders, $this->selectedColumns));
        if (empty($selectedCols)) {
            $selectedCols = $this->allHeaders;
        }

        $rows = $this->getMasterCsvRows();

        $fileName = 'KCODE_Product_Master_Report_' . date('Y-m-d_H-i') . '.csv';

        return response()->streamDownload(function () use ($selectedCols, $rows) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Microsoft Excel Arabic compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Write CSV Header
            fputcsv($file, $selectedCols);

            // Write Data Rows
            foreach ($rows as $row) {
                $line = [];
                foreach ($selectedCols as $col) {
                    $line[] = $row[$col] ?? '';
                }
                fputcsv($file, $line);
            }

            fclose($file);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}

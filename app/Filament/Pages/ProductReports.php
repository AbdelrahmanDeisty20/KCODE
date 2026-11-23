<?php

namespace App\Filament\Pages;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Support\StaticCache;
use BackedEnum;
use Filament\Pages\Page;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductReports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-s-arrow-down-tray';

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
     * Complete list of report columns backed by live database Products & Quiz Rules.
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

    /** @var array<string, array<int, array<string, string>>> */
    private array $rowsCache = [];

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
     * Fetch live database products and map them into report rows matching the exporter schema.
     */
    public function getMasterCsvRows(): array
    {
        $cacheKey = md5($this->search . '|' . $this->selectedCategory . '|' . $this->selectedBrand);
        if (isset($this->rowsCache[$cacheKey])) {
            return $this->rowsCache[$cacheKey];
        }

        $query = Product::query()
            ->with([
                'brand',
                'category',
                'subCategory',
                'skinTypes',
                'concerns',
                'goals',
                'marketingDetail',
                'recommendationRule',
            ])
            ->orderBy('id');

        if (trim($this->search) !== '') {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('sku', 'LIKE', "%{$term}%")
                    ->orWhere('name_en', 'LIKE', "%{$term}%")
                    ->orWhere('name_ar', 'LIKE', "%{$term}%")
                    ->orWhereHas('brand', fn ($bq) => $bq->where('name_ar', 'LIKE', "%{$term}%")->orWhere('name_en', 'LIKE', "%{$term}%"));
            });
        }

        if (trim($this->selectedCategory) !== '') {
            $cat = trim($this->selectedCategory);
            $query->whereHas('category', fn ($cq) => $cq->where('name_en', $cat)->orWhere('name_ar', $cat));
        }

        if (trim($this->selectedBrand) !== '') {
            $brand = trim($this->selectedBrand);
            $query->whereHas('brand', fn ($bq) => $bq->where('name_ar', $brand)->orWhere('name_en', $brand));
        }

        $rows = $query->get()
            ->map(fn (Product $product): array => $this->mapProductToReportRow($product))
            ->all();

        return $this->rowsCache[$cacheKey] = $rows;
    }

    public function getCategoriesListProperty(): array
    {
        $categories = StaticCache::remember('categories', 'all', fn () => Category::all());

        return $categories
            ->map(fn ($c) => trim((string) ($c->name_en ?: $c->name_ar)))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public function getBrandsListProperty(): array
    {
        $brands = StaticCache::remember('brands', 'all', fn () => Brand::all());

        return $brands
            ->map(fn ($b) => trim((string) ($b->name_ar ?: $b->name_en)))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
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

    /**
     * Map an Eloquent Product record and its related rule/marketing records into the exporter columns.
     *
     * @return array<string, string>
     */
    private function mapProductToReportRow(Product $product): array
    {
        $rule = $product->recommendationRule;
        $marketing = $product->marketingDetail;

        $skinTypesAr = $product->skinTypes->pluck('name_ar')->filter()->implode(', ');
        $skinTypesEn = $product->skinTypes->pluck('name_en')->filter()->implode(', ');
        $concernsAr = $product->concerns->pluck('name_ar')->filter()->implode(', ');
        $goalsAr = $product->goals->pluck('name_ar')->filter()->implode(', ');

        $row = array_fill_keys($this->allHeaders, '');

        return array_merge($row, [
            'product_id' => (string) ($product->sku ?: $product->id),
            'sku_slug' => (string) ($product->final_url_slug ?: $product->sku),
            'brand_en' => (string) ($product->brand?->name_en ?? ''),
            'brand_ar' => (string) ($product->brand?->name_ar ?? ''),
            'display_en_name' => (string) ($product->name_en ?? ''),
            'display_ar_name' => (string) ($product->name_ar ?? ''),
            'short_ar_name' => (string) ($product->short_name_ar ?? ''),
            'category' => (string) ($product->category?->name_en ?: $product->category?->name_ar ?? ''),
            'sub_category' => (string) ($product->subCategory?->name_en ?: $product->subCategory?->name_ar ?? ''),
            'product_step' => (string) ($product->routine_step_title_ar ?: $product->role_ar ?? ''),
            'layer_order' => (string) ($product->routine_step_number ?? ''),
            'routine_time' => (string) ($rule?->usage_policy['period'] ?? ''),
            'is_core_routine_step' => $rule?->is_catalog_eligible ? 'true' : 'false',
            'is_add_on' => $rule?->supplement_kind ? 'true' : 'false',
            'skin_type_fit' => $skinTypesEn ?: $skinTypesAr,
            'best_for_ar' => (string) ($product->brief_insight_ar ?? ''),
            'routine_tags' => is_array($rule?->roles) ? implode(', ', $rule->roles) : '',
            'quiz_skin_type_tags' => $skinTypesAr,
            'quiz_concern_tags' => $concernsAr ?: $goalsAr,
            'default_priority_score' => (string) ($rule?->default_priority_score ?? ''),
            'same_step_choice_group' => (string) ($rule?->same_step_choice_group ?? ''),
            'am_default' => $rule ? ($rule->am_default ? 'true' : 'false') : '',
            'pm_default' => $rule ? ($rule->pm_default ? 'true' : 'false') : '',
            'selection_rule_ar' => (string) ($rule?->selection_rule_ar ?? ''),
            'ar_product_title_seo' => (string) ($product->ar_product_title_seo ?? ''),
            'en_product_title_seo' => (string) ($product->en_product_title_seo ?? ''),
            'en_short_hook' => (string) ($product->en_short_hook ?? ''),
            'ar_long_description' => (string) ($product->description_ar ?? ''),
            'en_long_description' => (string) ($product->description_en ?? ''),
            'ar_key_benefits' => $this->flattenListColumn($product->ar_key_benefits),
            'en_key_benefits' => $this->flattenListColumn($product->en_key_benefits),
            'texture_ar' => (string) ($product->texture_ar ?? ''),
            'texture_en' => (string) ($product->texture_en ?? ''),
            'why_kcode_ar' => (string) ($product->why_kcode_ar ?? ''),
            'why_kcode_en' => (string) ($product->why_kcode_en ?? ''),
            'how_to_use_ar' => (string) ($product->how_to_use_ar ?? ''),
            'how_to_use_en' => (string) ($product->how_to_use_en ?? ''),
            'usage_frequency_ar' => (string) ($product->usage_frequency_ar ?? ''),
            'active_strength_level Low / Medium / High' => (string) ($product->active_strength_level ?? ''),
            'safety_notes_ar' => (string) ($product->safety_notes_ar ?? ''),
            'safety_notes_en' => (string) ($product->safety_notes_en ?? ''),
            'seo_meta_title_ar' => (string) ($product->seo_meta_title_ar ?? ''),
            'meta_description_en' => (string) ($product->meta_description_en ?? ''),
            'meta_description_ar' => (string) ($product->meta_description_ar ?? ''),
            'primary_keyword_en' => (string) ($product->primary_keyword_en ?? ''),
            'primary_keyword_ar' => (string) ($product->primary_keyword_ar ?? ''),
            'secondary_keywords_en' => (string) ($product->secondary_keywords_en ?? ''),
            'secondary_keywords_ar' => (string) ($product->secondary_keywords_ar ?? ''),
            'final_url_slug' => (string) ($product->final_url_slug ?? ''),
            'image_alt_en' => (string) ($product->image_alt_en ?? ''),
            'image_alt_ar' => (string) ($product->image_alt_ar ?? ''),
            'og_title_ar' => (string) ($product->og_title_ar ?? ''),
            'og_description_en' => (string) ($product->og_description_en ?? ''),
            'og_description_ar' => (string) ($product->og_description_ar ?? ''),
            'pdp_headline_en' => (string) ($product->pdp_headline_en ?? ''),
            'above_fold_hook_en' => (string) ($product->above_fold_hook_en ?? ''),
            'primary_badge_en' => (string) ($marketing?->primary_badge_en ?? ''),
            'primary_badge_ar' => (string) ($marketing?->primary_badge_ar ?? ''),
            'result_promise_en' => (string) ($marketing?->result_promise_en ?? ''),
            'result_promise_ar' => (string) ($marketing?->result_promise_ar ?? ''),
            'objection_answer_en' => (string) ($marketing?->objection_answer_en ?? ''),
            'objection_answer_ar' => (string) ($marketing?->objection_answer_ar ?? ''),
            'routine_reason_en' => (string) ($marketing?->routine_reason_en ?? ''),
            'routine_reason_ar' => (string) ($marketing?->routine_reason_ar ?? ''),
            'bundle_cta_en' => (string) ($marketing?->bundle_cta_en ?? ''),
            'bundle_cta_ar' => (string) ($marketing?->bundle_cta_ar ?? ''),
            'add_to_cart_microcopy_en' => (string) ($marketing?->add_to_cart_microcopy_en ?? ''),
            'add_to_cart_microcopy_ar' => (string) ($marketing?->add_to_cart_microcopy_ar ?? ''),
            'max_default_products_per_step' => (string) ($rule?->max_default_products_per_step ?? ''),
            'selection_weight_formula_note' => (string) ($rule?->selection_weight_formula_note ?? ''),
            'selection_priority_tie_breaker' => (string) ($rule?->selection_priority_tie_breaker ?? ''),
            'exclusion_rule' => (string) ($rule?->exclusion_rule ?? ''),
            'conflict_rule_strictness' => (string) ($rule?->conflict_rule_strictness ?? ''),
            'pairing_rule' => (string) ($rule?->pairing_rule ?? ''),
            'alternative_button_rule' => (string) ($rule?->alternative_button_rule ?? ''),
            'add_on_display_rule' => (string) ($rule?->add_on_display_rule ?? ''),
            'routine_builder_note' => (string) ($rule?->routine_builder_note ?? ''),
            'fallback_product_rule' => (string) ($rule?->fallback_product_rule ?? ''),
            'show_in_default_quiz_result' => $rule ? ($rule->is_catalog_eligible ? 'true' : 'false') : '',
            'Keywords' => (string) ($product->keywords ?? ''),
            'Conflict Rules' => (string) ($rule?->conflict_groups ?? ''),
            'Routine Role' => (string) ($rule?->routine_role ?: $product->role_en ?: ''),
        ]);
    }

    private function flattenListColumn(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (!is_array($value)) {
            return '';
        }

        return collect($value)
            ->map(function ($item) {
                if (is_array($item)) {
                    $title = trim((string) ($item['title'] ?? ''));
                    $text = trim((string) ($item['text'] ?? ''));
                    return trim($title !== '' && $text !== '' ? "{$title}: {$text}" : ($title ?: $text));
                }

                return trim((string) $item);
            })
            ->filter()
            ->implode(' | ');
    }
}

<x-filament-panels::page>
    <style>
        .kcode-reports-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            direction: rtl;
            text-align: right;
            font-family: inherit;
        }

        .kcode-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .kcode-banner {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        .kcode-banner-title {
            font-size: 1.35rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #ffffff;
            margin: 0 0 0.25rem 0;
        }

        .kcode-banner-desc {
            font-size: 0.875rem;
            color: #c7d2fe;
            margin: 0;
        }

        .kcode-export-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.875rem;
            border-radius: 0.75rem;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
            transition: all 0.2s ease;
            white-space: nowrap;
            text-decoration: none;
        }

        .kcode-export-btn:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
        }

        .kcode-filter-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        html.dark .kcode-filter-card {
            background: #18181b;
            border-color: #27272a;
        }

        .kcode-filter-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-top: 1rem;
        }

        @media (min-width: 768px) {
            .kcode-filter-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .kcode-input-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
            color: #475569;
        }

        html.dark .kcode-input-label {
            color: #a1a1aa;
        }

        .kcode-input {
            width: 100%;
            padding: 0.6rem 0.85rem;
            border-radius: 0.6rem;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            font-size: 0.875rem;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s;
        }

        html.dark .kcode-input {
            background: #27272a;
            border-color: #3f3f46;
            color: #f4f4f5;
        }

        .kcode-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .kcode-columns-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        html.dark .kcode-columns-card {
            background: #18181b;
            border-color: #27272a;
        }

        .kcode-group-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        html.dark .kcode-group-box {
            background: #27272a/50;
            border-color: #3f3f46;
        }

        .kcode-cols-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .kcode-col-pill {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.6rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: border-color 0.15s;
            word-break: break-all;
        }

        html.dark .kcode-col-pill {
            background: #18181b;
            border-color: #3f3f46;
            color: #e4e4e7;
        }

        .kcode-col-pill:hover {
            border-color: #6366f1;
        }

        .kcode-col-pill input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            accent-color: #4f46e5;
            cursor: pointer;
        }

        .kcode-btn-sm {
            padding: 0.35rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s;
        }

        .kcode-btn-primary {
            background: #e0e7ff;
            color: #4338ca;
            border-color: #c7d2fe;
        }

        html.dark .kcode-btn-primary {
            background: #312e81;
            color: #e0e7ff;
            border-color: #4338ca;
        }

        .kcode-btn-danger {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fca5a5;
        }

        html.dark .kcode-btn-danger {
            background: #7f1d1d;
            color: #fecaca;
            border-color: #991b1b;
        }

        .kcode-table-container {
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            max-height: 440px;
        }

        html.dark .kcode-table-container {
            border-color: #27272a;
        }

        .kcode-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
            font-size: 0.75rem;
        }

        .kcode-table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 800;
            padding: 0.65rem 0.85rem;
            border-bottom: 2px solid #cbd5e1;
            position: sticky;
            top: 0;
            z-index: 10;
            white-space: nowrap;
        }

        html.dark .kcode-table th {
            background: #27272a;
            color: #e4e4e7;
            border-bottom-color: #3f3f46;
        }

        .kcode-table td {
            padding: 0.6rem 0.85rem;
            border-bottom: 1px solid #f1f5f9;
            white-space: nowrap;
            max-width: 260px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        html.dark .kcode-table td {
            border-bottom-color: #27272a;
        }

        .kcode-table tr:hover td {
            background: rgba(99, 102, 241, 0.05);
        }

        .kcode-icon {
            width: 18px;
            height: 18px;
            display: inline-block;
            vertical-align: middle;
            flex-shrink: 0;
        }
    </style>

    <div class="kcode-reports-wrapper">

        <!-- Banner Card -->
        <div class="kcode-banner">
            <div>
                <h2 class="kcode-banner-title">
                    <span>📊</span>
                    <span>تقرير وتصدير منتجات الشيت الرئيسي (Master CSV Exporter)</span>
                </h2>
                <p class="kcode-banner-desc">
                    قم بتحديد الأعمدة المطلوبة وتطبيق الفلاتر لتوليد تقرير إكسيل/CSV فريد ومطابق بنسبة 100% لشيت البيانات الرئيسي.
                </p>
            </div>
            <div>
                <button wire:click="exportReport" type="button" class="kcode-export-btn">
                    <svg class="kcode-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>تصدير التقرير النهائي (Excel / CSV)</span>
                </button>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="kcode-filter-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <h3 style="font-size: 1rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <span>🔍</span>
                    <span>تصفية وفلترة التقرير</span>
                </h3>
                <span style="font-size: 0.75rem; font-weight: 700; background: #e0e7ff; color: #3730a3; padding: 0.25rem 0.65rem; border-radius: 9999px;">
                    المتاح: {{ count($this->getMasterCsvRows()) }} منتج
                </span>
            </div>

            <div class="kcode-filter-grid">
                <div>
                    <label class="kcode-input-label">بحث برقم المنتج، اسم الماركة، أو العنوان</label>
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        placeholder="ابحث هنا (مثال: KCODE-P001 أو Medicube)..." 
                        class="kcode-input"
                    />
                </div>

                <div>
                    <label class="kcode-input-label">القسم الرئيسي (Category)</label>
                    <select wire:model.live="selectedCategory" class="kcode-input">
                        <option value="">جميع الأقسام</option>
                        @foreach($this->categoriesList as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="kcode-input-label">الماركة / البراند (Brand)</label>
                    <select wire:model.live="selectedBrand" class="kcode-input">
                        <option value="">جميع الماركات</option>
                        @foreach($this->brandsList as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Column Selection Control Center -->
        <div class="kcode-columns-card">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #e2e8f0;">
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span>⚙️</span>
                        <span>تحديد الأعمدة المخصصة للتقرير (Column Selection)</span>
                    </h3>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0 0;">
                        تم اختيار <strong>{{ count($selectedColumns) }}</strong> من إجمالي <strong>{{ count($allHeaders) }}</strong> عاموداً.
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <button wire:click="selectAllColumns" type="button" class="kcode-btn-sm kcode-btn-primary">
                        ✅ تحديد الكل (Select All)
                    </button>
                    <button wire:click="deselectAllColumns" type="button" class="kcode-btn-sm kcode-btn-danger">
                        ❌ إلغاء الكل (Deselect All)
                    </button>
                </div>
            </div>

            <div style="max-height: 380px; overflow-y: auto; padding-left: 0.5rem;">
                @php
                    $groups = [
                        '📌 1. البيانات الأساسية والمعرفات' => [
                            'product_id', 'sku_slug', 'brand_en', 'brand_ar', 'display_en_name', 'display_ar_name', 'short_ar_name', 'category', 'sub_category'
                        ],
                        '🧴 2. قواعد الروتين ومحرك الـ Quiz' => [
                            'product_step', 'layer_order', 'routine_time', 'is_core_routine_step', 'is_add_on', 'primary_concern', 'secondary_concern', 'tertiary_concern', 'skin_type_fit', 'best_for_ar', 'routine_tags', 'quiz_skin_type_tags', 'quiz_concern_tags', 'skin_code_tags', 'default_priority_score', 'same_step_choice_group', 'am_default', 'pm_default', 'selection_rule_ar'
                        ],
                        '📝 3. النصوص، الوصف، والاستخدام' => [
                            'ar_product_title_seo', 'en_product_title_seo', 'en_short_hook', 'ar_long_description', 'en_long_description', 'ar_key_benefits', 'en_key_benefits', 'texture_ar', 'texture_en', 'why_kcode_ar', 'why_kcode_en', 'how_to_use_ar', 'how_to_use_en', 'usage_frequency_ar', 'active_strength_level Low / Medium / High', 'safety_notes_ar', 'safety_notes_en', 'avoid_pairing_same_routine', 'developer_output_rule'
                        ],
                        '🔍 4. التسويق و الـ SEO ورابط المنتج' => [
                            'show_alternatives_button', 'remove_if_customer_has_it', 'source_url', 'data_confidence', 'needs_manual_check', 'seo_meta_title_ar', 'meta_description_en', 'meta_description_ar', 'primary_keyword_en', 'primary_keyword_ar', 'secondary_keywords_en', 'secondary_keywords_ar', 'final_url_slug', 'image_alt_en', 'image_alt_ar', 'og_title_ar', 'og_description_en', 'og_description_ar', 'pdp_headline_en', 'above_fold_hook_en', 'primary_badge_en', 'primary_badge_ar', 'result_promise_en', 'result_promise_ar', 'objection_answer_en', 'objection_answer_ar', 'routine_reason_en', 'routine_reason_ar', 'bundle_cta_en', 'bundle_cta_ar', 'add_to_cart_microcopy_en', 'add_to_cart_microcopy_ar'
                        ],
                        '⚙️ 5. قواعد التوصية المتقدمة والتضارب' => [
                            'max_default_products_per_step', 'selection_weight_formula_note', 'selection_priority_tie_breaker', 'exclusion_rule', 'conflict_rule_strictness', 'pairing_rule', 'alternative_button_rule', 'add_on_display_rule', 'routine_builder_note', 'fallback_product_rule', 'show_in_default_quiz_result', 'Keywords', 'Problem Tag', 'Result Tag', 'Conflict Rules', 'Routine Role'
                        ]
                    ];
                @endphp

                @foreach($groups as $groupTitle => $groupCols)
                    <div class="kcode-group-box">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; padding-bottom: 0.35rem; border-bottom: 1px solid #cbd5e1;">
                            <span style="font-weight: 800; font-size: 0.8rem; color: #1e293b;">{{ $groupTitle }}</span>
                            <button 
                                wire:click="toggleColumnGroup({{ json_encode($groupCols) }})" 
                                type="button" 
                                style="font-size: 0.7rem; font-weight: 700; color: #4f46e5; background: none; border: none; cursor: pointer; text-decoration: underline;"
                            >
                                تحديد/إلغاء المجموعة
                            </button>
                        </div>
                        <div class="kcode-cols-grid">
                            @foreach($groupCols as $col)
                                <label class="kcode-col-pill">
                                    <input 
                                        type="checkbox" 
                                        wire:model.live="selectedColumns" 
                                        value="{{ $col }}" 
                                    />
                                    <span title="{{ $col }}">{{ $col }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        <!-- Live Preview Table Card -->
        @php
            $rows = $this->getMasterCsvRows();
            $activeCols = array_values(array_intersect($allHeaders, $selectedColumns));
            if (empty($activeCols)) {
                $activeCols = $allHeaders;
            }
            $previewRows = array_slice($rows, 0, 10);
        @endphp

        <div class="kcode-filter-card">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 1rem;">
                <div>
                    <h3 style="font-size: 1rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span>👁️</span>
                        <span>المعاينة الحية للتقرير النهائي (Live Preview)</span>
                    </h3>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0.2rem 0 0 0;">
                        عرض أول 10 صفوف من التقرير حسب الفلاتر والأعمدة المختارة.
                    </p>
                </div>
                <button wire:click="exportReport" type="button" class="kcode-export-btn" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                    <svg class="kcode-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>تحميل التقرير الكامل ({{ count($rows) }} صف)</span>
                </button>
            </div>

            @if(count($rows) === 0)
                <div style="padding: 2rem; text-align: center; background: #f8fafc; border-radius: 0.75rem; border: 1px dashed #cbd5e1;">
                    <p style="font-size: 0.875rem; font-weight: 700; color: #64748b; margin: 0;">لا توجد نتائج مطابقة لخيارات الفلترة الحالية.</p>
                </div>
            @else
                <div class="kcode-table-container">
                    <table class="kcode-table">
                        <thead>
                            <tr>
                                <th style="text-align: center; width: 40px;">#</th>
                                @foreach($activeCols as $col)
                                    <th>{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewRows as $index => $row)
                                <tr>
                                    <td style="text-align: center; color: #94a3b8; font-family: monospace;">{{ $index + 1 }}</td>
                                    @foreach($activeCols as $col)
                                        <td title="{{ $row[$col] ?? '' }}">
                                            @if(empty($row[$col]))
                                                <span style="color: #cbd5e1; font-family: monospace;">—</span>
                                            @else
                                                {{ $row[$col] }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>

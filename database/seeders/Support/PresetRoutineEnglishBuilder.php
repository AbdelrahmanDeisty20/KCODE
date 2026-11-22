<?php

namespace Database\Seeders\Support;

use App\Models\PresetRoutine;
use App\Models\Product;

class PresetRoutineEnglishBuilder
{
    public const LEGACY_GENERIC_WOMEN_HOW_TO_USE = 'Follow the steps in order. In the morning, always finish with sunscreen. In the evening, focus on hydration and follow the dedicated night schedule.';

    public static function containsArabic(?string $text): bool
    {
        if ($text === null || $text === '') {
            return false;
        }
        return (bool) preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}]/u', $text);
    }

    public static function needsEnglishText(?string $currentEn, ?string $slug = null, bool $allowSlugMatch = false): bool
    {
        if ($currentEn === null || trim($currentEn) === '') {
            return true;
        }
        $trimmed = trim($currentEn);
        if (self::containsArabic($trimmed)) {
            return true;
        }
        if ($allowSlugMatch && $slug !== null && strtolower($trimmed) === strtolower(trim($slug))) {
            return true;
        }
        return false;
    }

    public static function arrayHasArabicOrEmpty(?array $items): bool
    {
        if (!is_array($items) || empty($items)) {
            return true;
        }
        foreach ($items as $item) {
            if (self::containsArabic((string) $item)) {
                return true;
            }
        }
        return false;
    }

    public static function routineKey(bool $isForMen, ?string $slug): string
    {
        return ($isForMen ? 'men:' : 'women:') . strtolower(trim((string) $slug));
    }

    /**
     * Enrich a women routine seed config with complete English fields before create().
     */
    public static function enrichWomenConfig(array $config): array
    {
        $slug = (string) ($config['slug'] ?? '');
        $copy = self::routineCopyMap()[self::routineKey(false, $slug)] ?? [];

        foreach ([
            'title_en', 'subtitle_en', 'description_en', 'badge_en',
            'skin_type_en', 'goal_en', 'card_note_en', 'audience_en', 'how_to_use_en',
        ] as $field) {
            $current = $config[$field] ?? null;
            $isGoalSlug = $field === 'goal_en';
            $isLegacyHowToUse = $field === 'how_to_use_en' && trim((string) $current) === self::LEGACY_GENERIC_WOMEN_HOW_TO_USE;

            if ((self::needsEnglishText($current, $slug, $isGoalSlug) || $isLegacyHowToUse) && !empty($copy[$field])) {
                $config[$field] = $copy[$field];
            }
        }

        if (!empty($copy['timing_en'])) {
            $timing = is_array($config['timing_json'] ?? null) ? $config['timing_json'] : [];
            $timing['am_en'] = self::needsEnglishText($timing['am_en'] ?? null) ? $copy['timing_en']['am'] : $timing['am_en'];
            $timing['pm_en'] = self::needsEnglishText($timing['pm_en'] ?? null) ? $copy['timing_en']['pm'] : $timing['pm_en'];
            $config['timing_json'] = $timing;
        }

        if (!empty($copy['benefits_en'])) {
            $config['benefits_en_json'] = $copy['benefits_en'];
        }
        if (!empty($copy['what_it_offers_en'])) {
            $config['what_it_offers_en_json'] = $copy['what_it_offers_en'];
        }
        if (!empty($config['usage_guide_json']) && is_array($config['usage_guide_json'])) {
            $config['usage_guide_json'] = self::enrichWomenUsageGuide($config['usage_guide_json'], $slug);
        }
        if (!empty($config['complementary_products_json']) && is_array($config['complementary_products_json'])) {
            $config['complementary_products_json'] = self::enrichComplementary($config['complementary_products_json']);
        }
        if (!empty($config['items']) && is_array($config['items'])) {
            $config['items'] = array_map(fn ($item) => self::enrichWomenItem($item), $config['items']);
        }

        return $config;
    }

    /**
     * Enrich a men routine seed config with complete English fields before create().
     */
    public static function enrichMenConfig(array $config): array
    {
        $slug = (string) ($config['slug'] ?? '');
        $copy = self::routineCopyMap()[self::routineKey(true, $slug)] ?? [];

        foreach ([
            'title_en', 'subtitle_en', 'description_en', 'badge_en',
            'skin_type_en', 'goal_en', 'card_note_en', 'audience_en', 'how_to_use_en',
        ] as $field) {
            $current = $config[$field] ?? null;
            if (self::needsEnglishText($current) && !empty($copy[$field])) {
                $config[$field] = $copy[$field];
            }
        }

        if (!empty($copy['timing_en'])) {
            $timing = is_array($config['timing_json'] ?? null) ? $config['timing_json'] : [];
            $timing['am_en'] = self::needsEnglishText($timing['am_en'] ?? null) ? $copy['timing_en']['am'] : $timing['am_en'];
            $timing['pm_en'] = self::needsEnglishText($timing['pm_en'] ?? null) ? $copy['timing_en']['pm'] : $timing['pm_en'];
            $config['timing_json'] = $timing;
        }

        if (!empty($copy['benefits_en'])) {
            $config['benefits_en_json'] = $copy['benefits_en'];
        }
        if (!empty($copy['what_it_offers_en'])) {
            $config['what_it_offers_en_json'] = $copy['what_it_offers_en'];
        }
        if (!empty($config['usage_guide_json']) && is_array($config['usage_guide_json'])) {
            $config['usage_guide_json'] = self::enrichMenUsageGuide($config['usage_guide_json'], $slug);
        }
        if (!empty($config['steps']) && is_array($config['steps'])) {
            $config['steps'] = array_map(fn ($step) => self::enrichMenStep($step), $config['steps']);
        }

        return $config;
    }

    /**
     * Safely backfill an existing DB PresetRoutine row + items without wiping custom Dashboard edits.
     */
    public static function backfillRoutine(PresetRoutine $routine): bool
    {
        $slug = (string) $routine->slug;
        $isForMen = (bool) $routine->is_for_men;
        $copy = self::routineCopyMap()[self::routineKey($isForMen, $slug)] ?? [];
        $dirty = false;

        foreach ([
            'title_en', 'subtitle_en', 'description_en', 'badge_en',
            'skin_type_en', 'goal_en', 'card_note_en', 'audience_en', 'how_to_use_en',
        ] as $field) {
            $current = $routine->{$field};
            $isGoalSlug = !$isForMen && $field === 'goal_en';
            $isLegacyHowToUse = !$isForMen && $field === 'how_to_use_en' && trim((string) $current) === self::LEGACY_GENERIC_WOMEN_HOW_TO_USE;

            if ((self::needsEnglishText($current, $slug, $isGoalSlug) || $isLegacyHowToUse) && !empty($copy[$field])) {
                $routine->{$field} = $copy[$field];
                $dirty = true;
            }
        }

        if (self::arrayHasArabicOrEmpty($routine->benefits_en_json)) {
            $benefitsEn = !empty($copy['benefits_en'])
                ? $copy['benefits_en']
                : self::translateBulletList($routine->benefits_json ?? []);
            if (!empty($benefitsEn)) {
                $routine->benefits_en_json = $benefitsEn;
                $dirty = true;
            }
        }

        if (self::arrayHasArabicOrEmpty($routine->what_it_offers_en_json)) {
            $offersEn = !empty($copy['what_it_offers_en'])
                ? $copy['what_it_offers_en']
                : self::translateBulletList($routine->what_it_offers_json ?? []);
            if (!empty($offersEn)) {
                $routine->what_it_offers_en_json = $offersEn;
                $dirty = true;
            }
        }

        $timing = is_array($routine->timing_json) ? $routine->timing_json : [];
        if (!empty($timing) || !empty($copy['timing_en'])) {
            $updatedTiming = $timing;
            if (self::needsEnglishText($updatedTiming['am_en'] ?? null)) {
                $amEn = $copy['timing_en']['am'] ?? self::translatePhrase((string) ($updatedTiming['am'] ?? ''));
                if ($amEn !== null) {
                    $updatedTiming['am_en'] = $amEn;
                }
            }
            if (self::needsEnglishText($updatedTiming['pm_en'] ?? null)) {
                $pmEn = $copy['timing_en']['pm'] ?? self::translatePhrase((string) ($updatedTiming['pm'] ?? ''));
                if ($pmEn !== null) {
                    $updatedTiming['pm_en'] = $pmEn;
                }
            }
            if ($updatedTiming !== $timing) {
                $routine->timing_json = $updatedTiming;
                $dirty = true;
            }
        }

        $cover = is_array($routine->cover_json) ? $routine->cover_json : [];
        if (!empty($cover) && self::needsEnglishText($cover['alt_en'] ?? null)) {
            $altEn = $copy['cover_alt_en'] ?? $routine->title_en;
            if (!empty($altEn) && !self::containsArabic($altEn)) {
                $cover['alt_en'] = $altEn;
                $routine->cover_json = $cover;
                $dirty = true;
            }
        }

        $guide = is_array($routine->usage_guide_json) ? $routine->usage_guide_json : [];
        if (!empty($guide)) {
            $updatedGuide = $isForMen
                ? self::enrichMenUsageGuide($guide, $slug)
                : self::enrichWomenUsageGuide($guide, $slug);
            if ($updatedGuide !== $guide) {
                $routine->usage_guide_json = $updatedGuide;
                $dirty = true;
            }
        }

        $comp = is_array($routine->complementary_products_json) ? $routine->complementary_products_json : [];
        if (!empty($comp)) {
            $updatedComp = self::enrichComplementary($comp);
            if ($updatedComp !== $comp) {
                $routine->complementary_products_json = $updatedComp;
                $dirty = true;
            }
        }

        if ($dirty) {
            $routine->save();
        }

        $anyItemDirty = false;
        $routine->loadMissing('items.product');

        foreach ($routine->items as $item) {
            $itemDirty = false;
            $prod = $item->product;

            if (self::needsEnglishText($item->step_name_en)) {
                $stepNameEn = self::translateRole((string) $item->step_name_ar)
                    ?? ($prod && !self::containsArabic($prod->role_en ?? '') ? $prod->role_en : null)
                    ?? ('Step ' . $item->display_order);
                $item->step_name_en = $stepNameEn;
                $itemDirty = true;
            }

            if (self::needsEnglishText($item->use_time_en)) {
                $useTimeEn = self::translateUseTime((string) $item->use_time_ar, (bool) $item->morning, (bool) $item->night);
                $item->use_time_en = $useTimeEn;
                $itemDirty = true;
            }

            if (self::needsEnglishText($item->frequency_en)) {
                $freqEn = self::translateFrequency((string) $item->frequency_ar)
                    ?? ($prod && !self::containsArabic($prod->usage_frequency_en ?? '') ? $prod->usage_frequency_en : null)
                    ?? 'Daily.';
                $item->frequency_en = $freqEn;
                $itemDirty = true;
            }

            if (self::needsEnglishText($item->application_en) && !empty($item->application_ar)) {
                $appEn = self::translateApplication((string) $item->application_ar);
                if ($appEn !== null) {
                    $item->application_en = $appEn;
                    $itemDirty = true;
                }
            }

            if (self::needsEnglishText($item->starter_guide_en) && !empty($item->starter_guide_ar)) {
                $starterEn = self::translateStarterGuide((string) $item->starter_guide_ar);
                if ($starterEn !== null) {
                    $item->starter_guide_en = $starterEn;
                    $itemDirty = true;
                }
            }

            if (self::needsEnglishText($item->reason_en) && (!empty($item->reason_ar) || ($prod && !empty($prod->brief_insight_en)))) {
                $reasonEn = self::translatePhrase((string) ($item->reason_ar ?? ''))
                    ?? ($prod && !self::containsArabic($prod->brief_insight_en ?? '') ? $prod->brief_insight_en : null);
                if ($reasonEn !== null) {
                    $item->reason_en = $reasonEn;
                    $itemDirty = true;
                }
            }

            if ($itemDirty) {
                $item->save();
                $anyItemDirty = true;
            }
        }

        return $dirty || $anyItemDirty;
    }

    public static function enrichWomenItem(array $item): array
    {
        if (self::needsEnglishText($item['step_name_en'] ?? null)) {
            $item['step_name_en'] = self::translateRole((string) ($item['step_name_ar'] ?? '')) ?? ('Step ' . ($item['order'] ?? 1));
        }
        if (self::needsEnglishText($item['use_time_en'] ?? null)) {
            $item['use_time_en'] = self::translateUseTime(
                (string) ($item['use_time_ar'] ?? ''),
                (bool) ($item['morning'] ?? true),
                (bool) ($item['night'] ?? true)
            );
        }
        if (self::needsEnglishText($item['frequency_en'] ?? null)) {
            $item['frequency_en'] = self::translateFrequency((string) ($item['frequency_ar'] ?? '')) ?? 'Daily.';
        }
        if (self::needsEnglishText($item['application_en'] ?? null) && !empty($item['application_ar'])) {
            $appEn = self::translateApplication((string) $item['application_ar']);
            if ($appEn !== null) {
                $item['application_en'] = $appEn;
            }
        }
        if (self::needsEnglishText($item['starter_guide_en'] ?? null) && !empty($item['starter_guide_ar'])) {
            $starterEn = self::translateStarterGuide((string) $item['starter_guide_ar']);
            if ($starterEn !== null) {
                $item['starter_guide_en'] = $starterEn;
            }
        }
        if (self::needsEnglishText($item['reason_en'] ?? null) && !empty($item['reason_ar'] ?? $item['support_copy'] ?? null)) {
            $reasonEn = self::translatePhrase((string) ($item['reason_ar'] ?? $item['support_copy']));
            if ($reasonEn !== null) {
                $item['reason_en'] = $reasonEn;
            }
        }

        return $item;
    }

    public static function enrichMenStep(array $step): array
    {
        if (self::needsEnglishText($step['role_en'] ?? null)) {
            $step['role_en'] = self::translateRole((string) ($step['role_ar'] ?? '')) ?? ('Step ' . ($step['order'] ?? 1));
        }
        if (self::needsEnglishText($step['use_time_en'] ?? null)) {
            $step['use_time_en'] = self::translateUseTime(
                (string) ($step['use_time_ar'] ?? ''),
                (bool) ($step['morning'] ?? true),
                (bool) ($step['night'] ?? true)
            );
        }
        if (self::needsEnglishText($step['frequency_en'] ?? null)) {
            $step['frequency_en'] = self::translateFrequency((string) ($step['frequency_ar'] ?? '')) ?? 'Daily.';
        }
        if (self::needsEnglishText($step['application_en'] ?? null) && !empty($step['application_ar'])) {
            $appEn = self::translateApplication((string) $step['application_ar']);
            if ($appEn !== null) {
                $step['application_en'] = $appEn;
            }
        }

        return $step;
    }

    public static function enrichWomenUsageGuide(array $guide, string $slug): array
    {
        $copy = self::routineCopyMap()[self::routineKey(false, $slug)] ?? [];

        if (self::needsEnglishText($guide['am_title_en'] ?? null)) {
            $guide['am_title_en'] = 'Morning Steps';
        }
        if (self::needsEnglishText($guide['pm_title_en'] ?? null)) {
            $guide['pm_title_en'] = 'Evening Steps';
        }
        if (self::needsEnglishText($guide['pm_standard_label_en'] ?? null)) {
            $guide['pm_standard_label_en'] = 'Regular Care Night';
        }
        if (self::needsEnglishText($guide['night_notes_en'] ?? null)) {
            $guide['night_notes_en'] = $copy['card_note_en']
                ?? self::translatePhrase((string) ($guide['night_notes'] ?? ''))
                ?? ($guide['night_notes_en'] ?? null);
        }

        if (!empty($guide['am_steps']) && is_array($guide['am_steps'])) {
            $guide['am_steps'] = array_map(fn ($s) => is_array($s) ? self::enrichWomenGuideStep($s) : $s, $guide['am_steps']);
        }
        if (!empty($guide['pm_standard_steps']) && is_array($guide['pm_standard_steps'])) {
            $guide['pm_standard_steps'] = array_map(fn ($s) => is_array($s) ? self::enrichWomenGuideStep($s) : $s, $guide['pm_standard_steps']);
        }
        if (!empty($guide['pm_variants']) && is_array($guide['pm_variants'])) {
            $variantTranslations = $copy['variants'] ?? [];
            $guide['pm_variants'] = array_map(function ($variant) use ($variantTranslations) {
                if (!is_array($variant)) {
                    return $variant;
                }
                $id = (string) ($variant['id'] ?? '');
                $vCopy = $variantTranslations[$id] ?? [];

                if (self::needsEnglishText($variant['label_en'] ?? null)) {
                    $variant['label_en'] = $vCopy['label_en'] ?? self::translatePhrase((string) ($variant['label'] ?? '')) ?? 'Special Care Night';
                }
                if (self::needsEnglishText($variant['badge_en'] ?? null)) {
                    $variant['badge_en'] = $vCopy['badge_en'] ?? self::translatePhrase((string) ($variant['badge'] ?? '')) ?? '';
                }
                if (self::needsEnglishText($variant['note_en'] ?? null)) {
                    $variant['note_en'] = $vCopy['note_en'] ?? self::translatePhrase((string) ($variant['note'] ?? '')) ?? '';
                }
                if (!empty($variant['steps']) && is_array($variant['steps'])) {
                    $variant['steps'] = array_map(fn ($s) => is_array($s) ? self::enrichWomenGuideStep($s) : $s, $variant['steps']);
                }
                return $variant;
            }, $guide['pm_variants']);
        }

        return $guide;
    }

    public static function enrichMenUsageGuide(array $guide, string $slug): array
    {
        $copy = self::routineCopyMap()[self::routineKey(true, $slug)] ?? [];

        if (!empty($guide['morning_steps']) && is_array($guide['morning_steps'])) {
            $guide['morning_steps'] = array_map(fn ($s) => is_array($s) ? self::enrichMenGuideStep($s) : $s, $guide['morning_steps']);
        }
        if (!empty($guide['evening_variants']) && is_array($guide['evening_variants'])) {
            $guide['evening_variants'] = array_map(function ($variant) {
                if (!is_array($variant)) {
                    return $variant;
                }
                if (self::needsEnglishText($variant['label_en'] ?? null)) {
                    $variant['label_en'] = (($variant['id'] ?? '') === 'default')
                        ? 'Regular Evenings'
                        : (self::translatePhrase((string) ($variant['label_ar'] ?? '')) ?? 'Special Evenings');
                }
                if (!empty($variant['steps']) && is_array($variant['steps'])) {
                    $variant['steps'] = array_map(fn ($s) => is_array($s) ? self::enrichMenGuideStep($s) : $s, $variant['steps']);
                }
                return $variant;
            }, $guide['evening_variants']);
        }

        if (self::arrayHasArabicOrEmpty($guide['notes_en'] ?? null)) {
            $notesEn = !empty($copy['notes_en'])
                ? $copy['notes_en']
                : self::translateBulletList($guide['notes_ar'] ?? []);
            if (!empty($notesEn)) {
                $guide['notes_en'] = $notesEn;
            }
        }

        return $guide;
    }

    private static function enrichWomenGuideStep(array $step): array
    {
        if (self::needsEnglishText($step['role_en'] ?? null)) {
            $step['role_en'] = self::translateRole((string) ($step['role'] ?? '')) ?? ('Step ' . ($step['step_number'] ?? 1));
        }
        if (self::needsEnglishText($step['application_en'] ?? null) && !empty($step['application'])) {
            $appEn = self::translateApplication((string) $step['application']);
            if ($appEn !== null) {
                $step['application_en'] = $appEn;
            }
        }
        if (self::needsEnglishText($step['frequency_en'] ?? null) && !empty($step['frequency'])) {
            $freqEn = self::translateFrequency((string) $step['frequency']);
            if ($freqEn !== null) {
                $step['frequency_en'] = $freqEn;
            }
        }
        if (self::needsEnglishText($step['starter_guide_en'] ?? null)) {
            $starterEn = !empty($step['starter_guide'])
                ? self::translateStarterGuide((string) $step['starter_guide'])
                : '';
            if ($starterEn !== null) {
                $step['starter_guide_en'] = $starterEn;
            }
        }

        return $step;
    }

    private static function enrichMenGuideStep(array $step): array
    {
        if (self::needsEnglishText($step['role_en'] ?? null)) {
            $step['role_en'] = self::translateRole((string) ($step['role_ar'] ?? '')) ?? ('Step ' . ($step['step_number'] ?? 1));
        }
        if (self::needsEnglishText($step['instruction_en'] ?? null) && !empty($step['instruction_ar'])) {
            $instEn = self::translateApplication((string) $step['instruction_ar']);
            if ($instEn !== null) {
                $step['instruction_en'] = $instEn;
            }
        }

        return $step;
    }

    public static function enrichComplementary(array $items): array
    {
        $bySku = self::complementarySkuDefaults();

        return array_map(function ($comp) use ($bySku) {
            if (!is_array($comp)) {
                return $comp;
            }
            $sku = (string) ($comp['sku'] ?? '');
            $defaults = $bySku[$sku] ?? [];

            if (self::needsEnglishText($comp['step_en'] ?? null)) {
                $comp['step_en'] = $defaults['step_en']
                    ?? self::translateRole((string) ($comp['step'] ?? ''))
                    ?? 'Complementary Product';
            }

            if (self::needsEnglishText($comp['reason_en'] ?? null)) {
                $rawReason = (string) ($comp['reason'] ?? $comp['why'] ?? $comp['support_copy'] ?? '');
                $translated = self::complementaryPhraseMap()[trim($rawReason)]
                    ?? self::translatePhrase($rawReason)
                    ?? $defaults['reason_en']
                    ?? null;
                if ($translated !== null) {
                    $comp['reason_en'] = $translated;
                }
            }

            if (self::needsEnglishText($comp['application_en'] ?? null)) {
                $rawApp = (string) ($comp['application'] ?? '');
                $translatedApp = self::complementaryPhraseMap()[trim($rawApp)]
                    ?? self::translateApplication($rawApp)
                    ?? $defaults['application_en']
                    ?? null;
                if ($translatedApp !== null) {
                    $comp['application_en'] = $translatedApp;
                }
            }

            if (self::needsEnglishText($comp['note_en'] ?? null) && !empty($comp['note'])) {
                $noteEn = self::translatePhrase((string) $comp['note']);
                if ($noteEn !== null) {
                    $comp['note_en'] = $noteEn;
                }
            }

            return $comp;
        }, $items);
    }

    public static function translateRole(?string $ar): ?string
    {
        $k = trim((string) $ar);
        if ($k === '') {
            return null;
        }
        if (!self::containsArabic($k)) {
            return $k;
        }

        $map = [
            'الغسول' => 'Cleanser',
            'غسول' => 'Cleanser',
            'التونر' => 'Toner',
            'تونر' => 'Toner',
            'الإسنس' => 'Essence',
            'الخلاصة المرطبة' => 'Hydrating Essence',
            'السيروم' => 'Serum',
            'سيروم' => 'Serum',
            'سيروم التهدئة' => 'Soothing Serum',
            'سيروم النضارة' => 'Glow Serum',
            'سيروم التنعيم' => 'Softening Serum',
            'سيروم التفتيح والتجديد' => 'Active Treatment Serum',
            'سيروم التجديد والتفتيح' => 'Active Treatment Serum',
            'سيروم التمهيد' => 'Booster Serum',
            'السيروم التمهيدي' => 'Booster Serum',
            'سيروم العين بالريتينال' => 'Eye Retinal Serum',
            'سيروم العين' => 'Eye Serum',
            'المقشّر' => 'Exfoliator',
            'المقشّر (BHA)' => 'Exfoliator (BHA)',
            'المقشر' => 'Exfoliator',
            'المرطب' => 'Moisturizer',
            'مرطب' => 'Moisturizer',
            'المرطب الخفيف' => 'Gel Moisturizer',
            'المرطب المهدئ' => 'Calming Moisturizer',
            'المرطب الداعم للحاجز' => 'Barrier Cream',
            'المرطب الغني' => 'Rich Cream',
            'كريم شد البشرة' => 'Firming Cream',
            'المرطب الشامل' => 'All-In-One Cream',
            'واقي الشمس' => 'Sunscreen',
            'واقي شمس' => 'Sunscreen',
            'واقي الشمس المائي' => 'Aqua Sunscreen',
            'واقي الشمس الشفاف' => 'Clear Sun Essence',
            'واقي الشمس المغذي' => 'Relief Sunscreen',
            'علاج خفيف' => 'Light Treatment',
            'بعد الحلاقة' => 'Post-Shave Care',
        ];

        return $map[$k] ?? null;
    }

    public static function translateUseTime(?string $ar, bool $morning, bool $night): string
    {
        $k = trim((string) $ar);
        $map = [
            'صباحاً ومساءً' => 'Morning & Evening',
            'صباحًا ومساءً' => 'Morning & Evening',
            'صباحاً فقط' => 'Morning only',
            'صباحًا فقط' => 'Morning only',
            'صباحاً' => 'Morning',
            'صباحًا' => 'Morning',
            'مساءً فقط' => 'Evening only',
            'مساءً' => 'Evening',
        ];
        if (isset($map[$k])) {
            return $map[$k];
        }
        if ($morning && $night) {
            return 'Morning & Evening';
        }
        return $morning ? 'Morning only' : 'Evening only';
    }

    public static function translateFrequency(?string $ar): ?string
    {
        $k = trim((string) $ar);
        if ($k === '') {
            return null;
        }
        if (!self::containsArabic($k)) {
            return $k;
        }

        $map = [
            'يوميًا.' => 'Daily.',
            'يومياً.' => 'Daily.',
            'يوميًا' => 'Daily',
            'يومياً' => 'Daily',
            'صباحًا.' => 'Morning.',
            'صباحاً.' => 'Morning.',
            'صباحًا ومساءً.' => 'Morning & evening.',
            'صباحاً ومساءً.' => 'Morning & evening.',
            'صباحًا ومساءً، في خطوة الترطيب.' => 'Morning & evening at the moisturizing step.',
            'صباحًا؛ أعد التطبيق كل ساعتين عند البقاء خارج المنزل، وبعد السباحة أو التعرّق.' => 'In the morning; reapply every 2 hours when outdoors and after swimming or sweating.',
            'ابدأ مرة أو مرتين أسبوعيًا.' => 'Start once or twice a week.',
            'مساءً، مرتين إلى ثلاث مرات أسبوعيًا مع التدرّج.' => 'Evenings, 2–3 times weekly, building up gradually.',
            'ابدأ ليلة بعد ليلة، ثم يوميًا بعد التدرّج.' => 'Start every other night, then move to daily as skin adjusts.',
            'لمحيط العين: ابدأ مرة كل ثلاث ليالٍ.' => 'Eye area: start once every three nights.',
            'مساءً؛ ويمكن استخدامه صباحًا عند الحاجة قبل واقي الشمس.' => 'Evenings; may also be used in the morning if needed before sunscreen.',
            'مساءً: ابدأ بالسيروم كل يومين خلال الأسبوع الأول، ثم استخدمه يوميًا إذا بقيت البشرة مرتاحة.' => 'Evenings: start every other day during the first week, then daily if skin remains comfortable.',
            'بعد الحلاقة فقط.' => 'After shaving only.',
        ];

        return $map[$k] ?? null;
    }

    public static function translateApplication(?string $ar): ?string
    {
        $k = trim((string) $ar);
        if ($k === '') {
            return null;
        }
        if (!self::containsArabic($k)) {
            return $k;
        }

        $map = [
            'كوّن رغوة بكمية مناسبة بين كفّين مبلّلتين، دلّك الوجه بلطف، ثم اشطفه بالماء الفاتر.'
                => 'Work an appropriate amount into a lather between wet palms, massage gently over the face, and rinse with lukewarm water.',
            'ابدأ بقطرة إلى قطرتين في كل استخدام، بعد التونر وقبل المرطب.'
                => 'Start with 1–2 drops per application after toner and before moisturizer.',
            'استخدمه بعد الغسول على بشرة جافة، واتركه حتى تمتصّه البشرة قبل الانتقال إلى المرطب.'
                => 'Apply after cleansing on dry skin and let it absorb before moving on to moisturizer.',
            'وزّعه بالتساوي في خطوة الترطيب، ثم ربّت بلطف حتى تمتصّه البشرة.'
                => 'Apply evenly at the moisturizing step and gently pat until absorbed.',
            'وزّعه على الوجه والرقبة والأجزاء المكشوفة قبل الخروج بنحو 15 دقيقة.'
                => 'Apply generously to the face, neck, and exposed skin about 15 minutes before sun exposure.',
            'بعد التنظيف والتونر إن استخدمته، ضع بضع قطرات ووزّعها بلطف، ثم اتبع بالمرطب.'
                => 'After cleansing (and toner if used), apply a few drops evenly and follow with moisturizer.',
            'وزّع كمية بحجم قطعة نقدية صغيرة على الوجه في خطوة الترطيب، ثم ربّت بلطف حتى الامتصاص.'
                => 'Apply a coin-sized amount over the face at the moisturizing step and pat gently until absorbed.',
            'بعد الغسول، وزّعه براحة اليد أو بقطعة قطن، ثم ربّت بلطف حتى الامتصاص.'
                => 'After cleansing, apply with your palms or a cotton pad and pat gently until absorbed.',
            'بعد التنظيف والتونر إن استخدمته، وزّع كمية مناسبة على الوجه وربّت بلطف، ثم اتبع بالمرطب.'
                => 'After cleansing (and toner if used), smooth an appropriate amount onto the face, pat gently, and follow with moisturizer.',
            'وزّع كمية مناسبة على الوجه في خطوة الترطيب صباحًا ومساءً، ثم ربّت حتى الامتصاص.'
                => 'Apply an appropriate amount to the face at the moisturizing step morning and evening, then pat until absorbed.',
            'بعد التونر، ضع قطرتين إلى ثلاث قطرات وربّت حتى الامتصاص. استخدمه صباحًا أو في ليلة لا تستخدم فيها Retino-Mela.'
                => 'After toning, apply 2–3 drops and pat until absorbed. Use in the morning or on evenings when not using Retino-Mela.',
            'مساءً وعلى بشرة جافة، ضع كمية صغيرة بعد التونر وقبل المرطب، مع تجنّب محيط العين والشفاه.'
                => 'At night on dry skin, apply a small amount after toner and before moisturizer, avoiding the eye and lip contours.',
            'بعد الغسول والتونر إن استخدمته، وزّع كمية صغيرة، نحو ضغطتين إلى ثلاث، على الوجه وربّت بلطف حتى الامتصاص؛ تجنّب منطقة العينين.'
                => 'After cleansing (and toner if used), apply a small amount (about 2–3 pumps) over the face and pat gently until absorbed; avoid the eye area.',
            'وزّع كمية مناسبة على الوجه في خطوة الترطيب صباحًا ومساءً، ثم ربّت حتى الامتصاص.'
                => 'Apply an appropriate amount over the face morning and evening at the moisturizing step and pat until absorbed.',
            'بعد التنظيف مباشرةً، وزّع مضختين إلى ثلاث مضخات على الوجه وربّت حتى الامتصاص، ثم أكمل بقية الروتين.'
                => 'Immediately after cleansing, apply 2–3 pumps over the face, pat until absorbed, and continue with the rest of your routine.',
            'مساءً، استخدم ضغطة إلى ضغطتين، وابدأ بكمية أقل للبشرة الحساسة. ربّت برفق تحت العينين وحولهما بعد التنظيف والتونر إن استخدمته، ثم ضع المرطب. للبشرة الجافة أو الحساسة، ضع مرطبًا لطيفًا قبله. تجنّب ملامسة العينين.'
                => 'In the evening, use 1–2 pumps (less for sensitive skin). Gently tap under and around the eyes after cleansing and toning, then follow with moisturizer. For dry or sensitive skin, buffer with a gentle moisturizer first. Avoid direct eye contact.',
            'مساءً فقط، بعد التنظيف والتونر إن استخدمته، ضع بضع قطرات على يدين نظيفتين ووزّعها على الوجه وربّت بلطف، ثم اتبع بالمرطب.'
                => 'Preferably at night after cleansing (and toner if used), dispense a few drops onto clean hands, spread across the face, pat gently, and follow with moisturizer.',
            // Men guide instructions & steps:
            'اغسل وجهك بالغسول.' => 'Wash your face with the cleanser.',
            'ضع طبقة متساوية من واقي الشمس.' => 'Apply an even layer of sunscreen.',
            'ضع المرطب الجل.' => 'Apply the gel moisturizer.',
            'ضع كمية خفيفة من المرطب.' => 'Apply a light layer of moisturizer.',
            'ضع واقي الشمس بعد امتصاص المرطب.' => 'Apply sunscreen after the moisturizer absorbs.',
            'ضع المرطب.' => 'Apply moisturizer.',
            'ضع كمية خفيفة من المرطب. | ضع المرطب.' => 'Apply a light layer of moisturizer in the morning and regular amount at night.',
            'ضع 2–3 قطرات من السيروم.' => 'Apply 2–3 drops of serum.',
            'ضع المرطب بعد امتصاص السيروم.' => 'Apply moisturizer after the serum absorbs.',
            'اغسل وجهك، ثم احلق باتجاه نمو الشعر باستخدام كريم أو جل حلاقة، دون شدّ الجلد.'
                => 'Wash your face, then shave in the direction of hair growth using a shaving cream or gel without pulling the skin.',
            'اغسل وجهك، ثم احلق باتجاه نمو الشعر باستخدام كريم أو جل حلاقة، دون شدّ الجلد. | اغسل وجهك بالغسول.'
                => 'Morning: wash and shave in the direction of hair growth without pulling the skin. Evening: wash with the cleanser.',
            'ضع الأمبول بعد الحلاقة.' => 'Apply the ampoule after shaving.',
            'ضع واقي الشمس بعد امتصاص الأمبول.' => 'Apply sunscreen after the ampoule absorbs.',
        ];

        return $map[$k] ?? null;
    }

    public static function translateStarterGuide(?string $ar): ?string
    {
        $k = trim((string) $ar);
        if ($k === '') {
            return '';
        }
        if (!self::containsArabic($k)) {
            return $k;
        }

        $map = [
            'ابدأ بقطرة إلى قطرتين في كل استخدام بعد التونر وقبل المرطب.'
                => 'Start with 1–2 drops per application after toner and before moisturizer.',
            'ابدأ استخدامه مرة واحدة أسبوعياً مساءً لملاحظة استجابة البشرة، ثم زد التكرار تدريجياً حتى 2–3 مرات أسبوعياً.'
                => 'Start once a week in the evening to gauge skin tolerance, then gradually increase to 2–3 times weekly.',
            'ابدأ ليلة بعد ليلة بكمية بحجم حبة البازلاء مساءً فقط، ثم تدرج إلى الاستخدام اليومي بعد التأقلم.'
                => 'Start every other night using a pea-sized amount in the evening only, then build up to nightly use once acclimated.',
            'لمحيط العين: ابدأ مرة كل 3 ليالٍ مساءً، ثم زد التكرار إلى ليلة بعد ليلة حسب التحمل.'
                => 'For the eye area: start once every 3 nights in the evening, then increase to every other night as tolerated.',
        ];

        return $map[$k] ?? null;
    }

    private static function translateBulletList(array $list): array
    {
        $translated = [];
        foreach ($list as $item) {
            $text = trim((string) $item);
            if ($text === '') {
                continue;
            }
            if (!self::containsArabic($text)) {
                $translated[] = $text;
                continue;
            }
            $mapped = self::translatePhrase($text);
            if ($mapped !== null) {
                $translated[] = $mapped;
            }
        }
        return $translated;
    }

    private static function translatePhrase(?string $text): ?string
    {
        $k = trim((string) $text);
        if ($k === '') {
            return null;
        }
        if (!self::containsArabic($k)) {
            return $k;
        }

        $sharedMap = [
            'غسول ثم واقي شمس' => 'Cleanser, then sunscreen',
            'غسول ثم مرطب جل' => 'Cleanser, then gel moisturizer',
            'غسول، مرطب، ثم واقي شمس' => 'Cleanser, moisturizer, then sunscreen',
            'غسول ثم مرطب' => 'Cleanser, then moisturizer',
            'غسول، سيروم، ثم مرطب' => 'Cleanser, serum, then moisturizer',
            'غسول وحلاقة، أمبول، ثم واقي شمس' => 'Cleanse & shave, ampoule, then sunscreen',
            'غسول ثم مرطب؛ الأمبول بعد الحلاقة فقط' => 'Cleanser then moisturizer; ampoule after shaving only',
            'الليالي العادية' => 'Regular Evenings',
            'ليلة العناية المعتادة' => 'Regular Care Night',
        ];

        return $sharedMap[$k] ?? self::translateFrequency($k) ?? self::translateApplication($k);
    }

    private static function complementarySkuDefaults(): array
    {
        return [
            'KC0219' => [
                'step_en' => 'Micellar Water',
                'reason_en' => 'Helps remove sunscreen and makeup before cleansing in the evening when needed.',
                'application_en' => 'Soak a cotton pad with the cleansing water and gently wipe the face to remove makeup or sunscreen. Follow with water or cleanser if desired.',
            ],
            'KC0153' => [
                'step_en' => 'Acne Patch',
                'reason_en' => 'Applied directly onto active blemishes to help absorb impurities and shield them from picking or touching.',
                'application_en' => 'Clean and thoroughly dry the blemish, then stick a patch directly over it; replace once it turns white.',
            ],
            'KC0085' => [
                'step_en' => 'Pore Wash-Off Mask',
                'reason_en' => 'Used once or twice weekly to help clear excess sebum buildup and refine the look of pores.',
                'application_en' => 'On clean, damp skin, apply a thick even layer avoiding eyes, lips, and dry or irritated areas. Leave on for 5–10 minutes and rinse off before it dries and cracks.',
            ],
            'KC0218' => [
                'step_en' => 'Sun Stick (Reapplication)',
                'reason_en' => 'Designed for convenient matte SPF reapplication throughout the day; not a substitute for your primary morning sunscreen.',
                'application_en' => 'To reapply protection during the day, glide the stick 4 passes back and forth over each area and blend evenly across face, neck, and ears.',
            ],
            'KC0207' => [
                'step_en' => 'Overnight Barrier Mask',
                'reason_en' => 'Used 2–3 nights weekly in place of night moisturizer to replenish hydration and soothe dry feelings.',
                'application_en' => 'After watery evening steps, apply an even layer in place of your night moisturizer, leave overnight, and rinse in the morning.',
            ],
            'KC0189' => [
                'step_en' => 'Hydrogel Radiance Mask',
                'reason_en' => 'Weekly boost for dullness, radiance, and uneven skin tone; replaces your night moisturizer on evenings used.',
                'application_en' => 'After cleansing and toner or serum, smooth both mask pieces onto your face. Leave overnight or 3–4 hours during the day until transparent.',
            ],
            'KC0094' => [
                'step_en' => 'Cleansing Balm',
                'reason_en' => 'Melts away makeup and water-resistant sunscreen before your water-based cleanser when needed.',
                'application_en' => 'On dry face and hands, massage a suitable amount to dissolve makeup and sunscreen. Emulsify with a little water until milky, then rinse thoroughly.',
            ],
            'KC0184' => [
                'step_en' => 'Soothing Hydrogel Mask',
                'reason_en' => 'Weekly intensive treatment for deep moisture and skin-barrier comfort; replaces your night moisturizer when used.',
                'application_en' => 'After cleansing and toner or serum, apply both mask pieces onto the face. Leave overnight or 3–4 hours until transparent, then rinse/pat residual essence.',
            ],
            'KC0185' => [
                'step_en' => 'Collagen Hydrogel Mask',
                'reason_en' => 'Weekly boost for plump hydration, elasticity feel, and smoother skin; replaces your night moisturizer when used.',
                'application_en' => 'After cleansing and toner or serum, apply both mask pieces onto the face. Leave overnight or 3–4 hours until transparent; discontinue immediately if irritation occurs.',
            ],
            'KC0173' => [
                'step_en' => 'Hydrogel Eye Patches',
                'reason_en' => 'Use as needed to hydrate the under-eye area and refresh dark circles and puffiness on nights without retinal eye serum.',
                'application_en' => 'After cleansing (and toner if used), place one patch under each eye for 10–20 minutes, remove, and gently tap in remaining essence. Do not sleep wearing patches.',
            ],
        ];
    }

    private static function complementaryPhraseMap(): array
    {
        return [
            'يساعد على إزالة الواقي الشمسي والمكياج قبل الغسول عند الحاجة.'
                => 'Helps remove sunscreen and makeup before cleansing when needed.',
            'لإزالة الواقي الشمسي والمكياج قبل الغسول عند الحاجة، من دون إضافة خطوة تنظيف زيتية ثقيلة.'
                => 'Removes sunscreen and makeup before your cleanser when needed without adding a heavy oil-cleansing step.',
            'يساعد على إزالة الواقي الشمسي والمكياج قبل الغسول مساءً عند الحاجة.'
                => 'Helps remove sunscreen and makeup before your evening cleanser when needed.',
            'بلّل قطعة قطن بماء التنظيف وامسح الوجه بلطف لإزالة المكياج أو واقي الشمس. يمكنك شطفه أو استخدام الغسول المائي بعده عند الحاجة؛ لا يلزم غسول إضافي في كل مرة.'
                => 'Soak a cotton pad with the cleansing water and wipe the face gently to remove makeup or sunscreen. Rinse or follow with a water-based cleanser if desired.',
            'يُستخدم موضعياً على الحبة النشطة للمساعدة على امتصاص الإفرازات وحمايتها من اللمس.'
                => 'Apply as a spot treatment on active blemishes to help absorb impurities and protect from touching.',
            'نظّف الحبة وجففها جيدًا، ثم ضع اللصقة مباشرةً عليها؛ استبدلها عندما تصبح بيضاء.'
                => 'Cleanse and dry the blemish thoroughly, apply the patch directly over it, and replace once it turns white.',
            'يُستخدم مرة أو مرتين أسبوعياً للمساعدة على إزالة الدهون المتراكمة وتحسين مظهر المسام.'
                => 'Use 1–2 times weekly to help remove accumulated oil and refine the look of pores.',
            'على وجه نظيف ورطب، ضع طبقة سميكة ومتساوية بعيدًا عن العينين والشفاه والمناطق الجافة أو المتهيّجة. اتركها من 5 إلى 10 دقائق، ثم اشطفها قبل أن تجف وتتشقّق. للبشرة الحساسة، استخدمها على المناطق الدهنية فقط وأزلها بالماء دون فرك.'
                => 'Apply a thick, even layer on clean damp skin avoiding eyes, lips, and dry/irritated patches. Leave for 5–10 minutes and rinse off before it dries completely. For sensitive skin, apply only on oily zones and rinse without rubbing.',
            'مخصص لإعادة تطبيق الحماية خلال اليوم بلمسة مطفية؛ ولا يُعرض بديلاً عن واقي الشمس الأساسي صباحاً.'
                => 'Designed for matte sun protection reapplication during the day; does not replace your primary morning sunscreen.',
            'لتجديد الحماية خلال اليوم، مرّر الستيك أربع مرات ذهابًا وإيابًا على كل منطقة، ثم وزّع الطبقة برفق بالتساوي. غطّ الوجه والرقبة والأذنين وتجنّب العينين؛ التغطية الكافية قد تحرّك المكياج.'
                => 'Swipe 4 passes back and forth per zone and blend gently across the face, neck, and ears while avoiding eyes. Adequate coverage may shift makeup.',
            'يُستخدم ليلاً مرتين أو ثلاث مرات أسبوعياً بدلاً من المرطب الليلي، لمنح البشرة عناية ترطيبية إضافية.'
                => 'Use 2–3 nights weekly instead of your night moisturizer for extra overnight hydration support.',
            'بعد الخطوات المائية مساءً، ضع طبقة مناسبة بدل المرطب الليلي، واتركها طوال الليل ثم اغسل الوجه صباحًا.'
                => 'After watery evening steps, smooth an even layer instead of night cream, leave overnight, and rinse in the morning.',
            'عناية أسبوعية إضافية لتحسين مظهر البهتان والإشراقة وتفاوت اللون، ويحل محل المرطب في ليلة استخدامه.'
                => 'Weekly booster to brighten dullness and uneven tone; replaces moisturizer on nights you use it.',
            'عناية أسبوعية إضافية بالإشراقة والنعومة وتفاوت اللون، ويحل محل المرطب الليلي في ليلة استخدامه.'
                => 'Weekly boost for radiance, smoothness, and uneven tone; replaces your night moisturizer when used.',
            'بعد التنظيف والتونر أو السيروم، ثبّت جزأي الماسك على الوجه. يمكنك تركه طوال الليل؛ وللاستخدام نهارًا اتركه من 3 إلى 4 ساعات أو حتى يصبح شفافًا. أزله وأضف مرطبًا إذا بقي الجفاف، واستخدم واقي الشمس صباحًا. أزله فورًا إذا سبّب حكة أو لسعًا.'
                => 'After cleansing and toner or serum, apply both mask pieces onto your face. Leave overnight, or 3–4 hours in the day until clear. Follow with moisturizer if dryness persists and apply sunscreen in the morning. Discontinue if irritation occurs.',
            'يساعد على إزالة المكياج والواقي الشمسي مساءً قبل الغسول، عند الحاجة فقط.'
                => 'Helps melt away makeup and sunscreen before your evening cleanser when needed.',
            'يساعد على إزالة المكياج والواقي الشمسي مساءً قبل الغسول عند الحاجة.'
                => 'Helps dissolve makeup and sunscreen before your evening cleanser when needed.',
            'على وجه ويدين جافّين، دلّك كمية مناسبة لإذابة المكياج وواقي الشمس. أضف قليلًا من الماء حتى يتحوّل إلى سائل حليبي، ثم اشطفه جيدًا. إذا استخدمت غسولًا بعده، فليكن بعد شطف البلسم بالكامل.'
                => 'On dry skin and hands, massage a suitable amount to dissolve makeup and sunscreen. Add a splash of water to emulsify into a milk, rinse cleanly, and follow with your water cleanser if desired.',
            'عناية أسبوعية مكثفة لدعم الترطيب وحاجز البشرة، ويُستخدم بدلاً من المرطب الليلي في ليلة استخدامه.'
                => 'Weekly intensive care for deep hydration and barrier support; replaces your night moisturizer when used.',
            'يمنح البشرة عناية أسبوعية إضافية بالترطيب والنعومة والإشراقة، ويحل محل المرطب الليلي عند استخدامه.'
                => 'Gives skin a weekly boost of deep hydration, softness, and glow; replaces your night moisturizer when used.',
            'عناية أسبوعية إضافية بالترطيب والنعومة ومظهر الامتلاء، ويحل محل المرطب الليلي عند استخدامه.'
                => 'Weekly boost for deep moisture, smoothness, and plumper look; replaces your night moisturizer when used.',
            'يُستخدم عند الحاجة لترطيب محيط العين والعناية بمظهر الهالات والانتفاخ، ويفضل في الأيام التي لا يُستخدم فيها سيروم الريتينال حول العين.'
                => 'Use as needed to hydrate the under-eye area and target dark circles and puffiness, preferably on nights without retinal eye serum.',
            'بعد التنظيف والتونر إن استخدمته، ضع لصقة تحت كل عين لمدة 10–20 دقيقة، ثم أزلها وربّت ما تبقّى من الخلاصة. تجنّب حافة الجفن ولا تنم باللصقات.'
                => 'After cleansing and toning, place a patch under each eye for 10–20 minutes, remove, and pat in excess essence. Avoid the lash line and do not sleep with patches on.',
        ];
    }

    public static function routineCopyMap(): array
    {
        return [
            'women:acne' => [
                'title_en' => 'Acne Routine',
                'subtitle_en' => 'Clearer pores, calmer redness & balanced hydration',
                'description_en' => 'A balanced routine targeting breakouts, redness, and excess oil without compromising hydration. It helps care for clogged pores and post-acne marks so skin looks clearer, smoother, and more balanced.',
                'badge_en' => 'Selected by KCODE Formulation Review',
                'skin_type_en' => 'Oily and combination acne-prone skin.',
                'goal_en' => 'Breakouts, Blemishes & Post-Acne Marks',
                'card_note_en' => 'Use COSRX BHA in the evening 2–3 times weekly, building up gradually based on skin tolerance.',
                'audience_en' => 'Oily or combination skin struggling with breakouts, congested pores, redness, or post-acne marks.',
                'how_to_use_en' => 'Follow the step order for each time of day. In the morning: follow the sequence and always finish with sunscreen. In the evening: use your regular care night, or choose the dedicated night on its scheduled days. Important note: Use COSRX BHA in the evening 2–3 times weekly, building up gradually based on skin tolerance.',
                'timing_en' => [
                    'am' => 'Cleanser, serum, moisturizer, then sunscreen',
                    'pm' => 'Cleanser, serum (or BHA on exfoliation nights), then moisturizer',
                ],
                'benefits_en' => [
                    'Helps reduce the appearance of breakouts and blemishes.',
                    'Cares for redness associated with blemishes.',
                    'Helps regulate excess oil and reduce shine.',
                    'Improves the look of pores and blackheads.',
                ],
                'what_it_offers_en' => [
                    'Helps reduce the appearance of breakouts and blemishes.',
                    'Cares for redness associated with blemishes.',
                    'Helps regulate excess oil and reduce shine.',
                    'Improves the look of pores and blackheads.',
                    'Helps fade the appearance of post-acne marks.',
                    'Maintains skin hydration and overall balance.',
                ],
                'cover_alt_en' => 'Five products in the acne and blemish routine: SKIN1004 cleanser, ANUA azelaic serum, COSRX BHA, AXIS-Y cream and COSRX sunscreen',
                'variants' => [
                    'exfoliate' => [
                        'label_en' => 'Exfoliation Night',
                        'badge_en' => '2–3 times weekly, gradually',
                        'note_en' => 'On exfoliation night, the BHA exfoliant replaces your regular serum after cleansing and before moisturizer. Do not layer both active treatments on the same night.',
                    ],
                ],
            ],
            'women:pores' => [
                'title_en' => 'Pores Routine',
                'subtitle_en' => 'Oil control, blackhead care & smoother texture',
                'description_en' => 'A targeted routine for skin with blackheads, visible pores, and quick daytime shine. It helps care for congested pores, balance excess oil, and refine skin texture for a clearer, smoother, more balanced look.',
                'badge_en' => 'Selected by KCODE Formulation Review',
                'skin_type_en' => 'Oily and combination skin.',
                'goal_en' => 'Pores, Blackheads & Excess Shine',
                'card_note_en' => 'Use COSRX BHA in the evening 2–3 times weekly, building up gradually. The sun stick is for reapplication only.',
                'audience_en' => 'Oily or combination skin with visible pores, blackheads, and fast-returning daytime shine.',
                'how_to_use_en' => 'Follow the step order for each time of day. In the morning: follow the sequence and always finish with sunscreen. In the evening: use your regular care night, or choose the exfoliation night on its scheduled days. Important note: Use COSRX BHA in the evening 2–3 times weekly, building up gradually. The sun stick is for reapplication only.',
                'timing_en' => [
                    'am' => 'Cleanser, serum, gel moisturizer, then sunscreen',
                    'pm' => 'Cleanser, serum (or BHA on exfoliation nights), then gel moisturizer',
                ],
                'benefits_en' => [
                    'Helps minimize the appearance of pores.',
                    'Cares for blackheads and congested pores.',
                    'Helps regulate excess sebum.',
                    'Reduces visible shine throughout the day.',
                ],
                'what_it_offers_en' => [
                    'Helps minimize the appearance of pores.',
                    'Cares for blackheads and congested pores.',
                    'Helps regulate excess sebum.',
                    'Reduces visible shine throughout the day.',
                    'Refines skin texture and smoothness.',
                    'Cares for post-acne marks and uneven texture.',
                ],
                'cover_alt_en' => 'Five products in the pores and blackheads routine: SKIN1004 cleanser, COSRX Niacinamide 15 serum, COSRX BHA, Beauty of Joseon Red Bean Water Gel and COSRX sunscreen',
                'variants' => [
                    'exfoliate' => [
                        'label_en' => 'Exfoliation Night',
                        'badge_en' => '2–3 times weekly, gradually',
                        'note_en' => 'On exfoliation night, the BHA exfoliant replaces your regular serum after cleansing and before moisturizer. Do not layer both active treatments on the same night.',
                    ],
                ],
            ],
            'women:oily-hydration' => [
                'title_en' => 'Oily Hydration Routine',
                'subtitle_en' => 'Weightless moisture without heaviness or shine',
                'description_en' => 'If your skin looks shiny yet feels tight after cleansing, it does not need more drying—it needs a better balance of hydration and oil. This routine delivers lightweight moisture and greater comfort while keeping shine under control without heavy grease.',
                'badge_en' => 'Selected by KCODE Formulation Review',
                'skin_type_en' => 'Oily and combination skin experiencing tightness or dehydration.',
                'goal_en' => 'Oily Skin Feeling Tight After Cleansing',
                'card_note_en' => 'ABIB mask replaces your night moisturizer on evenings you use it; do not layer it on top of another cream.',
                'audience_en' => 'Oily or combination skin that shines quickly yet feels tight or dehydrated after washing.',
                'how_to_use_en' => 'Follow the step order for each time of day. In the morning: follow the sequence and always finish with sunscreen. In the evening: follow your regular evening routine. Important note: ABIB mask replaces your night moisturizer on evenings you use it; do not layer it on top of another cream.',
                'timing_en' => [
                    'am' => 'Cleanser, milky toner, B5 serum, calming gel, then sunscreen',
                    'pm' => 'Cleanser, milky toner, B5 serum, then calming gel',
                ],
                'benefits_en' => [
                    'Helps relieve post-cleanse tightness.',
                    'Provides lightweight hydration without weighing skin down.',
                    'Helps balance shine and excess oil.',
                    'Soothes skin and maintains daily comfort.',
                ],
                'what_it_offers_en' => [
                    'Helps relieve post-cleanse tightness.',
                    'Provides lightweight hydration without weighing skin down.',
                    'Helps balance shine and excess oil.',
                    'Soothes skin and maintains daily comfort.',
                    'Supports the skin barrier and reduces moisture loss.',
                    'Leaves skin looking smoother and more balanced.',
                ],
                'cover_alt_en' => 'Five products for oily skin that feels tight after cleansing: TORRIDEN cleanser, ANUA Rice 70 toner, numbuzin B5 serum, PURITO Oat-In cream and Beauty of Joseon Aqua-Fresh sunscreen',
            ],
            'women:pigmentation' => [
                'title_en' => 'Pigmentation Routine',
                'subtitle_en' => 'Multi-pathway care for melasma, dark spots & tone',
                'description_en' => 'A focused multi-pathway routine targeting melasma, dark spots, and uneven skin tone instead of relying on a single ingredient. It combines targeted pigment care, radiance, texture support, and fine-line care with hydration and daily sun protection.',
                'badge_en' => 'Selected by KCODE Formulation Review',
                'skin_type_en' => 'Non-sensitive oily, combination, normal, and dry skin.',
                'goal_en' => 'Melasma, Dark Spots & Uneven Skin Tone Care',
                'card_note_en' => 'Use the ANUA serum in the morning or on nights without Retino-Mela, and use Retino-Mela at night only as scheduled. Avoid layering both serums in the same session.',
                'audience_en' => 'Anyone looking to fade visible dark spots, melasma, or uneven tone with structured daytime and alternating evening care.',
                'how_to_use_en' => 'Follow the step order for each time of day. In the morning: follow the sequence and always finish with sunscreen. In the evening: use your regular care night, or choose the Retino-Mela night on its scheduled days. Important note: Use the ANUA serum in the morning or on nights without Retino-Mela, and use Retino-Mela at night only as scheduled. Avoid layering both serums in the same session.',
                'timing_en' => [
                    'am' => 'Cleanser, TXA serum, barrier cream, then sunscreen',
                    'pm' => 'Cleanser, TXA serum (or Retino-Mela on alternating nights), then barrier cream',
                ],
                'benefits_en' => [
                    'Helps reduce the look of melasma and pigmentation.',
                    'Targets dark spots and post-acne marks.',
                    'Helps promote a more even-looking skin tone.',
                    'Boosts radiance and reduces dullness.',
                ],
                'what_it_offers_en' => [
                    'Helps reduce the look of melasma and pigmentation.',
                    'Targets dark spots and post-acne marks.',
                    'Helps promote a more even-looking skin tone.',
                    'Boosts radiance and reduces dullness.',
                    'Improves the appearance of texture and fine lines.',
                    'Supports the skin barrier and provides daily sun protection.',
                ],
                'cover_alt_en' => 'Five products in the pigmentation routine: SKIN1004 cleanser, ANUA Niacinamide TXA serum, Dr.Reju-All Retino-Mela serum, PURITO moisturizer and numbuzin No.1 sunscreen',
                'variants' => [
                    'retino' => [
                        'label_en' => 'Retino-Mela Night',
                        'badge_en' => 'Every other night, then daily gradually',
                        'note_en' => 'Apply the Retino-Mela serum at night only according to the gradual schedule. It replaces your regular brightening serum on this night; do not layer both serums in one session.',
                    ],
                ],
            ],
            'women:barrier' => [
                'title_en' => 'Barrier Routine',
                'subtitle_en' => 'Calming redness & rebuilding daily comfort',
                'description_en' => 'A gentle routine for skin prone to visible redness, dryness, or discomfort. It focuses on soothing, hydrating, and supporting the skin barrier with comfortable steps that help skin regain a softer, calmer, and more balanced appearance.',
                'badge_en' => 'Selected by KCODE Formulation Review',
                'skin_type_en' => 'Sensitive skin across oily, combination, normal, and dry types.',
                'goal_en' => 'Redness Soothing & Skin Barrier Support',
                'card_note_en' => 'Only add an oil cleansing step when needed to remove makeup or sunscreen. The sheet mask replaces night moisturizer on evenings you use it.',
                'audience_en' => 'Sensitive or reactive skin showing visible redness, dryness, or compromised barrier comfort.',
                'how_to_use_en' => 'Follow the step order for each time of day. In the morning: follow the sequence and always finish with sunscreen. In the evening: follow your regular evening routine. Important note: Only add an oil cleansing step when needed to remove makeup or sunscreen. The mask replaces night moisturizer on evenings you use it.',
                'timing_en' => [
                    'am' => 'Cleanser, essence, B5 serum, calming gel, then sunscreen',
                    'pm' => 'Cleanser, essence, B5 serum, then calming gel',
                ],
                'benefits_en' => [
                    'Helps calm visible redness.',
                    'Reduces feelings of dryness and discomfort.',
                    'Delivers deep, balanced hydration.',
                    'Supports the skin barrier and minimizes moisture loss.',
                ],
                'what_it_offers_en' => [
                    'Helps calm visible redness.',
                    'Reduces feelings of dryness and discomfort.',
                    'Delivers deep, balanced hydration.',
                    'Supports the skin barrier and minimizes moisture loss.',
                    'Improves softness and plumped comfort.',
                    'Provides daily sun protection suited for sensitive skin.',
                ],
                'cover_alt_en' => 'Five products for the redness-soothing and skin-barrier routine: TORRIDEN cleanser, COSRX Snail 96 essence, numbuzin B5 serum, PURITO Oat-In cream and numbuzin No.1 sunscreen',
            ],
            'women:dry-hydration' => [
                'title_en' => 'Dry Hydration Routine',
                'subtitle_en' => 'Rich hydration, softness & renewed radiance',
                'description_en' => 'A rich routine for skin that feels dry or rough and lacks vitality and glow. It helps replenish hydration, support the skin barrier, and refine texture so skin looks softer, plumper, and more radiant.',
                'badge_en' => 'Selected by KCODE Formulation Review',
                'skin_type_en' => 'Dry and normal-to-dry skin.',
                'goal_en' => 'Hydration & Glow for Dry, Dull Skin',
                'card_note_en' => 'The mask replaces your night moisturizer on evenings you use it; do not layer it as an extra step over cream.',
                'audience_en' => 'Dry or dull skin seeking rich nourishment, smoother texture, and lasting radiance.',
                'how_to_use_en' => 'Follow the step order for each time of day. In the morning: follow the sequence and always finish with sunscreen. In the evening: follow your regular evening routine. Important note: The mask replaces your night moisturizer on evenings you use it; do not layer it as an extra step over cream.',
                'timing_en' => [
                    'am' => 'Cleanser, milky toner, softening serum, rich cream, then sunscreen',
                    'pm' => 'Cleanser, milky toner, softening serum, then rich cream',
                ],
                'benefits_en' => [
                    'Helps soften the look of dryness and rough texture.',
                    'Provides rich, comforting hydration.',
                    'Supports the skin barrier and reduces moisture loss.',
                    'Improves smoothness and skin texture.',
                ],
                'what_it_offers_en' => [
                    'Helps soften the look of dryness and rough texture.',
                    'Provides rich, comforting hydration.',
                    'Supports the skin barrier and reduces moisture loss.',
                    'Improves smoothness and skin texture.',
                    'Reduces dullness and enhances natural glow.',
                    'Leaves skin looking plumper and revitalized.',
                ],
                'cover_alt_en' => 'Five products in the dry-skin hydration and radiance routine: TORRIDEN cleanser, ANUA Rice 70 toner, numbuzin No.3 Skin Softening Serum, BIODANCE Skin-Glow cream and Beauty of Joseon sunscreen',
            ],
            'women:lines' => [
                'title_en' => 'Lines Routine',
                'subtitle_en' => 'Peptide firming & targeted under-eye care',
                'description_en' => 'A comprehensive routine for visible signs of aging—from fine lines and loss of firmness to under-eye dark circles and puffiness. It pairs peptides with targeted evening care and hydration for smoother, firmer, more revitalized skin around the face and eyes.',
                'badge_en' => 'Selected by KCODE Formulation Review',
                'skin_type_en' => 'Non-sensitive oily, combination, normal, and dry skin.',
                'goal_en' => 'Fine Lines, Wrinkles, Firmness & Eye Care',
                'card_note_en' => 'Use the eye serum at night, starting once every three nights. Use the eye patches on nights when you are not applying retinal eye serum.',
                'audience_en' => 'Anyone targeting fine lines, firmness loss, and dark circles or puffiness around the eye contour.',
                'how_to_use_en' => 'Follow the step order for each time of day. In the morning: follow the sequence and always finish with sunscreen. In the evening: use your regular care night, or follow the dedicated eye-serum night schedule. Important note: Use the eye serum at night, starting once every three nights. Use the eye patches on nights when you are not applying retinal eye serum.',
                'timing_en' => [
                    'am' => 'Cleanser, peptide booster, firming cream, then sunscreen',
                    'pm' => 'Cleanser, peptide booster, retinal eye serum (scheduled nights), then firming cream',
                ],
                'benefits_en' => [
                    'Helps soften the appearance of fine lines and wrinkles.',
                    'Supports visible skin elasticity and firmness.',
                    'Hydrates skin for a fuller, plumped look.',
                    'Improves smoothness and refined texture.',
                ],
                'what_it_offers_en' => [
                    'Helps soften the appearance of fine lines and wrinkles.',
                    'Supports visible skin elasticity and firmness.',
                    'Hydrates skin for a fuller, plumped look.',
                    'Improves smoothness and refined texture.',
                    'Cares for fine lines around the eye area.',
                    'Helps improve the appearance of under-eye dark circles and puffiness.',
                ],
                'cover_alt_en' => 'Five products for the fine-lines, firmness and eye-care routine: SKIN1004 cleanser, COSRX 6 Peptide serum, Beauty of Joseon eye serum, TORRIDEN CELLMAZING cream and numbuzin No.1 sunscreen',
                'variants' => [
                    'retinal' => [
                        'label_en' => 'Eye Serum Night',
                        'badge_en' => 'Eye area: start once every 3 nights',
                        'note_en' => 'Apply the retinal eye serum at night only, starting once every three nights after cleansing and toning, followed by moisturizer.',
                    ],
                ],
            ],
            'women:daily' => [
                'title_en' => 'Daily Routine',
                'subtitle_en' => 'Balanced everyday hydration, glow & protection',
                'description_en' => 'You do not need a major skin concern to start caring for your skin. This routine builds a balanced daily foundation for lasting hydration, smoothness, and glow while helping care for light spots, uneven tone, and daily UV exposure.',
                'badge_en' => 'Selected by KCODE Formulation Review',
                'skin_type_en' => 'All skin types.',
                'goal_en' => 'Daily Care for Balanced, Radiant Skin',
                'card_note_en' => 'The mask replaces your night moisturizer on evenings you use it. Complementary products are optional and not auto-added.',
                'audience_en' => 'Skin without acute concerns looking for a complete, reliable daily glow and protection ritual.',
                'how_to_use_en' => 'Follow the step order for each time of day. In the morning: follow the sequence and always finish with sunscreen. In the evening: follow your regular evening routine. Important note: The mask replaces your night moisturizer on evenings you use it. Complementary products are optional and not auto-added.',
                'timing_en' => [
                    'am' => 'Cleanser, milky toner, glow serum, cream, then sunscreen',
                    'pm' => 'Cleanser, milky toner, glow serum, then All-In-One cream',
                ],
                'benefits_en' => [
                    'Helps keep skin hydrated and smooth.',
                    'Boosts daily radiance and healthy vitality.',
                    'Cares for uneven skin tone and mild spots.',
                    'Supports the skin barrier and daily comfort.',
                ],
                'what_it_offers_en' => [
                    'Helps keep skin hydrated and smooth.',
                    'Boosts daily radiance and healthy vitality.',
                    'Cares for uneven skin tone and mild spots.',
                    'Supports the skin barrier and daily comfort.',
                    'Improves the look of smoothness and plumpness.',
                    'Provides daily protection against sun exposure.',
                ],
                'cover_alt_en' => 'Five products for balanced, radiant daily skincare: SKIN1004 cleanser, ANUA Rice 70 toner, Beauty of Joseon Glow Deep serum, COSRX Snail 92 cream and numbuzin No.1 sunscreen',
            ],
            'men:daily' => [
                'title_en' => 'Essential Daily Care',
                'subtitle_en' => 'Cleansing, hydration, and sun protection in simple steps.',
                'description_en' => 'A straightforward start for cleaner, smoother, healthier-looking skin. This routine combines cleansing, hydration, and daily sun protection to help keep skin looking healthy and guard against early dryness and sun-related marks.',
                'badge_en' => "Men's Care Routine",
                'skin_type_en' => 'All Skin Types',
                'goal_en' => 'Sunscreen helps reduce early sun-related dark spots and premature fine lines.',
                'card_note_en' => 'Apply moisturizer in the morning if needed, before sunscreen.',
                'audience_en' => 'You are looking for an easy daily routine to cleanse, hydrate, and protect your skin from the sun.',
                'how_to_use_en' => 'Morning: wash with the cleanser, optionally apply moisturizer if dry, and finish with sunscreen. Evening: cleanse and apply the gel moisturizer.',
                'timing_en' => [
                    'am' => 'Cleanser, then sunscreen',
                    'pm' => 'Cleanser, then gel moisturizer',
                ],
                'benefits_en' => [
                    'Removes daily impurities and oil for a cleaner look.',
                    'Hydrates skin and leaves a smoother feel without heaviness.',
                    'Helps bring out a fresh look and reduce dryness.',
                    'Supports the skin barrier to lock in comfort and moisture.',
                ],
                'what_it_offers_en' => [
                    'Removes daily impurities and oil for a cleaner look.',
                    'Hydrates skin and leaves a smoother feel without heaviness.',
                    'Helps bring out a fresh look and reduce dryness.',
                    'Supports the skin barrier to lock in comfort and moisture.',
                    'Hydration helps soften the look of fine dehydration lines.',
                    'Sunscreen helps reduce early sun-related dark spots and premature fine lines.',
                ],
                'cover_alt_en' => "Men's Essential Daily Care routine products: SKIN1004 Centella cleanser, PURITO Oat-In gel moisturizer, and Beauty of Joseon Aqua-Fresh sunscreen",
                'notes_en' => [
                    'Apply moisturizer in the morning if needed, before sunscreen.',
                ],
            ],
            'men:shine' => [
                'title_en' => 'Excess Shine Care',
                'subtitle_en' => 'Lightweight care for skin that gets shiny throughout the day.',
                'description_en' => 'For a fresher, more balanced look all day. Cleanses everyday excess oil and gives skin weightless hydration with an invisible sunscreen that helps protect skin and keep it healthy over time.',
                'badge_en' => "Men's Care Routine",
                'skin_type_en' => 'Oily, Combination & Normal Skin',
                'goal_en' => 'Sunscreen helps reduce early sun-related dark spots and fine lines.',
                'card_note_en' => 'Helps balance the look of excess shine with consistent use.',
                'audience_en' => 'You notice excess shine during the day and want gentle cleansing and light hydration without a greasy feel.',
                'how_to_use_en' => 'Morning: wash with cleanser and apply invisible sunscreen. Evening: cleanse and finish with the lightweight gel moisturizer.',
                'timing_en' => [
                    'am' => 'Cleanser, then sunscreen',
                    'pm' => 'Cleanser, then gel moisturizer',
                ],
                'benefits_en' => [
                    'Clears away daily impurities and oil for a fresher look.',
                    'Helps balance excess shine over time.',
                    'Provides light hydration without a heavy greasy feel.',
                    'Promotes smooth texture and healthy natural tone.',
                ],
                'what_it_offers_en' => [
                    'Clears away daily impurities and oil for a fresher look.',
                    'Helps balance excess shine over time.',
                    'Provides light hydration without a heavy greasy feel.',
                    'Promotes smooth texture and healthy natural tone.',
                    'Regular hydration softens fine dryness lines.',
                    'Sunscreen helps reduce early sun-related dark spots and fine lines.',
                ],
                'cover_alt_en' => "Men's Excess Shine Care routine products: ANUA Heartleaf cleanser, Beauty of Joseon Red Bean gel, and COSRX Ultra Light sunscreen",
                'notes_en' => [
                    'Helps balance the look of excess shine with consistent use.',
                ],
            ],
            'men:tightness' => [
                'title_en' => 'Post-Wash Hydration & Comfort',
                'subtitle_en' => 'Hydrating care for dryness and tightness after washing your face.',
                'description_en' => "Restore your skin's comfort after washing. Pairs gentle cleansing with barrier-supporting moisture so skin feels smoother and refreshed while easing dryness and tightness, finished with daily sun protection.",
                'badge_en' => "Men's Care Routine",
                'skin_type_en' => 'Dry, Normal & Combination Skin',
                'goal_en' => 'Protects against sun damage that worsens dark spots and premature lines.',
                'card_note_en' => 'Use moisturizer morning and evening; apply sunscreen in the morning.',
                'audience_en' => 'Your skin feels dry or tight after washing and needs comfort-focused moisture that supports the skin barrier.',
                'how_to_use_en' => 'Morning: cleanse, apply moisturizer, and finish with sunscreen once absorbed. Evening: cleanse and apply moisturizer.',
                'timing_en' => [
                    'am' => 'Cleanser, moisturizer, then sunscreen',
                    'pm' => 'Cleanser, then moisturizer',
                ],
                'benefits_en' => [
                    'Gently cleanses while preserving skin comfort.',
                    'Helps ease post-wash dryness and tight feeling.',
                    'Supports the skin barrier to retain hydration.',
                    'Improves dry texture for softer, healthier-looking skin.',
                ],
                'what_it_offers_en' => [
                    'Gently cleanses while preserving skin comfort.',
                    'Helps ease post-wash dryness and tight feeling.',
                    'Supports the skin barrier to retain hydration.',
                    'Improves dry texture for softer, healthier-looking skin.',
                    'Hydration helps minimize the look of fine dehydration lines.',
                    'Protects against sun damage that worsens dark spots and premature lines.',
                ],
                'cover_alt_en' => "Men's Post-Wash Hydration & Comfort routine products: TORRIDEN Dive-In cleanser, ILLIYOON Ceramide Ato cream, and numbuzin No.1 sunscreen",
                'notes_en' => [
                    'Use moisturizer morning and evening; apply sunscreen in the morning.',
                ],
            ],
            'men:dullness' => [
                'title_en' => 'Radiance for Dull Skin',
                'subtitle_en' => 'Targeted care for dull skin to boost glow and even tone.',
                'description_en' => 'Give your skin a brighter, more energized look. Combines a lightweight brightening serum with daily moisturizer and sunscreen to gradually improve uneven tone, smoothness, and natural radiance.',
                'badge_en' => "Men's Care Routine",
                'skin_type_en' => 'All Skin Types',
                'goal_en' => 'Sun protection preserves even tone and reduces early UV signs.',
                'card_note_en' => 'Start using the serum every other night during the first week.',
                'audience_en' => 'Your skin looks tired or dull and you want care that brightens and evens out your tone.',
                'how_to_use_en' => 'Morning: cleanse and finish with sunscreen. Evening: cleanse, apply 2–3 drops of serum (every other night in week 1, then daily if comfortable), and seal with moisturizer.',
                'timing_en' => [
                    'am' => 'Cleanser, then sunscreen',
                    'pm' => 'Cleanser, serum, then moisturizer',
                ],
                'benefits_en' => [
                    'Clears everyday buildup for a cleaner, refreshed appearance.',
                    'Helps reduce dullness and reveal brighter radiance.',
                    'Gradually supports more even-looking skin tone.',
                    'Hydrates skin for a smoother texture and livelier look.',
                ],
                'what_it_offers_en' => [
                    'Clears everyday buildup for a cleaner, refreshed appearance.',
                    'Helps reduce dullness and reveal brighter radiance.',
                    'Gradually supports more even-looking skin tone.',
                    'Hydrates skin for a smoother texture and livelier look.',
                    'Hydration helps soften fine lines caused by dryness.',
                    'Sun protection preserves even tone and reduces early UV signs.',
                ],
                'cover_alt_en' => "Men's Radiance for Dull Skin routine products: SKIN1004 cleanser, Beauty of Joseon Glow serum, PURITO Oat-In cream, and Aqua-Fresh sunscreen",
                'notes_en' => [
                    'Start using the serum every other night during the first week.',
                ],
            ],
            'men:shaving' => [
                'title_en' => 'Post-Shave Care',
                'subtitle_en' => 'Gentle care for dryness and discomfort after shaving.',
                'description_en' => 'Post-shave comfort that lasts all day. Combines gentle cleansing and a soothing ampoule with lightweight moisturizer and sunscreen to calm post-shave dryness and keep skin smooth and refreshed.',
                'badge_en' => "Men's Care Routine",
                'skin_type_en' => 'All Skin Types',
                'goal_en' => 'Protects skin from UV rays and helps minimize sun-related aging signs.',
                'card_note_en' => 'Do not apply the ampoule on an open cut or broken skin.',
                'audience_en' => 'Your skin feels dry or irritated after shaving and needs gentle calming hydration.',
                'how_to_use_en' => 'Morning: wash, shave in the direction of hair growth with shave cream or gel, apply the ampoule after shaving, and finish with sunscreen. Evening: cleanse and moisturize (apply ampoule before moisturizer if shaving at night).',
                'timing_en' => [
                    'am' => 'Cleanse & shave, ampoule, then sunscreen',
                    'pm' => 'Cleanser then moisturizer; ampoule after shaving only',
                ],
                'benefits_en' => [
                    'Gently cleanses and prepares facial skin for shaving.',
                    'Helps calm post-shave burning and discomfort.',
                    'Replenishes moisture and reduces dryness and tightness.',
                    'Leaves skin smoother, calmer, and more refreshed.',
                ],
                'what_it_offers_en' => [
                    'Gently cleanses and prepares facial skin for shaving.',
                    'Helps calm post-shave burning and discomfort.',
                    'Replenishes moisture and reduces dryness and tightness.',
                    'Leaves skin smoother, calmer, and more refreshed.',
                    'Consistent hydration helps soften fine dryness lines.',
                    'Protects skin from UV rays and helps minimize sun-related aging signs.',
                ],
                'cover_alt_en' => "Men's Post-Shave Care routine products: SKIN1004 cleanser, SKIN1004 Centella ampoule, PURITO Oat-In moisturizer, and Beauty of Joseon Aqua-Fresh sunscreen",
                'notes_en' => [
                    'If shaving in the evening, apply the ampoule after shaving and before moisturizer. Do not apply on open cuts.',
                    'If you use retinol or an exfoliant, avoid applying it immediately after shaving.',
                ],
            ],
        ];
    }
}

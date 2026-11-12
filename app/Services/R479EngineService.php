<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class R479EngineService
{
    public const VERSION = 'KCODE-R479-handoff';

    public const ROLE_ORDER = [
        'cleanser',
        'toner',
        'treatment1',
        'treatment2',
        'moisturizer',
        'sunscreen',
    ];

    public const FIELDS = [
        'acne'       => ['Acne'],
        'pores'      => ['Pores'],
        'blackheads' => ['Blackheads'],
        'dark_spots' => ['Pigmentation PIH'],
        'dullness'   => ['Radiance'],
        'redness'    => ['Redness PIE'],
        'dryness'    => ['Barrier', 'Hydration'],
        'lines'      => ['Lines'],
        'maintain'   => ['Hydration', 'Barrier', 'Radiance'],
        'oil'        => ['Oil Control'],
        'eyes'       => ['Eye Dark Circles'],
    ];

    public const EXFOLIATORS = [
        'KC0010', 'KC0046', 'KC0063', 'KC0177', 'KC0180', 'KC0181',
        'KC0040', 'KC0083', 'KC0085', 'KC0109', 'KC0012', 'KC0014',
        'KC0052', 'KC0126', 'KC0175', 'KC0259',
    ];

    public const GOAL_LABELS_AR = [
        'acne'       => 'الحبوب',
        'pores'      => 'مظهر المسام',
        'blackheads' => 'الرؤوس السوداء',
        'dark_spots' => 'البقع الداكنة',
        'dullness'   => 'البهتان',
        'redness'    => 'الاحمرار',
        'dryness'    => 'الجفاف ودعم الحاجز',
        'lines'      => 'الخطوط الدقيقة',
        'maintain'   => 'العناية العامة',
        'oil'        => 'الدهون واللمعان',
    ];

    public const TREATMENT_GOAL_DISPLAY_AR = [
        'acne'       => 'الحبوب',
        'pores'      => 'مظهر المسام',
        'blackheads' => 'الرؤوس السوداء',
        'dark_spots' => 'البقع الداكنة',
        'dullness'   => 'إشراقة البشرة',
        'redness'    => 'الاحمرار',
        'dryness'    => 'الجفاف ودعم حاجز البشرة',
        'lines'      => 'الخطوط الدقيقة',
        'maintain'   => 'ترطيب البشرة ونضارتها',
        'oil'        => 'الدهون الزائدة واللمعان',
        'eyes'       => 'الهالات تحت العين',
    ];

    protected ?array $contract = null;
    protected ?array $catalog = null;
    protected ?array $catalogBySku = null;
    protected ?array $masterBySku = null;
    protected ?array $productsContent = null;
    protected ?array $supplementRegistry = null;
    protected ?array $purposes = null;
    protected ?array $excludedExfoliatorCases = null;
    protected ?array $usagePolicies = null;
    protected ?array $usageShort = null;
    protected ?array $displayLabels = null;
    protected ?array $uiMatrix = null;

    protected function loadData(): void
    {
        if ($this->catalog !== null) {
            return;
        }

        $quizDir = base_path('database/seeders/data/r479_quiz');
        $dataDir = base_path('database/seeders/data');

        $this->contract = json_decode(File::get("$quizDir/quiz_contract.json"), true);
        $this->catalog = json_decode(File::get("$quizDir/catalog.json"), true);
        $this->catalogBySku = [];
        foreach ($this->catalog as $item) {
            $this->catalogBySku[$item['sku']] = $item;
        }

        $masterRaw = json_decode(File::get("$dataDir/r479_master_snapshot.json"), true);
        $this->masterBySku = [];
        foreach ($masterRaw as $row) {
            if (!empty($row['SKU'])) {
                $this->masterBySku[$row['SKU']] = $row;
            }
        }

        $this->productsContent = json_decode(File::get("$dataDir/r479_products.json"), true);
        $this->supplementRegistry = json_decode(File::get("$quizDir/supplement_registry.json"), true);
        $this->purposes = json_decode(File::get("$quizDir/purposes.json"), true);
        $this->excludedExfoliatorCases = json_decode(File::get("$quizDir/approved_exfoliator_exclusions.json"), true);
        $this->usagePolicies = json_decode(File::get("$quizDir/usage_policies.json"), true);
        $this->usageShort = json_decode(File::get("$quizDir/usage_short.json"), true);
        $this->displayLabels = json_decode(File::get("$quizDir/display_labels.json"), true);

        if (File::exists("$dataDir/quiz_results_matrix.json")) {
            $this->uiMatrix = json_decode(File::get("$dataDir/quiz_results_matrix.json"), true);
        }
    }

    public function getContract(): array
    {
        $this->loadData();
        return $this->contract;
    }

    public function validateAnswers(array $a): void
    {
        $this->loadData();
        $requiredKeys = ['primary', 'secondary', 'skin', 'sensitive'];
        if (count(array_diff($requiredKeys, array_keys($a))) > 0 || count($a) !== 4) {
            throw new \InvalidArgumentException('invalid fields');
        }
        if (
            !in_array($a['primary'], $this->contract['primary'], true)
            || ($a['secondary'] !== null && !in_array($a['secondary'], $this->contract['secondary'], true))
            || $a['primary'] === $a['secondary']
        ) {
            throw new \InvalidArgumentException('invalid goals');
        }
        if (!in_array($a['skin'], $this->contract['skin'], true) || !is_bool($a['sensitive'])) {
            throw new \InvalidArgumentException('invalid eligibility answers');
        }
    }

    protected function goalKey(array $p, string $goal): array
    {
        $fields = self::FIELDS[$goal] ?? [];
        $res = [];
        foreach ($fields as $f) {
            $res[] = (float) ($p['scores'][$f] ?? 0.0);
        }
        return $res;
    }

    /**
     * Compare two products by descending score tuple, descending tolerance, ascending SKU.
     */
    protected function compareRank(array $valsA, float $tolA, string $skuA, array $valsB, float $tolB, string $skuB): int
    {
        $len = max(count($valsA), count($valsB));
        for ($i = 0; $i < $len; $i++) {
            $va = $valsA[$i] ?? 0.0;
            $vb = $valsB[$i] ?? 0.0;
            if ($va !== $vb) {
                return $vb <=> $va; // descending
            }
        }
        if ($tolA !== $tolB) {
            return $tolB <=> $tolA; // descending
        }
        return strcmp($skuA, $skuB); // ascending
    }

    /**
     * Exact port of runtime/engine.py::select()
     */
    public function selectCore(array $a, array $unavailable = [], bool $toner = true): array
    {
        $this->validateAnswers($a);
        $unavailableSet = array_flip($unavailable);
        $skin = $a['skin'];
        $sensitive = $a['sensitive'];

        $pool = array_values(array_filter($this->catalog, function (array $p) use ($unavailableSet, $skin, $sensitive) {
            if (isset($unavailableSet[$p['sku']])) {
                return false;
            }
            if (($p['fit'][$skin] ?? 0) < 60) {
                return false;
            }
            if ($sensitive && ($p['tolerance'] ?? 0) < 60) {
                return false;
            }
            return true;
        }));

        // Preserve maintenance + secondary normalization
        $primary = $a['primary'];
        $secondary = $a['secondary'];
        if ($primary === 'maintain' && $secondary !== null) {
            $primary = $secondary;
            $secondary = null;
        }

        $picks = [];
        $trace = [];
        $missing = [];

        $pick = function (string $role, array $ps, callable $keyFn) use (&$picks, &$trace, &$missing) {
            if (empty($ps)) {
                $missing[] = $role;
                return;
            }
            usort($ps, function (array $x, array $y) use ($keyFn) {
                return $this->compareRank(
                    $keyFn($x),
                    (float) $x['tolerance'],
                    $x['sku'],
                    $keyFn($y),
                    (float) $y['tolerance'],
                    $y['sku']
                );
            });
            $winner = $ps[0];
            $picks[$role] = $winner['sku'];
            $trace[$role] = [
                'rank_values' => $keyFn($winner),
                'eligible_candidates' => count($ps),
            ];
        };

        foreach ([['treatment1', $primary], ['treatment2', $secondary]] as [$role, $g]) {
            if ($g === null) {
                continue;
            }
            $scope = $g === 'blackheads' ? 'blackheads_treatment' : ($g === 'eyes' ? 'eye_treatment' : 'face_treatment');
            $ps = array_values(array_filter($pool, function (array $p) use ($scope, $g) {
                if (!in_array($scope, $p['roles'], true)) {
                    return false;
                }
                if (!empty($p['allowed_goals']) && !in_array($g, $p['allowed_goals'], true)) {
                    return false;
                }
                $gk = $this->goalKey($p, $g);
                return ($gk[0] ?? 0) > 0;
            }));
            $pick($role, $ps, fn (array $p) => $this->goalKey($p, $g));
        }

        if ($secondary !== null) {
            $tops = [];
            foreach ([$primary, $secondary] as $g) {
                $scope = $g === 'blackheads' ? 'blackheads_treatment' : ($g === 'eyes' ? 'eye_treatment' : 'face_treatment');
                $ps = array_values(array_filter($pool, function (array $p) use ($scope, $g) {
                    if (!in_array($scope, $p['roles'], true)) {
                        return false;
                    }
                    if (!empty($p['allowed_goals']) && !in_array($g, $p['allowed_goals'], true)) {
                        return false;
                    }
                    $gk = $this->goalKey($p, $g);
                    return ($gk[0] ?? 0) > 0;
                }));
                $best = null;
                foreach ($ps as $p) {
                    $gk = $this->goalKey($p, $g);
                    if ($best === null || $gk > $best) {
                        $best = $gk;
                    }
                }
                $topSkus = [];
                foreach ($ps as $p) {
                    if ($this->goalKey($p, $g) === $best) {
                        $topSkus[$p['sku']] = true;
                    }
                }
                $tops[] = $topSkus;
            }
            $common = array_intersect_key($tops[0], $tops[1]);
            if (!empty($common)) {
                $commonPool = array_values(array_filter($pool, fn (array $p) => isset($common[$p['sku']])));
                usort($commonPool, fn (array $x, array $y) => $this->compareRank([], (float) $x['tolerance'], $x['sku'], [], (float) $y['tolerance'], $y['sku']));
                $winnerSku = $commonPool[0]['sku'];
                $picks['treatment1'] = $winnerSku;
                $picks['treatment2'] = $winnerSku;
            }
        }

        // Approved pair review: retain primary winner, avoid a second retinoid (KC0122 + KC0242)
        $t1 = $picks['treatment1'] ?? null;
        $t2 = $picks['treatment2'] ?? null;
        if (($t1 === 'KC0122' && $t2 === 'KC0242') || ($t1 === 'KC0242' && $t2 === 'KC0122')) {
            $ps = array_values(array_filter($pool, function (array $p) use ($secondary) {
                if (!in_array('face_treatment', $p['roles'], true)) {
                    return false;
                }
                $lowerName = strtolower($p['name'] ?? '');
                foreach (['retinol', 'retinal', 'retino'] as $term) {
                    if (str_contains($lowerName, $term)) {
                        return false;
                    }
                }
                if (!empty($p['allowed_goals']) && !in_array($secondary, $p['allowed_goals'], true)) {
                    return false;
                }
                $gk = $this->goalKey($p, (string) $secondary);
                return ($gk[0] ?? 0) > 0;
            }));
            if (empty($ps)) {
                unset($picks['treatment2'], $trace['treatment2']);
            }
            $pick('treatment2', $ps, fn (array $p) => $this->goalKey($p, (string) $secondary));
        }

        foreach (['cleanser', 'moisturizer', 'sunscreen'] as $role) {
            $keyFn = function (array $p) use ($role, $skin) {
                if ($role === 'moisturizer') {
                    return [
                        (float) $p['fit'][$skin],
                        (float) $p['scores']['Hydration'],
                        (float) $p['scores']['Barrier'],
                    ];
                }
                return [
                    (float) $p['fit'][$skin],
                    (float) $p['tolerance'],
                ];
            };
            $ps = array_values(array_filter($pool, fn (array $p) => in_array($role, $p['roles'], true)));
            $pick($role, $ps, $keyFn);
        }

        if ($toner) {
            $pickedSkus = array_values($picks);
            $existing = null;
            foreach ($pool as $p) {
                if (in_array($p['sku'], $pickedSkus, true) && in_array('toner', $p['roles'], true)) {
                    $existing = $p;
                    break;
                }
            }
            if ($existing) {
                $picks['toner'] = $existing['sku'];
                $trace['toner'] = ['reason' => 'same product serves treatment and toner'];
            } else {
                $goals = array_values(array_filter([$primary, $secondary], fn ($g) => $g && !in_array($g, ['eyes', 'blackheads'], true)));
                if (empty($goals)) {
                    $goals = ['maintain'];
                }
                $keyFn = function (array $p) use ($goals) {
                    $vals = [];
                    foreach ($goals as $g) {
                        foreach ($this->goalKey($p, $g) as $v) {
                            $vals[] = $v;
                        }
                    }
                    return $vals;
                };
                $ps = array_values(array_filter($pool, function (array $p) use ($goals) {
                    if (!in_array('toner', $p['roles'], true)) {
                        return false;
                    }
                    foreach ($goals as $g) {
                        if (($this->goalKey($p, $g)[0] ?? 0) > 0) {
                            return true;
                        }
                    }
                    return false;
                }));
                $pick('toner', $ps, $keyFn);
            }
        }

        $uniqueProducts = array_values(array_unique(array_values($picks)));

        return [
            'answers' => $a,
            'effective_goals' => [$primary, $secondary],
            'picks' => $picks,
            'products' => $uniqueProducts,
            'missing' => $missing,
            'trace' => $trace,
        ];
    }

    protected function exfoliationCheck(string $sku, array $coreSkus, array $goals): ?string
    {
        if (!in_array($sku, self::EXFOLIATORS, true)) {
            return null;
        }
        $coreSet = array_flip($coreSkus);
        if (in_array($sku, ['KC0010', 'KC0046', 'KC0063', 'KC0177', 'KC0180', 'KC0181'], true)) {
            if (isset($coreSet['KC0122'])) {
                return 'existing_retinol_salicylic_treatment_no_added_benefit_established';
            }
            foreach ($coreSkus as $k) {
                $conflictGroups = strtolower($this->masterBySku[$k]['Conflict Groups'] ?? '');
                if (str_contains($conflictGroups, 'spicule')) {
                    return 'spicule_exfoliator_pair_unverified';
                }
            }
        }
        $others = array_values(array_intersect($coreSkus, self::EXFOLIATORS));
        sort($others);
        if (empty($others)) {
            return null;
        }
        $skuPurposes = array_filter(explode(' ', $this->purposes[$sku] ?? ''));
        $targets = array_values(array_intersect($skuPurposes, $goals));
        $covered = [];
        foreach ($others as $k) {
            foreach (array_filter(explode(' ', $this->purposes[$k] ?? '')) as $pGoal) {
                $covered[$pGoal] = true;
            }
        }
        foreach ($targets as $t) {
            if (!isset($covered[$t])) {
                return 'additional_exfoliation_pair_unverified';
            }
        }
        return 'duplicate_exfoliation_purpose';
    }

    /**
     * Exact port of runtime/supplements.py::choose()
     */
    public function chooseSupplements(array $a, array $coreIds = [], array $unavailable = []): array
    {
        $this->validateAnswers($a);
        $coreSet = array_flip($coreIds);
        $unavailableSet = array_flip($unavailable);
        $goals = array_values(array_filter([$a['primary'], $a['secondary']]));
        $skin = $a['skin'];
        $sensitive = $a['sensitive'];

        $eligible = [];
        $rejected = [];

        foreach ($this->supplementRegistry as $entry) {
            $s = $entry['sku'];
            $p = $this->catalogBySku[$s];
            $why = null;
            if (isset($coreSet[$s])) {
                $why = 'core_duplicate';
            } elseif (isset($unavailableSet[$s])) {
                $why = 'unavailable';
            } elseif (($p['fit'][$skin] ?? 0) < 60) {
                $why = 'skin_fit_below60';
            } elseif ($sensitive && ($p['tolerance'] ?? 0) < 60) {
                $why = 'tolerance_below60';
            } else {
                $why = $this->exfoliationCheck($s, $coreIds, $goals);
            }

            if ($why !== null) {
                $rejected[] = ['sku' => $s, 'reason' => $why];
                continue;
            }

            $sup = [];
            $keys = [];
            foreach ($goals as $g) {
                if ($g === 'eyes' && $entry['kind'] === 'eye_patch') {
                    $vals = [(float) $p['scores']['Eye Puffiness']];
                } elseif ($g === 'lines' && $entry['kind'] === 'eye_patch') {
                    $vals = [(float) $p['scores']['Lines']];
                } elseif (in_array($g, $entry['goals'], true) && isset(self::FIELDS[$g])) {
                    $vals = $this->goalKey($p, $g);
                } else {
                    $vals = [];
                }

                if (!empty($vals) && $vals[0] > 0) {
                    $sup[$g] = $vals;
                }
                if (!empty($vals)) {
                    foreach ($vals as $v) {
                        $keys[] = $v;
                    }
                } else {
                    $padCount = count(self::FIELDS[$g] ?? ['Eye Puffiness']);
                    for ($i = 0; $i < $padCount; $i++) {
                        $keys[] = -1.0;
                    }
                }
            }

            $general = empty($sup) && $entry['kind'] === 'mask' && !in_array($s, self::EXFOLIATORS, true);
            if (empty($sup) && !$general) {
                continue;
            }

            if ($entry['kind'] === 'eye_patch') {
                $targets = [];
                if (in_array('lines', $goals, true)) {
                    $targets[] = 'Lines';
                }
                if (in_array('eyes', $goals, true)) {
                    $targets[] = 'Eye Puffiness';
                }
                if (empty($targets)) {
                    continue;
                }
                $keys = array_map(fn ($f) => (float) $p['scores'][$f], $targets);
            }

            if ($general) {
                $reason = 'ماسك ترطيب عام اختياري؛ ليس علاجًا للمشكلة المختارة';
            } else {
                $parts = [];
                foreach (array_keys($sup) as $g) {
                    $parts[] = self::GOAL_LABELS_AR[$g] ?? 'لمظهر الانتفاخ تحت العين، إذا كان موجودًا أيضًا';
                }
                $reason = 'دعم اختياري: ' . implode(' و', $parts);
            }

            if ($entry['kind'] === 'eye_patch' && in_array('lines', $goals, true)) {
                $reason = 'عناية اختيارية للخطوط الدقيقة حول العين' . (in_array('eyes', $goals, true) ? '؛ ولمظهر الانتفاخ إذا كان موجودًا أيضًا' : '');
            }
            if ($entry['kind'] === 'acne_patch') {
                $reason = 'للحبة السطحية المناسبة ذات الإفرازات؛ ليس للآثار أو الرؤوس السوداء';
            }

            $eligible[] = [
                'sku' => $s,
                'kind' => $entry['kind'],
                'supported' => $sup,
                'rank_values' => $keys,
                'general_support' => $general,
                'reason' => $reason,
                'optional' => true,
                'conditional_puffiness' => $entry['kind'] === 'eye_patch' && in_array('eyes', $goals, true),
            ];
        }

        $rankSort = function (array &$list) {
            usort($list, function (array $x, array $y) {
                return $this->compareRank(
                    $x['rank_values'],
                    (float) $this->catalogBySku[$x['sku']]['tolerance'],
                    $x['sku'],
                    $y['rank_values'],
                    (float) $this->catalogBySku[$y['sku']]['tolerance'],
                    $y['sku']
                );
            });
        };

        $winners = [];
        foreach (['mask', 'eye_patch', 'acne_patch', 'essence', 'exfoliator', 'mist'] as $kind) {
            $ps = array_values(array_filter($eligible, fn (array $x) => $x['kind'] === $kind));
            if ($kind === 'mask') {
                $direct = array_values(array_filter($ps, fn (array $x) => !$x['general_support']));
                if (!empty($direct)) {
                    $ps = $direct;
                } else {
                    foreach ($ps as &$x) {
                        $sc = $this->catalogBySku[$x['sku']]['scores'];
                        $x['rank_values'] = [(float) $sc['Hydration'], (float) $sc['Barrier']];
                    }
                    unset($x);
                }
            }
            if (!empty($ps)) {
                $rankSort($ps);
                $winners[$kind] = $ps[0];
            }
        }

        $selected = [];
        foreach (['mask', 'eye_patch', 'acne_patch'] as $k) {
            if (isset($winners[$k])) {
                $selected[] = $winners[$k];
            }
        }

        $remaining = [];
        foreach ($winners as $k => $v) {
            if (!in_array($k, ['mask', 'eye_patch', 'acne_patch'], true)) {
                $remaining[] = $v;
            }
        }
        $rankSort($remaining);

        foreach ($remaining as $candidate) {
            if (count($selected) >= 3) {
                break;
            }
            if (in_array($candidate['kind'], ['essence', 'mist'], true)) {
                $hasFluid = false;
                foreach ($selected as $sel) {
                    if (in_array($sel['kind'], ['essence', 'mist'], true)) {
                        $hasFluid = true;
                        break;
                    }
                }
                if ($hasFluid) {
                    $rejected[] = ['sku' => $candidate['sku'], 'reason' => 'one_fluid_support_slot'];
                    continue;
                }
            }
            $selected[] = $candidate;
        }

        $selected = array_slice($selected, 0, 3);

        // Approved 16 exfoliator exclusions
        $isExcludedCase = false;
        foreach ($this->excludedExfoliatorCases as $ex) {
            if (
                $ex['primary'] === $a['primary']
                && $ex['secondary'] === $a['secondary']
                && $ex['skin'] === $a['skin']
                && $ex['sensitive'] === $a['sensitive']
            ) {
                $isExcludedCase = true;
                break;
            }
        }

        if ($isExcludedCase) {
            foreach ($selected as $x) {
                if ($x['kind'] === 'exfoliator') {
                    $rejected[] = ['sku' => $x['sku'], 'reason' => 'approved_16_case_exclusion_no_refill'];
                }
            }
            $selected = array_values(array_filter($selected, fn (array $x) => $x['kind'] !== 'exfoliator'));
        }

        return [
            'answers' => $a,
            'addons' => $selected,
            'candidates' => $eligible,
            'rejected' => $rejected,
            'category_winners' => $winners,
        ];
    }

    /**
     * Exact port of runtime/service.py::recommend() + UI enrichment & usage schedule
     */
    public function recommend(array $answers, array $unavailable = []): array
    {
        $this->loadData();
        $this->validateAnswers($answers);

        $core = $this->selectCore($answers, $unavailable);
        $supplements = $this->chooseSupplements($answers, $core['products'], $unavailable);

        $scenarioKey = implode('|', [
            $answers['primary'],
            $answers['secondary'] ?? 'none',
            $answers['skin'],
            $answers['sensitive'] ? 'yes' : 'no',
        ]);

        // Pre-index UI matrix cards for this scenario if available
        $matrixRoleBySku = [];
        if (isset($this->uiMatrix['paths'][$scenarioKey])) {
            $pathData = $this->uiMatrix['paths'][$scenarioKey];
            foreach (array_merge($pathData['primary_routine'] ?? [], $pathData['routine_support'] ?? []) as $pIdx) {
                if (isset($this->uiMatrix['products'][$pIdx])) {
                    $mp = $this->uiMatrix['products'][$pIdx];
                    $matrixRoleBySku[$mp['sku']] = $mp;
                }
            }
        }

        $buildCard = function (string $sku, array $roles) use ($core, $matrixRoleBySku) {
            $p = $this->masterBySku[$sku] ?? [];
            $c = $this->productsContent[$sku] ?? [];
            $matrixCard = $matrixRoleBySku[$sku] ?? null;
            $policy = $this->usagePolicies[$sku] ?? ($matrixCard['usage_policy'] ?? null);
            $shortAr = $this->usageShort[$sku] ?? ($matrixCard['usage_short_ar'] ?? null);
            $displayRoleAr = $matrixCard['display_role_ar'] ?? $this->resolveDisplayRoleAr($sku, $roles, $core['effective_goals']);

            return [
                'sku' => $sku,
                'brand' => $c['brand'] ?? ($p['Brand'] ?? ''),
                'name' => $c['name'] ?? ($p['Product'] ?? ''),
                'size' => $c['size'] ?? ($p['Size'] ?? ''),
                'roles' => $roles,
                'routine_step_code' => $matrixCard['routine_step_code'] ?? ($roles[0] ?? 'step'),
                'display_role_ar' => $displayRoleAr,
                'description_ar' => $c['description'] ?? ($p['Description AR'] ?? ''),
                'chosen_for_ar' => $matrixCard['chosen_for_ar'] ?? ($c['description'] ?? ($p['Description AR'] ?? '')),
                'usage' => $c['usage_tiles'] ?? [],
                'usage_text_ar' => $c['usage'] ?? '',
                'usage_short_ar' => $shortAr,
                'usage_policy' => $policy,
                'usage_notice_ar' => $c['usage_notice_ar'] ?? '',
                'frequency' => $p['Frequency'] ?? '',
                'usage_status' => 'reviewed_product_directions',
                'guide_status' => isset($this->usageShort[$sku]) ? 'reviewed_short_copy' : 'individual_product_directions_only',
            ];
        };

        $cards = [];
        foreach (self::ROLE_ORDER as $role) {
            $sku = $core['picks'][$role] ?? null;
            if ($sku && !in_array($sku, array_column($cards, 'sku'), true)) {
                $assignedRoles = [];
                foreach (self::ROLE_ORDER as $r) {
                    if (($core['picks'][$r] ?? null) === $sku) {
                        $assignedRoles[] = $r;
                    }
                }
                $cards[] = $buildCard($sku, $assignedRoles);
            }
        }

        $additions = [];
        foreach ($supplements['addons'] as $x) {
            $item = $buildCard($x['sku'], ['supplement']);
            $item['kind'] = $x['kind'];
            $item['reason_ar'] = $x['reason'];
            $item['optional'] = true;
            $item['conditional_puffiness'] = $x['conditional_puffiness'];
            $item['general_support'] = $x['general_support'];
            $item['display_role_ar'] = 'عناية مكمّلة';
            $item['routine_step_code'] = 'supplement';
            $additions[] = $item;
        }

        $schedule = $this->buildUsageSchedule($cards);

        return [
            'schema_version' => '1',
            'rules_version' => self::VERSION,
            'scenario_key' => $scenarioKey,
            'answers' => $answers,
            'core' => $cards,
            'supplements' => $additions,
            'missing_roles' => $core['missing'],
            'selection_complete' => empty($core['missing']),
            'usage_schedule' => $schedule,
        ];
    }

    /**
     * Resolve display_role_ar when a fallback SKU is used outside the 728 full-stock matrix.
     */
    protected function resolveDisplayRoleAr(string $sku, array $roles, array $effectiveGoals): string
    {
        if (in_array('cleanser', $roles, true)) {
            return 'الغسول';
        }
        if (in_array('moisturizer', $roles, true)) {
            return 'المرطب';
        }
        if (in_array('sunscreen', $roles, true)) {
            return 'واقي الشمس';
        }
        if (in_array('supplement', $roles, true)) {
            return 'عناية مكمّلة';
        }

        $labelsForSku = $this->displayLabels[$sku] ?? [];
        if (count($labelsForSku) === 1) {
            return $labelsForSku[0];
        }

        if ($roles === ['toner']) {
            return 'التونر';
        }

        // Treatment role label
        [$primary, $secondary] = $effectiveGoals;
        $goalNames = [];
        if (in_array('treatment1', $roles, true) && $primary) {
            $goalNames[] = self::TREATMENT_GOAL_DISPLAY_AR[$primary] ?? $primary;
        }
        if (in_array('treatment2', $roles, true) && $secondary && $secondary !== $primary) {
            $goalNames[] = self::TREATMENT_GOAL_DISPLAY_AR[$secondary] ?? $secondary;
        }
        $goalNames = array_values(array_unique($goalNames));

        $cat = strtolower($this->masterBySku[$sku]['Category'] ?? 'serum');
        $prefix = match (true) {
            in_array('toner', $roles, true) || str_contains($cat, 'toner') => 'تونر',
            str_contains($cat, 'eye') => 'كريم عين',
            str_contains($cat, 'cream') || str_contains($cat, 'moisturizer') => 'كريم',
            str_contains($cat, 'ampoule') => 'أمبول',
            str_contains($cat, 'oil') => 'زيت',
            default => 'سيروم',
        };

        if (!empty($goalNames)) {
            return $prefix . ' للعناية بـ' . implode(' و', $goalNames);
        }

        return $labelsForSku[0] ?? 'العناية المركّزة';
    }

    /**
     * Exact port of frontend/presentation.js::usagePlan() & guide()
     */
    public function buildUsageSchedule(array $selectedItems): array
    {
        $itemsWithPolicy = array_values(array_filter($selectedItems, fn (array $p) => !empty($p['usage_policy'])));

        $sortByOrder = function (array $list): array {
            usort($list, function (array $a, array $b) {
                $oa = (int) ($a['usage_policy']['order'] ?? 0);
                $ob = (int) ($b['usage_policy']['order'] ?? 0);
                if ($oa !== $ob) {
                    return $oa <=> $ob;
                }
                return strcmp($a['sku'], $b['sku']);
            });
            return $list;
        };

        $morning = [];
        $evening = [];

        foreach ($itemsWithPolicy as $p) {
            $period = $p['usage_policy']['period'] ?? 'both';
            $family = $p['usage_policy']['family'] ?? 'daily';

            if ($period === 'morning' || ($period === 'both' && $family !== 'gradual')) {
                $morning[] = $p;
            }
            if ($period === 'evening' || ($period === 'both' && $family !== 'gradual')) {
                $evening[] = $p;
            }
            // Azelaic / gradual with period=both: show once in the morning
            if ($family === 'gradual' && $period === 'both') {
                $morning[] = $p;
            }
        }

        $morning = $sortByOrder($morning);
        $evening = $sortByOrder($evening);

        $retinoids = array_values(array_filter($itemsWithPolicy, fn (array $p) => ($p['usage_policy']['family'] ?? '') === 'retinoid'));
        $exfoliants = array_values(array_filter($itemsWithPolicy, fn (array $p) => ($p['usage_policy']['family'] ?? '') === 'exfoliant'));

        $separateNights = (count($retinoids) + count($exfoliants)) > 1;
        $eveningNote = $separateNights ? 'المقشّر والريتينويد: ليالٍ منفصلة، وليس معًا. لا تجمع ريتينويدين.' : '';

        $formatStep = fn (array $p) => [
            'sku' => $p['sku'],
            'brand' => $p['brand'],
            'name' => $p['name'],
            'display_role_ar' => $p['display_role_ar'],
            'usage_short_ar' => $p['usage_short_ar'] ?? $p['usage_text_ar'] ?? '',
            'usage_notice_ar' => $p['usage_notice_ar'] ?? '',
            'order' => $p['usage_policy']['order'] ?? 0,
            'family' => $p['usage_policy']['family'] ?? 'daily',
            'label' => '', // Empty per canonical R479 spec so redundant inline badge is not rendered in usage view
        ];

        return [
            'morning' => [
                'slot' => 'morning',
                'label' => 'صباحًا',
                'note' => '',
                'steps' => array_map($formatStep, $morning),
            ],
            'evening' => [
                'slot' => 'evening',
                'label' => 'مساءً',
                'note' => $eveningNote,
                'steps' => array_map($formatStep, $evening),
            ],
            'separate_nights_warning' => $eveningNote ?: null,
        ];
    }
}


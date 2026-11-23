<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CleanupServerFilesCommand extends Command
{
    protected $signature = 'kcode:cleanup-extra
                            {--apply : Actually delete the matched files/directories instead of running in dry-run mode}
                            {--docs : Include unreferenced client HTML prototypes, docs, and API guide files (~50 MB)}
                            {--seeders : Include database/seeders, tests, phpunit.xml, and exicel/kcode_products.json (~52 MB, for production servers not running tests)}
                            {--force : Skip interactive confirmation prompt when --apply is set}
                            {--base= : Override project base path (primarily for testing)}';

    protected $description = 'Verify dependencies across runtime/tests and safely inspect or remove unnecessary extra files on the server.';

    private const TIER_DEBUG = [
        'test-smtp.php',
        'scratch',
        'app/Http/Resources/ProductResource.php',
        'app/Models/ProductQuizProfile.php',
        'database/migrations/2026_11_23_120000_create_product_quiz_profiles_and_configs_tables.php',
    ];

    private const TIER_DOCS = [
        'KCODE_R422_I_DESKTOP (2).html',
        'KCODE_CART_DESKTOP_R17 (1).html',
        'KCODE_PLP_DESKTOP_R36.html',
        'KCODE_PDP_DESKTOP_R34.html',
        'KCODE_BACKEND_ACTION_PLAN.html',
        'KCODE_Final_Test_Report_2026-11-16.html',
        'docs',
        'data',
        'results',
        'scratch_technical',
        'scripts',
        'ui',
        'BACKEND_REQUIRED_EDITS_AR.md',
        'cart_fields_report.md',
        'CATEGORIES_DESCRIPTION_API_GUIDE.md',
        'CUSTOMER_TESTIMONIALS_API_GUIDE.md',
        'database_architecture.md',
        'DEVELOPER_RESPONSE_TEMPLATE_AR.txt',
        'FRONTEND_PRODUCT_API_GUIDE.md',
        'KCODE_R479_FULL_GUIDE_AR.md',
        'PDP_AND_MODAL_API_GUIDE.md',
        'QUIZ_FLOW_EXPLANATION.md',
        'QUIZ_R479_GAP_ANALYSIS_AR.md',
        'SERVER_DEPLOYMENT_GUIDE.md',
        'SHOP_BY_SKIN_TYPE_API_GUIDE.md',
        'README.md',
    ];

    private const TIER_SEEDERS_AND_TESTS = [
        'database/seeders',
        'exicel/kcode_products.json',
        'tests',
        'phpunit.xml',
    ];

    private const PROTECTED_PATHS = [
        '.env',
        '.env.example',
        '.git',
        '.htaccess',
        'app',
        'artisan',
        'bootstrap',
        'config',
        'database/migrations',
        'database/migrations/fixtures/r479_quiz_bootstrap.json.gz',
        'exicel/KCODE_FINAL_10_10_POLISHED - Product_Master.csv',
        'index.php',
        'lang',
        'public',
        'resources',
        'routes',
        'storage',
        'vendor',
    ];

    public function handle(): int
    {
        $basePath = rtrim((string) ($this->option('base') ?: base_path()), "\\/");
        $apply = (bool) $this->option('apply');
        $includeDocs = (bool) $this->option('docs');
        $includeSeeders = (bool) $this->option('seeders');

        $runtimeRefs = $this->scanRuntimeReferences($basePath);
        $testRefs = $this->scanTestReferences($basePath);

        $this->info('=== KCODE Pre-Flight Dependency Audit ===');
        $this->line('Protected runtime asset references detected: ' . (empty($runtimeRefs) ? 'none' : implode(', ', array_keys($runtimeRefs))));
        $this->line('Test/seeder asset references detected: ' . (empty($testRefs) ? 'none' : implode(', ', array_keys($testRefs))));
        $this->newLine();

        $candidates = $this->collectCandidates($basePath, $includeDocs, $includeSeeders);
        $safeTargets = [];
        $blockedTargets = [];
        $totalBytes = 0;

        foreach ($candidates as $rel => $tier) {
            $norm = str_replace('\\', '/', $rel);
            if ($this->isProtected($norm, $runtimeRefs)) {
                $blockedTargets[] = [$norm, $runtimeRefs[$norm] ?? 'Protected system/runtime path'];
                continue;
            }

            $full = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $norm);
            if (!file_exists($full) && !is_link($full)) {
                continue;
            }

            $bytes = $this->measureBytes($full);
            $totalBytes += $bytes;
            $safeTargets[] = [
                'path' => $norm,
                'full' => $full,
                'tier' => $tier,
                'bytes' => $bytes,
                'is_dir' => is_dir($full) && !is_link($full),
                'test_dep' => $testRefs[$norm] ?? null,
            ];
        }

        if (!empty($blockedTargets)) {
            $this->warn('Skipped protected/live-linked paths:');
            foreach ($blockedTargets as [$p, $reason]) {
                $this->line("  [KEEP] {$p} -> {$reason}");
            }
            $this->newLine();
        }

        if (empty($safeTargets)) {
            $this->info('Nothing extra found to clean up for the selected tiers.');
            return self::SUCCESS;
        }

        $this->info(($apply ? 'Targets selected for deletion' : 'Dry-Run Preview (no files will be deleted yet)') . ':');
        foreach ($safeTargets as $t) {
            $sizeLabel = $this->formatBytes($t['bytes']);
            $note = $t['test_dep'] ? " (Note: used by {$t['test_dep']})" : '';
            $this->line("  [{$t['tier']}] {$t['path']} ({$sizeLabel}){$note}");
        }

        $this->newLine();
        $this->info('Total reclaimable space: ' . $this->formatBytes($totalBytes));

        if (!$apply) {
            $this->comment('Dry-run complete. Re-run with --apply (and optional --docs / --seeders) on the target server to delete.');
            return self::SUCCESS;
        }

        if (!$this->option('force') && !$this->confirm('Are you sure you want to permanently delete the listed paths?', false)) {
            $this->warn('Aborted by user. Zero files deleted.');
            return self::SUCCESS;
        }

        $deleted = 0;
        foreach ($safeTargets as $t) {
            $ok = $t['is_dir']
                ? File::deleteDirectory($t['full'])
                : File::delete($t['full']);

            if ($ok) {
                $deleted++;
                $this->line("  [DELETED] {$t['path']}");
            } else {
                $this->error("  [FAILED]  {$t['path']}");
            }
        }

        $this->info("Cleanup completed: {$deleted}/" . count($safeTargets) . ' target(s) removed.');
        return self::SUCCESS;
    }

    /**
     * Collect normalized relative candidate paths mapped to their tier label.
     *
     * @return array<string, string>
     */
    private function collectCandidates(string $basePath, bool $includeDocs, bool $includeSeeders): array
    {
        $candidates = [];

        foreach (self::TIER_DEBUG as $rel) {
            $candidates[$rel] = 'debug';
        }

        foreach (glob($basePath . DIRECTORY_SEPARATOR . 'scratch_*') ?: [] as $match) {
            $name = basename($match);
            if ($name !== 'scratch_technical') {
                $candidates[$name] = 'debug';
            }
        }

        if ($includeDocs) {
            foreach (self::TIER_DOCS as $rel) {
                $candidates[$rel] = 'docs';
            }
        }

        if ($includeSeeders) {
            foreach (self::TIER_SEEDERS_AND_TESTS as $rel) {
                $candidates[$rel] = 'seeders';
            }
        }

        return $candidates;
    }

    /**
     * Scan live runtime directories (app, routes, bootstrap, config, migrations) for asset references.
     *
     * @return array<string, string>
     */
    private function scanRuntimeReferences(string $basePath): array
    {
        $refs = [];
        $files = $this->listPhpFiles($basePath, ['app', 'routes', 'bootstrap', 'config', 'database/migrations']);

        $knownWatched = [
            'exicel/KCODE_FINAL_10_10_POLISHED - Product_Master.csv',
            'exicel/kcode_products.json',
            'database/migrations/fixtures/r479_quiz_bootstrap.json.gz',
            'database/seeders',
            'scratch_technical',
        ];

        foreach ($files as $file) {
            if (realpath($file) === realpath(__FILE__)) {
                continue;
            }
            $content = @file_get_contents($file);
            if (!$content) {
                continue;
            }
            foreach ($knownWatched as $needle) {
                if (str_contains($content, $needle)) {
                    $relSource = ltrim(str_replace(['\\', $basePath], ['/', ''], $file), '/');
                    $refs[$needle] = "referenced by {$relSource}";
                }
            }
        }

        return $refs;
    }

    /**
     * Detect files referenced by PHPUnit tests or seeders so callers know before pruning test assets.
     *
     * @return array<string, string>
     */
    private function scanTestReferences(string $basePath): array
    {
        $refs = [];
        $files = $this->listPhpFiles($basePath, ['tests', 'database/seeders']);

        foreach ($files as $file) {
            if (realpath($file) === realpath(__FILE__)) {
                continue;
            }
            $content = @file_get_contents($file);
            if (!$content) {
                continue;
            }
            if (str_contains($content, 'kcode_products.json')) {
                $refs['exicel/kcode_products.json'] = 'R479EnglishBuilder / Seeders';
            }
            if (str_contains($content, 'Database\\Seeders') || str_contains($content, 'seeders/data')) {
                $refs['database/seeders'] = 'tests/Feature localization & seeder suites';
            }
        }

        return $refs;
    }

    private function isProtected(string $normRel, array $runtimeRefs): bool
    {
        if (isset($runtimeRefs[$normRel])) {
            return true;
        }

        foreach (self::PROTECTED_PATHS as $protected) {
            if ($normRel === $protected || str_starts_with($protected, $normRel . '/')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, string>  $subDirs
     * @return array<int, string>
     */
    private function listPhpFiles(string $basePath, array $subDirs): array
    {
        $out = [];
        foreach ($subDirs as $dir) {
            $fullDir = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $dir);
            if (!is_dir($fullDir)) {
                continue;
            }
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fullDir));
            foreach ($it as $file) {
                if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
                    $out[] = $file->getPathname();
                }
            }
        }

        return $out;
    }

    private function measureBytes(string $path): int
    {
        if (is_link($path)) {
            return 0;
        }
        if (is_file($path)) {
            return (int) (@filesize($path) ?: 0);
        }
        if (!is_dir($path)) {
            return 0;
        }

        $total = 0;
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->isFile()) {
                $total += (int) ($file->getSize() ?: 0);
            }
        }

        return $total;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }
}

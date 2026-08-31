<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FuzzyImageMatcherService;

class MatchImagesFolderCommand extends Command
{
    protected $signature = 'kcode:match-folder {folderPath?}';
    protected $description = 'Fuzzy match and assign all image files inside a folder to DB products, brands, and categories';

    public function handle()
    {
        $folderPath = $this->argument('folderPath');

        if (!$folderPath) {
            $folderPath = $this->ask('Please enter the folder path containing your images (e.g. C:\Users\Dell\Downloads\my_images)', 'C:\Users\Dell\Downloads\KCODE_Homepage_Developer_V24_FINAL\KCODE_Homepage_Developer_V24_FINAL\assets');
        }

        $this->info("Scanning and fuzzy-matching image files in: {$folderPath}...");

        $res = FuzzyImageMatcherService::processFolder($folderPath);

        if (!$res['status']) {
            $this->error($res['message']);
            return;
        }

        $this->info("Scan Completed!");
        $this->info("Total image files found: {$res['total_files']}");
        $this->info("Successfully matched & assigned: {$res['matched_files']}");

        $this->newLine();
        $this->info("--- Detailed Matches ---");
        foreach ($res['details'] as $item) {
            if ($item['matched']) {
                $this->line("✔ [{$item['type']}] {$item['file']} ➔ {$item['target']} (Score: {$item['score']}%)");
            } else {
                $this->warn("✖ [Unmatched] {$item['file']}");
            }
        }
    }
}

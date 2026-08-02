<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and update public/sitemap.xml and public/robots.txt files';

    /**
     * Execute the console command.
     */
    public function handle(SitemapService $sitemapService): int
    {
        $this->info('Generating sitemap.xml and robots.txt...');
        $sitemapService->saveToPublic();
        $this->info('✅ public/sitemap.xml and public/robots.txt updated successfully!');

        return Command::SUCCESS;
    }
}

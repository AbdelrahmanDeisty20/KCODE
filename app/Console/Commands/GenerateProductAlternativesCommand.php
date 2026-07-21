<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductService;

class GenerateProductAlternativesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kcode:generate-alternatives {--product_id= : Generate alternatives for a specific product ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and sync product alternatives in the database based on their routine steps';

    /**
     * Execute the console command.
     */
    public function handle(ProductService $productService)
    {
        $productId = $this->option('product_id');

        if ($productId) {
            $this->info("Generating alternatives for product ID: {$productId}...");
        } else {
            $this->info('Generating alternatives for all products...');
        }

        $result = $productService->generateAlternatives($productId);

        if ($result['status']) {
            $this->info($result['message']);
        } else {
            $this->error($result['message']);
        }
    }
}

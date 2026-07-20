<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear all image folders so fresh images are downloaded
        $this->command->info('🗑️  Clearing old image folders...');
        $imageDirs = [
            'categories',
            'sub_categories',
            'products',
            'skin_types',
            'concerns',
            'routine-goals',
            'pages',
            'product_images',
        ];

        foreach ($imageDirs as $dir) {
            $path = storage_path('app/public/' . $dir);
            if (File::exists($path)) {
                File::deleteDirectory($path);
                File::makeDirectory($path, 0755, true, true);
                $this->command->line("  Cleared: storage/app/public/{$dir}/");
            }
        }
        $this->command->info('✅ Image folders cleared.' . PHP_EOL);

        $this->call([
            SkinTypeSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            ConcernSeeder::class,
            RoutineGoalSeeder::class,
            ProductSeeder::class,
            QuizQuestionSeeder::class,
            ReviewSeeder::class,
            OfferSeeder::class,
            LoyaltyLevelsSeeder::class,
        ]);

        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'image' => 'default.png',
            ]
        );
    }
}
// php artisan db:seed --class=LoyaltyLevelsSeeder
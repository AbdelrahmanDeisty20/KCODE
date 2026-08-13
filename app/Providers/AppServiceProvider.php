<?php

namespace App\Providers;

use App\Models\Blog;
use App\Models\Offer;
use App\Observers\BlogObserver;
use App\Observers\OfferObserver;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Offer::observe(OfferObserver::class);
        Blog::observe(BlogObserver::class);

        if (class_exists(LanguageSwitch::class)) {
            LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
                $switch
                    ->locales(['ar', 'en'])
                    ->labels([
                        'ar' => 'العربية',
                        'en' => 'English',
                    ]);
            });
        }
    }
}

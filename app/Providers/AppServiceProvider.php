<?php

namespace App\Providers;

use App\Models\Blog;
use App\Models\Coupon;
use App\Models\Offer;
use App\Models\Order;
use App\Observers\BlogObserver;
use App\Observers\CouponObserver;
use App\Observers\OfferObserver;
use App\Observers\OrderObserver;
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
        Order::observe(OrderObserver::class);
        Coupon::observe(CouponObserver::class);

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

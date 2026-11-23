<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\SubCategory;
use App\Models\User;
use App\Observers\AddressObserver;
use App\Observers\BlogObserver;
use App\Observers\BrandObserver;
use App\Observers\CategoryObserver;
use App\Observers\CouponObserver;
use App\Observers\OfferObserver;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use App\Observers\ReviewObserver;
use App\Observers\StaticCacheObserver;
use App\Observers\SubCategoryObserver;
use App\Observers\UserObserver;
use App\Services\ActivityLogger;
use App\Support\StaticCache;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
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
        User::observe(UserObserver::class);
        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);
        SubCategory::observe(SubCategoryObserver::class);
        Brand::observe(BrandObserver::class);
        Review::observe(ReviewObserver::class);
        Address::observe(AddressObserver::class);

        // Automatically invalidate static data caches whenever changed from Dashboard or anywhere else
        foreach (array_keys(StaticCache::MODEL_GROUPS) as $cachedModelClass) {
            if (class_exists($cachedModelClass)) {
                $cachedModelClass::observe(StaticCacheObserver::class);
            }
        }

        // Listen for Web & Filament Admin Logins
        Event::listen(Login::class, function (Login $event) {
            $user = $event->user;
            if (!$user) return;
            $guard = $event->guard ?? 'web';
            if ($guard !== 'sanctum') {
                ActivityLogger::log(
                    event: 'login',
                    description: "قام المشرف/المستخدم [{$user->name}] بتسجيل الدخول إلى لوحة التحكم ({$guard})",
                    subjectType: 'User',
                    subjectId: $user->id,
                    newValues: ['guard' => $guard],
                    userId: $user->id,
                    userName: $user->name
                );
            }
        });

        // Listen for Web & Filament Admin Logouts
        Event::listen(Logout::class, function (Logout $event) {
            $user = $event->user;
            if (!$user) return;
            $guard = $event->guard ?? 'web';
            if ($guard !== 'sanctum') {
                ActivityLogger::log(
                    event: 'logout',
                    description: "قام المشرف/المستخدم [{$user->name}] بتسجيل الخروج ({$guard})",
                    subjectType: 'User',
                    subjectId: $user->id,
                    newValues: ['guard' => $guard],
                    userId: $user->id,
                    userName: $user->name
                );
            }
        });

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

        $this->configureRateLimiting();
    }

    /**
     * Protect public & authenticated API routes against burst floods and abuse without throttling normal traffic.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api-general', function (Request $request) {
            return Limit::perMinute(240)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('api-sensitive', function (Request $request) {
            return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('api-auth', function (Request $request) {
            return Limit::perMinute(15)->by($request->user()?->id ?: $request->ip());
        });
    }
}

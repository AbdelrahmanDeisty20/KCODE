<?php

use App\Http\Controllers\API\AUHT\AuthController;
use App\Http\Controllers\API\AUHT\ForgetPasswordController;
use App\Http\Controllers\API\Genral\BrandController;
use App\Http\Controllers\API\Genral\CartController;
use App\Http\Controllers\API\Genral\CategoryController;
use App\Http\Controllers\API\Genral\ConcernController;
use App\Http\Controllers\API\Genral\FavouriteController;
use App\Http\Controllers\API\Genral\OfferController;
use App\Http\Controllers\API\Genral\PageController;
use App\Http\Controllers\API\Genral\ProductController;
use App\Http\Controllers\API\Genral\QuizController;
use App\Http\Controllers\API\Genral\ReviewController;
use App\Http\Controllers\API\Genral\RoutineController;
use App\Http\Controllers\API\Genral\RoutineGoalController;
use App\Http\Controllers\API\Genral\SkinController;
use App\Http\Controllers\API\Genral\LoyaltyController;
use App\Http\Controllers\API\Genral\NewsletterController;
use App\Http\Controllers\API\Genral\FaqController;
use App\Http\Controllers\API\Genral\PolicyController;
use App\Http\Controllers\API\Genral\LocationController;
use App\Http\Controllers\API\Genral\AddressController;
use App\Http\Middleware\SetLang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::middleware([SetLang::class])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/verify-otp', 'verifyOtp');
        Route::post('/resend-otp', 'resendOtp');
        Route::post('/login', 'login');
        Route::get('redirect/{provider}', 'redirectToProvider');
        Route::get('callback/{provider}', 'handleProviderCallback');
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/profile', 'profile');
            Route::post('/update-profile', 'updateProfile');
            Route::post('/logout', 'logout');
            Route::post('/logout-all-devices', 'logoutAllDevice');
        });
    });
    Route::controller(ForgetPasswordController::class)->group(function () {
        Route::post('/forget-password', 'SendEmail');
        Route::post('/forget-password/verify-otp', 'VerifyOtp');
        Route::post('/forget-password/reset', 'ResetPassword');
        Route::post('/forget-password/resend-otp', 'resendOtp');
    });
    Route::post('/refresh-token', [AuthController::class, 'refresh']);

    // Brands Routes
    Route::controller(BrandController::class)->group(function () {
        Route::get('/brands', 'index');
        Route::get('/brands/{id}', 'show');
    });

    // Categories Routes
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index');
        Route::get('/category/{id}', 'show');
    });
    // Skins Routes
    Route::controller(SkinController::class)->group(function () {
        Route::get('/skin-types', 'SkinTypes');
        Route::get('/skin-type/{id}', 'show');
    });
    // Sub Categories Routes
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/sub-categories', 'sub_categories');
    });
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index');
        Route::get('/products/filter', 'filter');
        Route::get('/products/best-sellers', 'bestSellers');
        Route::get('/products/by-skin-type/{skin_type_id}', 'bySkinType');
        Route::get('/products/by-goal/{goal_id}', 'byGoal');
        Route::get('/products/search', 'search');
        Route::get('/products/{id}/alternatives', 'alternatives');
        Route::get('/products/{id}', 'show');
    });
    // Routine goals
    Route::controller(RoutineGoalController::class)->group(function () {
        Route::get('goals', 'index');
    });
    // Concerns Routes
    Route::controller(ConcernController::class)->group(function () {
        Route::get('/concerns', 'index');
        Route::get('/concerns/{id}', 'show');
    });
    // Offers Routes
    Route::controller(OfferController::class)->group(function () {
        Route::get('/offers', 'index');
    });

    // Quiz Routes
    Route::controller(QuizController::class)->group(function () {
        Route::get('/quiz/questions', 'getQuestions');
        Route::post('/quiz/submit-answers', 'evaluate')->middleware('auth:sanctum');
    });

    // Routine Routes
    Route::controller(RoutineController::class)->group(function () {
        Route::get('/routine', 'getRoutine')->middleware('auth:sanctum');
        Route::get('/routine/suggested', 'getSuggestedRoutine')->middleware('auth:sanctum');
        Route::post('/routine/confirm', 'saveFinalRoutine')->middleware('auth:sanctum');
        Route::post('/routine/select-alternative', 'selectAlternative')->middleware('auth:sanctum');
        Route::delete('/routine', 'deleteRoutine')->middleware('auth:sanctum');
        Route::post('/routine/delete', 'deleteRoutine')->middleware('auth:sanctum');
    });

    // Loyalty Routes
    Route::controller(LoyaltyController::class)->group(function () {
        Route::get('/loyalty/profile', 'getLoyaltyProfile')->middleware('auth:sanctum');
        Route::get('/loyalty/levels', 'getLoyaltyLevels');
    });

    // Cart Routes
    Route::controller(CartController::class)->group(function () {
        Route::post('/cart/add-bulk', 'addBulk');
    });

    // Newsletter Routes
    Route::controller(NewsletterController::class)->group(function () {
        Route::post('/newsletter/subscribe', 'subscribe');
    });

    // Page Routes
    Route::controller(PageController::class)->group(function () {
        Route::get('/pages/about', 'getAboutUs');
        Route::get('/pages/{type}', 'getPageByType');
    });

    // FAQ & Policy Routes
    Route::controller(FaqController::class)->group(function () {
        Route::get('/faqs', 'getFaqs');
    });

    Route::controller(PolicyController::class)->group(function () {
        Route::get('/policies/shipping', 'getShippingPolicy');
        Route::get('/policies/return', 'getReturnPolicy');
        Route::get('/policies/terms', 'getTermsOfUse');
        Route::get('/policies/privacy', 'getPrivacyPolicy');
        Route::get('/policies/coupons', 'getCouponPolicy');
        Route::get('/policies/points-program', 'getPointsProgramPolicy');
    });

    // Location Routes
    Route::controller(LocationController::class)->group(function () {
        Route::get('/countries', 'getCountries');
        Route::get('/countries/{countryId}/states', 'getStates');
        Route::get('/states/{stateId}/cities', 'getCities');
    });

    // Favorites & Reviews Routes (Protected)
    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(FavouriteController::class)->group(function () {
            Route::get('/favorites', 'index');
            Route::post('/favorites/{product_id}', 'add');
            Route::delete('/favorites/{product_id}', 'remove');
        });

        Route::controller(ReviewController::class)->group(function () {
            Route::get('/my-reviews', 'myReviews');
            Route::post('/reviews/add', 'store');
            Route::post('/reviews/website', 'storeWebsiteReview');
            Route::put('/reviews/{id}', 'update');
            Route::delete('/reviews/{id}', 'destroy');
        });

        Route::controller(AddressController::class)->group(function () {
            Route::get('/addresses', 'index');
            Route::post('/addresses', 'store');
            Route::put('/addresses/{id}', 'update');
            Route::delete('/addresses/{id}', 'destroy');
            Route::patch('/addresses/{id}/default', 'setDefault');
        });
    });
    Route::controller(ReviewController::class)->group(function () {
        Route::get('/reviews/general', 'genralReview');
    });
});

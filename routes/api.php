<?php

use App\Http\Controllers\API\AUHT\AuthController;
use App\Http\Controllers\API\AUHT\ForgetPasswordController;
use App\Http\Controllers\API\Genral\BrandController;
use App\Http\Controllers\API\Genral\CategoryController;
use App\Http\Controllers\API\Genral\ProductController;
use App\Http\Controllers\API\Genral\SkinController;
use App\Http\Controllers\API\Genral\CartController;
use App\Http\Controllers\API\Genral\QuizController;
use App\Http\Controllers\API\Genral\RoutineController;
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
        Route::get('/products/{id}', 'show');
        Route::get('/products/{id}/alternatives', 'alternatives')->middleware('auth:sanctum');
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
    });

    // Cart Routes
    Route::controller(CartController::class)->group(function () {
        Route::post('/cart/add-bulk', 'addBulk');
    });
});

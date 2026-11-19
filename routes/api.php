<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/me', [App\Http\Controllers\Auth\SanctumAuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/logout', [App\Http\Controllers\Auth\SanctumAuthController::class, 'userLogout'])->middleware('auth:sanctum');
Route::get('/profile-setting', [App\Http\Controllers\API\SettingController::class, 'getGlobalSetting']);
Route::prefix('user')->group(function () {
    Route::controller(App\Http\Controllers\Auth\SanctumAuthController::class)->group(function () {
        Route::post('/login', 'userLogin');
        Route::post('/register', 'userRegister');
        Route::post('/send-otp', 'sendOtp');
        Route::post('/check-otp', 'checkOtp');
        Route::post('/reset-password', 'resetPassword');
    });
});

Route::prefix('frontend/v1')->group(function () {
    Route::get('/setting', [App\Http\Controllers\API\SettingController::class, 'getAllSetting']);
    Route::get('/footer-colomn', [App\Http\Controllers\API\FooterController::class, 'footer']);
    Route::get('/hero-section', [App\Http\Controllers\API\Public\FrontendHomeController::class, 'heroSection']);
    Route::get('/slider', [App\Http\Controllers\API\Public\FrontendHomeController::class, 'slider']);
    Route::get('/counter-section', [App\Http\Controllers\API\Public\FrontendHomeController::class, 'counter']);
    Route::get('/testimonial', [App\Http\Controllers\API\Public\FrontendHomeController::class, 'testimonial']);
    Route::get('/faqs', [App\Http\Controllers\API\Public\FrontendHomeController::class, 'faqs']);
    Route::post('/subscribe-newsletter', [App\Http\Controllers\API\NewsletterController::class, 'store']);
    Route::get('/page/{slug}', [App\Http\Controllers\API\Public\FrontendHomeController::class, 'getCustomPage']);
    Route::get('/projects', [App\Http\Controllers\API\ProjectController::class, 'index']);
    Route::get('/categories', [App\Http\Controllers\API\CategoryController::class, 'index']);
    Route::get('/languages', [App\Http\Controllers\API\LanguageController::class, 'index']);
    Route::get('/testimonials', [App\Http\Controllers\API\TestimonialController::class, 'index']);
    Route::get('/experiences', [App\Http\Controllers\API\ExperienceController::class, 'index']);
    Route::get('/projects/{project}', [App\Http\Controllers\API\ProjectController::class, 'show']);
    Route::get('/related-projects/{project}', [App\Http\Controllers\API\ProjectController::class, 'relatedProjects']);
    Route::post('/project_rating', [App\Http\Controllers\API\TestimonialController::class, 'store']);
    Route::post('/store-mails', [App\Http\Controllers\API\ContactController::class, 'store']);
    Route::get('/cv', [App\Http\Controllers\API\Public\FrontendHomeController::class, 'downloadCV']);
});

Route::prefix('admin')->group(function () {
    Route::post('/login', [App\Http\Controllers\Auth\SanctumAuthController::class, 'adminLogin']);
    Route::middleware(['auth:sanctum', 'abilities:admin'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\API\DashboardController::class, 'dashboard']);
        Route::get('/setting', [App\Http\Controllers\API\SettingController::class, 'getAllSetting']);
        Route::post('/save-setting', [App\Http\Controllers\API\SettingController::class, 'saveSetting']);
        Route::post('/profile-setting', [App\Http\Controllers\API\SettingController::class, 'profileSetting']);
        Route::get('/hero-section-inactive/{id}', [App\Http\Controllers\API\HeroSectionController::class, 'inActiveStatus']);
        Route::get('/hero-section-active/{id}', [App\Http\Controllers\API\HeroSectionController::class, 'activeStatus']);
        Route::get('/counter-inactive-status/{id}', [App\Http\Controllers\API\CounterController::class, 'inActiveStatus']);
        Route::get('/counter-active-status/{id}', [App\Http\Controllers\API\CounterController::class, 'activeStatus']);
        Route::apiResource('page', App\Http\Controllers\API\PageController::class);
        Route::apiResource('category', App\Http\Controllers\API\CategoryController::class);
        Route::apiResource('footer', App\Http\Controllers\API\FooterController::class);
        Route::apiResource('slider', App\Http\Controllers\API\SliderController::class);
        Route::apiResource('hero-section', App\Http\Controllers\API\HeroSectionController::class);
        Route::apiResource('counter', App\Http\Controllers\API\CounterController::class);
        Route::apiResource('testimonials', App\Http\Controllers\API\TestimonialController::class);
        Route::get('/testimonial-inactive/{id}', [App\Http\Controllers\API\TestimonialController::class, 'inActiveStatus']);
        Route::get('/testimonial-active/{id}', [App\Http\Controllers\API\TestimonialController::class, 'activeStatus']);
        Route::apiResource('newsletter', App\Http\Controllers\API\NewsletterController::class);
        Route::apiResource('language', App\Http\Controllers\API\LanguageController::class);
        Route::apiResource('project', App\Http\Controllers\API\ProjectController::class);
        Route::post('/project/{project}/images', [App\Http\Controllers\API\ProjectController::class, 'addImages']);
        Route::delete('/project/{project}/images/{image}', [App\Http\Controllers\API\ProjectController::class, 'deleteImage']);
        Route::apiResource('experience', App\Http\Controllers\API\ExperienceController::class);
        Route::get('/experience-inactive/{id}', [App\Http\Controllers\API\ExperienceController::class, 'inActiveStatus']);
        Route::get('/experience-active/{id}', [App\Http\Controllers\API\ExperienceController::class, 'activeStatus']);
        Route::get('/users', [App\Http\Controllers\API\DashboardController::class, 'users']);
        Route::get('/contact-unread-count', [App\Http\Controllers\API\ContactController::class, 'unreadCount']);
        Route::get('/contact-latest-unread', [App\Http\Controllers\API\ContactController::class, 'latestUnread']);
        Route::patch('/contact-mark-as-read/{contact}', [App\Http\Controllers\API\ContactController::class, 'markAsRead']);
        Route::apiResource('contact', App\Http\Controllers\API\ContactController::class)->only(['index', 'show', 'destroy']);
        Route::post('/contact/{contact}/reply', [App\Http\Controllers\API\ContactController::class, 'reply']);
        // CV Routes
        Route::get('/cv', [App\Http\Controllers\API\CVController::class, 'index']);
        Route::post('/cv/upload', [App\Http\Controllers\API\CVController::class, 'upload']);
        Route::delete('/cv/{id}', [App\Http\Controllers\API\CVController::class, 'destroy']);
    });
});

Route::post('/success', [\App\Http\Controllers\SslCommerzPaymentController::class, 'success']);
Route::post('/fail', [\App\Http\Controllers\SslCommerzPaymentController::class, 'fail']);
Route::post('/cancel', [\App\Http\Controllers\SslCommerzPaymentController::class, 'cancel']);
Route::post('/ipn', [\App\Http\Controllers\SslCommerzPaymentController::class, 'ipn']);

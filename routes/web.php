<?php

use App\Http\Controllers\Admin\KnowledgeArticleController as AdminKnowledgeArticleController;
use App\Http\Controllers\Admin\KnowledgeCommentModerationController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\SellerVerificationController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BuyerRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\KnowledgeCommentController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ListingSubmissionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SellerReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/marketplace', [ListingController::class, 'index'])->name('listings.index');
Route::get('/marketplace/{listing:slug}', [ListingController::class, 'show'])->name('listings.show');
Route::get('/buyer-requests', [BuyerRequestController::class, 'index'])->name('buyer-requests.index');
Route::get('/buyer-requests/{buyerRequest:slug}', [BuyerRequestController::class, 'show'])->name('buyer-requests.show');
Route::get('/knowledge', [ArticleController::class, 'index'])->name('knowledge.index');
Route::get('/knowledge/{knowledgeArticle:slug}', [ArticleController::class, 'show'])->name('knowledge.show');
Route::get('/businesses', [BusinessController::class, 'index'])->name('businesses.index');
Route::get('/businesses/{business}', [BusinessController::class, 'show'])->name('businesses.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/knowledge/{knowledgeArticle:slug}/comments', [KnowledgeCommentController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('knowledge.comments.store');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/inbox', [InboxController::class, 'index'])->name('inbox.index');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::post('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::post('/inbox/enquiries/{enquiry}/reply', [InboxController::class, 'reply'])->name('inbox.enquiries.reply');
    Route::patch('/inbox/offers/{buyerOffer}', [InboxController::class, 'respondToOffer'])->name('inbox.offers.respond');
    Route::get('/admin', AdminDashboardController::class)->name('admin.dashboard');
    Route::get('/listings/create', [ListingSubmissionController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingSubmissionController::class, 'store'])->name('listings.store');
    Route::post('/buyer-requests/{buyerRequest}/offers', [BuyerRequestController::class, 'storeOffer'])->middleware('throttle:10,1')->name('buyer-requests.offers.store');
    Route::post('/enquiries', [EnquiryController::class, 'store'])->middleware('throttle:5,1')->name('enquiries.store');
    Route::post('/reports', [ReportController::class, 'store'])->middleware('throttle:5,1')->name('reports.store');
    Route::post('/inbox/offers/{buyerOffer}/review', [SellerReviewController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('inbox.offers.review');

    Route::prefix('/admin')->name('admin.')->group(function () {
        Route::get('/seller-verification', [SellerVerificationController::class, 'index'])->name('seller-verification.index');
        Route::patch('/seller-verification/{user}', [SellerVerificationController::class, 'update'])->name('seller-verification.update');
        Route::get('/moderation/{type}', [ModerationController::class, 'index'])->name('moderation.index');
        Route::patch('/moderation/{type}/{id}', [ModerationController::class, 'update'])->name('moderation.update');
        Route::resource('/articles', AdminKnowledgeArticleController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::get('/knowledge-comments', [KnowledgeCommentModerationController::class, 'index'])->name('knowledge-comments.index');
        Route::patch('/knowledge-comments/{knowledgeComment}', [KnowledgeCommentModerationController::class, 'update'])->name('knowledge-comments.update');
        Route::get('/settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SiteSettingController::class, 'update'])->name('settings.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

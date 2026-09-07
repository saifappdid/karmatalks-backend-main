<?php

namespace App;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AstroController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PromocodeController;
use App\Http\Controllers\Misc\SendReportController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\WalletPackageController;
use App\Http\Controllers\Admin\BusinessSettingController;
use App\Http\Controllers\Admin\WalletTransactionController;
use App\Http\Controllers\Admin\AstroBusinessSettingController;
use App\Http\Controllers\Admin\AstroTimeReportController;
use App\Http\Controllers\Admin\InstantConsultationHistoryController;
use App\Http\Controllers\Admin\RazorpayWebhookController;
use App\Http\Controllers\Admin\AnalyticsDashboardController;
use App\Http\Controllers\WebhookController;

Route::get('/', function () {
    return redirect()->route('admin.home.index');
});

Route::controller(WebhookController::class)->prefix('webhook')->group(function () {
    Route::post('handle', 'handlePayment');
});

Route::controller(RazorpayWebhookController::class)->prefix('webhook')->group(function () {
    Route::post('fetch-booking-payment', 'fetchBookingPayment');
});
Route::get('privacy-policy', function () {
    return view('policies.privacy-policy');
})->name('privacy-policy');
Route::get('refund-policy', function () {
    return view('policies.refund-policy');
})->name('refund-policy');
Route::get('terms-conditions', function () {
    return view('policies.terms-conditions');
})->name('terms-conditions');

Route::prefix('admin')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/logout', [LogoutController::class, 'logout'])->name('logout-admin');
});

Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
    Route::name('home.')->controller(DashboardController::class)->group(function () {
        Route::get('/', 'home')->name('index');
        Route::get('invoice-download', 'downloadPdf')->name('invoice-download');
    });

    // Analytics Dashboard Routes
    Route::name('analytics.')->prefix('analytics')->controller(AnalyticsDashboardController::class)->group(function () {
        Route::get('users', 'userAnalytics')->name('users');
        Route::get('revenue', 'revenueAnalytics')->name('revenue');
        Route::get('astrologers', 'astrologerAnalytics')->name('astrologers');
        Route::get('consultations', 'consultationAnalytics')->name('consultations');
        Route::get('orders', 'orderAnalytics')->name('orders');
        Route::get('realtime', 'realtimeAnalytics')->name('realtime');
        Route::get('retention', 'retentionAnalytics')->name('retention');
        Route::get('funnel', 'funnelAnalytics')->name('funnel');
    });

    Route::controller(SendReportController::class)->name('miscellaneous.')->prefix('miscellaneous')->group(function () {
        Route::post('send-report', 'send')->name('send-report');
    });

    Route::name('business-settings.')->prefix('business-settings')->controller(BusinessSettingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
    });

    Route::name('astro-business-settings.')->prefix('astro-business-settings')->controller(AstroBusinessSettingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
    });

    Route::name('users.')->prefix('users')->controller(UserController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::get('profile/{id}', 'profile')->name('profile');
        Route::get('{id}/edit', "edit")->name('edit');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('update', 'update')->name('update');
        Route::put('status', 'status')->name('status');
    });

    Route::name('banners.')->prefix('banners')->controller(BannerController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('blocked', 'index')->name('blocked');
        Route::get('deleted', 'index')->name('deleted');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', "edit")->name('edit');
        Route::get('{id}/restore', "restoreDeletedValue")->name('restoreDeletedValue');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('update', 'update')->name('update');
        Route::put('status', 'status')->name('status');
    });
    Route::name('services.')->prefix('services')->controller(ServiceController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('blocked', 'index')->name('blocked');
        Route::get('deleted', 'index')->name('deleted');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', "edit")->name('edit');
        Route::get('{id}/restore', "restoreDeletedValue")->name('restoreDeletedValue');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('update', 'update')->name('update');
        Route::put('status', 'status')->name('status');
        Route::get('rank', 'rank')->name('rank');
        Route::post('post-rank', 'postRank')->name('post-rank');
    });

    Route::name('astros.')->prefix('astros')->controller(AstroController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('blocked', 'index')->name('blocked');
        Route::get('deleted', 'index')->name('deleted');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', "edit")->name('edit');
        Route::get('{id}/details', 'details')->name('details');
        Route::get('{id}/restore', "restoreDeletedValue")->name('restoreDeletedValue');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('update', 'update')->name('update');
        Route::put('status', 'status')->name('status');
        Route::post('delete-price', 'deletePrice')->name('delete-price');
        Route::post('get-timeline', 'getAstroTimeLine')->name('get-timeline');
        Route::post('update-timeline', 'updateTimeLine')->name('update-timeline');
        Route::post('get-price', 'getAstroPrices')->name('get-price');
        Route::post('update-pricing', 'updatePricing')->name('update-pricing');
        Route::post('update-instant-pricing', 'updateInstantPricing')->name('update-instant-pricing');
    });

    Route::name('categorys.')->prefix('categorys')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('blocked', 'index')->name('blocked');
        Route::get('deleted', 'index')->name('deleted');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', "edit")->name('edit');
        Route::get('{id}/restore', "restoreDeletedValue")->name('restoreDeletedValue');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('update', 'update')->name('update');
        Route::put('status', 'status')->name('status');
    });
    Route::name('products.')->prefix('products')->controller(ProductController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('blocked', 'index')->name('blocked');
        Route::get('deleted', 'index')->name('deleted');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', "edit")->name('edit');
        Route::get('{id}/restore', "restoreDeletedValue")->name('restoreDeletedValue');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('update', 'update')->name('update');
        Route::put('status', 'status')->name('status');
        Route::get('get-categories', 'getCategory')->name('get-categories');
        Route::delete('delete-image/{id}', 'deleteImage')->name('delete-image');
    });
    /*==============================================================>
    /==================Promocode routes======================>
    /==============================================================*/
    Route::name('promocodes.')->prefix('promocodes')->controller(PromocodeController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::delete('destroy/{id}', 'destroy')->name('destroy');
        Route::post('update', 'update')->name('update');
        Route::put('status', 'status')->name('status');
    });

    /*==============================================================>
    /==================Wallet Package routes======================>
    /==============================================================*/
    Route::name('walletpackages.')->prefix('walletpackages')->controller(WalletPackageController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('blocked', 'index')->name('blocked');
        Route::get('deleted', 'index')->name('deleted');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', "edit")->name('edit');
        Route::get('{id}/restore', "restoreDeletedValue")->name('restoreDeletedValue');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('update', 'update')->name('update');
        Route::put('status', 'status')->name('status');
        Route::put('for-new-user', 'forNewUser')->name('for-new-user');
    });

    /*==============================================================>
    /==================Order Management routes======================>
    /==============================================================*/
    Route::name('orders.')->prefix('orders')->controller(OrderController::class)->group(function () {
        Route::get('/', 'index')->name('all');
        Route::get('confirmed', 'index')->name('confirmed');
        Route::get('shipped', 'index')->name('shipped');
        Route::get('delivered', 'index')->name('delivered');
        Route::get('/detail/{id}', 'show')->name('show');
        Route::post('/updateStatus', 'updateStatus')->name('updateStatus');
    });

    /*==============================================================>
    /==================Booking Management routes======================>
    /==============================================================*/
    Route::name('bookings.')->prefix('bookings')->controller(BookingController::class)->group(function () {
        Route::get('/', 'index')->name('all');
        Route::get('confirmed', 'index')->name('confirmed');
        Route::get('started', 'index')->name('started');
        Route::get('ended', 'index')->name('ended');
        Route::get('/detail/{id}', 'show')->name('show');
        Route::post('/updateStatus', 'updateStatus')->name('updateStatus');
        Route::post('/reschedule', 'rescheduleBooking')->name('reschedule');
        Route::post('get-available-times', 'getAvailableTimes')->name('getAvailableTimes');
    });

    /*==============================================================>
    /==================Wallet Transactions routes======================>
    /==============================================================*/
    Route::name('wallettransactions.')->prefix('wallettransactions')->controller(WalletTransactionController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update', 'update')->name('update');
    });
    /*==============================================================>
    /===============Instant Consultation routes=====================>
    /==============================================================*/
    Route::name('instant-consultation.')->prefix('instant-consultation')->controller(InstantConsultationHistoryController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update', 'update')->name('update');
        Route::get('chat/{id}', 'chatHistory')->name('chat');
    });
    /*==============================================================>
    /==================Languages routes======================>
    /==============================================================*/
    Route::name('languages.')->prefix('languages')->controller(LanguageController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::delete('destroy/{id}', 'destroy')->name('destroy');
        Route::post('update', 'update')->name('update');
        Route::put('status', 'status')->name('status');
    });

    Route::name('skills.')->prefix('skills')->controller(SkillController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('blocked', 'index')->name('blocked');
        Route::get('deleted', 'index')->name('deleted');
        Route::post('store', 'store')->name('store');
        Route::get('{id}/edit', "edit")->name('edit');
        Route::get('{id}/restore', "restoreDeletedValue")->name('restoreDeletedValue');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::post('update', 'update')->name('update');
        Route::put('status', 'status')->name('status');
    });

    Route::name('notifications.')
        ->prefix('notifications')
        ->controller(NotificationController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('store', 'store')->name('store');
            Route::get('{id}/edit', "edit")->name('edit');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('update', 'update')->name('update');
            Route::put('status', 'status')->name('status');
        });

    Route::name('astro-time-report.')
        ->prefix('astro-time-report')
        ->controller(AstroTimeReportController::class)->group(function () {
            Route::get('/', 'index')->name('index');
        });
});

Route::get('/services/{code}', function ($code) {

    $agent = strtolower(request()->header('User-Agent'));
    $ip = request()->ip();
    $referralCode = $code;

    $device = 'desktop';

    if (str_contains($agent, 'android')) {
        $device = 'android';
    } elseif (str_contains($agent, 'iphone') || str_contains($agent, 'ipad')) {
        $device = 'iphone';
    }

    return view('content.tables.apps', [
        'referralCode' => $referralCode,
        'device' => $device
    ]);
});

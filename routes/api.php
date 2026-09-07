<?php

use App\Http\Controllers\API\v1\AstroController;
use App\Http\Controllers\API\v1\AstroWorkController;
use App\Http\Controllers\API\v1\Auth\AuthController;
use App\Http\Controllers\API\v1\BasicController;
use App\Http\Controllers\API\v1\BookingController;
use App\Http\Controllers\API\v1\ChatController;
use App\Http\Controllers\API\v1\EventController;
use App\Http\Controllers\API\v1\FollowController;
use App\Http\Controllers\API\v1\InvoiceController;
use App\Http\Controllers\API\v1\InstantConsultationController;
use App\Http\Controllers\API\v1\OrderController;
use App\Http\Controllers\API\v1\ProductController;
use App\Http\Controllers\API\v1\PromocodeController;
use App\Http\Controllers\API\v1\ReviewController;
use App\Http\Controllers\API\v1\UserController;
use App\Http\Controllers\API\v1\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::post('generate-otp', 'generateOtp');
        Route::post('login', 'login');
        Route::post('astro-login', 'astroLogin');
        Route::post('google-login', 'googleLogin');
    });

    Route::controller(BasicController::class)->prefix('basic')->group(function () {
        Route::get('business-setting', 'businessSettings');
        Route::post('set-app-versions', 'setAppVersions');
        Route::get('astro-business-setting', 'astroBusinessSettings');
        Route::get('banners', 'getBanners');
        Route::get('services', 'getServices');
        Route::get('categories', 'getCategories');
        Route::get('products', 'getProducts');
        Route::get('countries', 'getCountries');
        Route::get('states', 'getStates');
        Route::get('cities', 'getCities');
        Route::get('get-astros', 'getAstros');
        Route::get('walletpackages', 'walletPackages');
        Route::get('languages', 'getLanguages');
        Route::get('skills', 'getSkills');
        Route::post('refresh-device-id', 'refreshDeviceId');

    });
    Route::controller(ProductController::class)->prefix('product')->group(function () {
        Route::get('categories', 'getCategories');
        Route::get('get', 'getProducts');
        Route::post('detail', 'getProductDetail');
    });
    Route::controller(AstroController::class)->prefix('astro')->group(function () {
        // Instant Online Astro List
        Route::post('get', 'getAstro');
        Route::get('detail', 'getAstroDetail');
        // Service astro list
        Route::post('by-service', 'getServiceAstro');
        Route::post('block-user', 'blockUser');

    });
    Route::controller(EventController::class)->prefix('event')->group(function () {
        Route::get('agora-token', 'getAgoraToken');
    });

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::controller(UserController::class)->prefix('user')->group(function () {
            Route::post('update-profile', 'updateProfile');
            Route::get('profile', 'getProfile');
            Route::post('store-address', 'storeAddress')->name('store-address');
            Route::get('address', 'getAddress')->name('address');
            Route::post('update-address', 'updateAddress')->name('update-address');
            Route::post('delete-address', 'deleteAddress')->name('delete-address');
            Route::get('suggested-astro', 'getSuggestedAstro');
            Route::post('delete-suggested-astro', 'deleteSuggestedAstro');
        });
        Route::controller(ReviewController::class)->prefix('review')->group(function () {
            Route::post('add', 'addReview');
            Route::get('get', 'getReviews');
        });
        Route::controller(EventController::class)->prefix('event')->group(function () {
            Route::post('user-initiate-call', 'initiateCall');
            Route::post('user-update-call-log', 'updateCallLogForUser');
            Route::post('user-media-share', 'mediaShare');
        });

        Route::controller(BookingController::class)->prefix('booking')->group(function () {
            Route::post('create-booking', 'createBooking')->name('create-booking'); //{{baseURL}}api/v1/booking/create-booking
            Route::post('fetch-booking', 'fetchBooking')->name('fetch-booking');    //{{baseURL}}api/v1/booking/fetch-booking
            Route::get('get', 'getMyBooking');
            Route::get('detail', 'getBookingDetail');
        });
        Route::controller(InvoiceController::class)->prefix('invoice')->group(function () {
            Route::get('booking', 'bookingInvoice'); //{{baseURL}}api/v1/invoice/booking?id=
            Route::get('order', 'orderInvoice'); //{{baseURL}}api/v1/invoice/order?id=
            Route::get('wallet', 'walletTransactionInvoice'); //{{baseURL}}api/v1/invoice/wallet?id=
            //test
        });
        Route::controller(AstroWorkController::class)->prefix('astro-work')->group(function () {
            Route::post('work-times', 'getWorkTimes');
            Route::post('international-work-times', 'getInternationalWorkTimes');
        });
        Route::controller(PromocodeController::class)->prefix('promocode')->group(function () {
            Route::get('get', 'getPromocodes');
            Route::post('validate', 'validatePromocode');
        });
        Route::controller(OrderController::class)->prefix('order')->group(function () {
            Route::post('create', 'createOrder');
            Route::post('fetch', 'fetchOrder');
            Route::get('my-orders', 'myOrders');
            Route::get('detail', 'orderDetails');
        });

        Route::controller(WalletController::class)->prefix('wallet')->group(function () {
            Route::post('addWallet', 'addWallet');
            Route::post('fetchWallet', 'fetchWallet');
            Route::get('transactions', 'walletTransactions');
        });

        Route::controller(FollowController::class)->prefix('follow')->group(function () {
            Route::post('toggle', 'toggleFollow');
            Route::get('followers', 'getFollowers');
        });

        Route::controller(ChatController::class)->prefix('user/chat')->group(function () {
            Route::post('initiate', 'initiateChat');
            Route::post('decline', 'declineChat');
            Route::post('start', 'startChat');
            Route::post('update', 'updateChat');
            Route::post('end', 'endChat');
            Route::post('user-send-message', 'sendMessage');
        });

        Route::controller(InstantConsultationController::class)->prefix('instant-consultation')->group(function () {
            Route::post('initiate-call-request', 'initiateCallRequest');
            Route::post('update-log', 'updateCallLog');
            Route::post('start-session', 'startSession');
            Route::post('update-session', 'updateSession');
            Route::post('end-session', 'endSession');
            Route::get('history', 'getHistory');
            Route::get('detail', 'getDetail');
        });
    });

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::controller(AuthController::class)->prefix('astro')->group(function () {
            Route::get('logout', 'astroLogout');
        });
        Route::controller(AstroController::class)->prefix('astro')->group(function () {
            Route::get('profile', 'getProfile');
            Route::post('update-profile', 'updateProfile');
            Route::post('update-is-online-status', 'updateIsOnlineStatus');
            Route::post('update-consultation-time-line', 'updateConsultationTimeLine');
            Route::post('update-instant-consultation-status', 'updateInstantConsultationStatus');
            Route::get('get-instant-consultation-history', 'getInstantConsultationHistory');
            Route::get('get-instant-consultation-detail', 'getInstantConsultationDetail');
            Route::get('get-appointments', 'getAppointments');
            Route::get('get-todays-appointments', 'getTodaysAppointments');
            Route::get('get-upcoming-appointments', 'getUpcomingAppointments');
            Route::get('get-appointment-details', 'appointmentDetails');
            Route::get('get-followers', 'getFollowers');
            Route::get('update-last-active', 'updateLastActive');
            Route::controller(InstantConsultationController::class)->prefix('instant-consultation')->group(function () {
                Route::post('update-log', 'updateCallLog');
            });
        });
        Route::controller(EventController::class)->prefix('event')->group(function () {
            Route::post('astro-initiate-call', 'initiateCall');
            Route::post('astro-update-call-log', 'updateCallLogForAstro');
            Route::post('astro-media-share', 'mediaShare');
        });
        Route::controller(ChatController::class)->prefix('chat')->group(function () {
            Route::post('start', 'startChat');
            Route::post('update', 'updateChat');
            Route::post('decline', 'declineChat');
            Route::post('end', 'endChat');
            Route::post('send-message', 'sendMessage');
        });
    });
});

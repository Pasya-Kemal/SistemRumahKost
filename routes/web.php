<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KostController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AdminRoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OwnerFinancialController;
use App\Http\Controllers\OwnerAdminController;
use App\Http\Controllers\TenantRoomController;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Public / Auth routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware('guest')->group(function () {
    Route::get('/login',     [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [WebAuthController::class, 'login']);
    Route::get('/register',  [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Protected (session-authenticated) routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // DOMAIN OWNER
    // ==========================================
    // Kost: Owner manages CRUD untuk Kost.
    Route::resource('kosts', KostController::class);

    // Room: Owner only read.
    Route::resource('rooms', RoomController::class)->only(['index', 'show']);

    // Financial record (read-only).
    Route::get('financial-reports', [OwnerFinancialController::class, 'index'])
        ->name('owner.financials.index');

    // Tim Admin: Owner manages Admin accounts scoped to their kosts.
    Route::get('tim-admin',             [OwnerAdminController::class, 'index'])->name('owner.admins.index');
    Route::post('tim-admin',            [OwnerAdminController::class, 'store'])->name('owner.admins.store');
    Route::delete('tim-admin/{admin}',  [OwnerAdminController::class, 'destroy'])->name('owner.admins.destroy');

    // ==========================================
    // DOMAIN ADMIN
    // ==========================================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('rooms', AdminRoomController::class);
    });

    // ==========================================
    // DOMAIN TENANT
    // ==========================================
    // Browse + search + paginate available rooms.
    Route::get('tenant/rooms', [TenantRoomController::class, 'index'])
        ->name('tenant.rooms.index');

    // Booking: Tenant booking flow.
    Route::resource('bookings', BookingController::class)->except(['edit', 'update']);

    // Payment: Tenant payment flow.
    Route::resource('payments', PaymentController::class)->except(['edit', 'update', 'destroy']);
});

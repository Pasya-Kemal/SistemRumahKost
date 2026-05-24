<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KostController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AdminRoomController; 
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OwnerFinancialController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
| 
*/

Route::middleware(['auth'])->group(function () {

    // ==========================================
    // DOMAIN OWNER
    // ==========================================
    Route::resource('kosts', KostController::class);
    Route::resource('rooms', RoomController::class)->only(['index', 'show']);
    
    // financial record: read
    Route::get('financial-reports', [OwnerFinancialController::class, 'index'])->name('owner.financials.index');
    
    // Kost: Owner Manage (CRUD) data Kost.
    Route::resource('kosts', KostController::class);
    
    // Room: owner only read
    Route::resource('rooms', RoomController::class)->only(['index', 'show']);


    // ==========================================
    // DOMAIN ADMIN
    // ==========================================
    
    // Room: Admin mange CRUD penuh all room.
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('rooms', AdminRoomController::class);
    });


    // ==========================================
    //  DOMAIN TENANT (Financial & Transaction)
    // ==========================================
    
    // Booking: Tenant Booking.
    Route::resource('bookings', BookingController::class)->except([
        'edit', 'update'
    ]);

    // Payment: Tenant payment.
    Route::resource('payments', PaymentController::class)->except([
        'edit', 'update', 'destroy'
    ]);

});


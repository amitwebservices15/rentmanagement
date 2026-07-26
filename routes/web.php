<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home page
Route::get('/', function () {
    return view('welcome');
});

// Auth Routes (handled by routes/auth.php)
require __DIR__.'/auth.php';

// Dashboard redirect based on role
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->role === 'super_admin') {
            return redirect('/admin/dashboard');
        } elseif ($user->role === 'owner') {
            return redirect('/owner/dashboard');
        }
        
        return redirect('/');
    })->name('dashboard');
});


/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:super_admin'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    });

    Route::resource('admin/plans',   App\Http\Controllers\Admin\SubscriptionPlanController::class, ['as' => 'admin']);
    Route::resource('admin/credits', App\Http\Controllers\Admin\CreditPackController::class, ['as' => 'admin']);

});


/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:owner'])->group(function () {

    Route::get('/owner/dashboard', function () {
        return view('owner.dashboard');
    });

    // Subscription and Credit Management
    Route::get('subscriptions', [App\Http\Controllers\Owner\SubscriptionController::class, 'index'])->name('owner.subscriptions.index');
    Route::get('subscriptions/{plan}/purchase', [App\Http\Controllers\Owner\SubscriptionController::class, 'purchase'])->name('owner.subscriptions.purchase');
    Route::post('subscriptions/{plan}/purchase', [App\Http\Controllers\Owner\SubscriptionController::class, 'processPurchase'])->name('owner.subscriptions.process');
    Route::get('my-subscriptions', [App\Http\Controllers\Owner\SubscriptionController::class, 'mySubscriptions'])->name('owner.subscriptions.my');

    Route::get('credits', [App\Http\Controllers\Owner\CreditController::class, 'index'])->name('owner.credits.index');
    Route::get('credits/{pack}/purchase', [App\Http\Controllers\Owner\CreditController::class, 'purchase'])->name('owner.credits.purchase');
    Route::post('credits/{pack}/purchase', [App\Http\Controllers\Owner\CreditController::class, 'processPurchase'])->name('owner.credits.process');
    Route::get('my-purchases', [App\Http\Controllers\Owner\CreditController::class, 'myPurchases'])->name('owner.credits.my');

    // WhatsApp Messaging
    Route::post('whatsapp/rent-slip/{rentRecord}', [App\Http\Controllers\Owner\WhatsAppController::class, 'sendRentSlip'])->name('owner.whatsapp.rent-slip');
    Route::post('whatsapp/rent-reminder/{rentRecord}/{tenant}', [App\Http\Controllers\Owner\WhatsAppController::class, 'sendRentReminder'])->name('owner.whatsapp.rent-reminder');
    Route::get('whatsapp/compose/{tenant}', [App\Http\Controllers\Owner\WhatsAppController::class, 'composeMessage'])->name('owner.whatsapp.compose');
    Route::post('whatsapp/send/{tenant}', [App\Http\Controllers\Owner\WhatsAppController::class, 'sendCustomMessage'])->name('owner.whatsapp.send-custom');
    Route::get('whatsapp/history', [App\Http\Controllers\Owner\WhatsAppController::class, 'messageHistory'])->name('owner.whatsapp.history');

    Route::resource('properties', App\Http\Controllers\PropertyController::class);

    Route::resource('properties.rooms', App\Http\Controllers\RoomController::class)
         ->except(['show']);

    // Tenant management from within room edit page
    Route::post('properties/{property}/rooms/{room}/tenants',           [App\Http\Controllers\RoomController::class, 'tenantStore'])->name('room.tenants.store');
    Route::put('properties/{property}/rooms/{room}/tenants/{tenant}',   [App\Http\Controllers\RoomController::class, 'tenantUpdate'])->name('room.tenants.update');
    Route::patch('properties/{property}/rooms/{room}/tenants/{assignment}/vacate', [App\Http\Controllers\RoomController::class, 'tenantVacate'])->name('room.tenants.vacate');

    Route::resource('tenants', App\Http\Controllers\TenantController::class)
         ->except(['show']);

    Route::get('tenants/{tenant}/assign',   [App\Http\Controllers\AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('tenants/{tenant}/assign',  [App\Http\Controllers\AssignmentController::class, 'store'])->name('assignments.store');
    Route::patch('assignments/{assignment}/vacate', [App\Http\Controllers\AssignmentController::class, 'vacate'])->name('assignments.vacate');

    // Rent generation
    Route::get('properties/{property}/rooms/{room}/rent/create',  [App\Http\Controllers\RentController::class, 'create'])->name('rent.create');
    Route::post('properties/{property}/rooms/{room}/rent',        [App\Http\Controllers\RentController::class, 'store'])->name('rent.store');
    Route::get('properties/{property}/rooms/{room}/rent',         [App\Http\Controllers\RentController::class, 'index'])->name('rent.index');
    Route::patch('rent/{record}/pay',                             [App\Http\Controllers\RentController::class, 'markPaid'])->name('rent.pay');
    Route::delete('rent/{record}',                                [App\Http\Controllers\RentController::class, 'destroy'])->name('rent.destroy');

});
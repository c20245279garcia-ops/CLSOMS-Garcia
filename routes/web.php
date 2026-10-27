<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LaundryOrderController;

Route::get('/create-order', [LaundryOrderController::class, 'create']);

// Route to handle the form submission
Route::post('/orders', [LaundryOrderController::class, 'store'])->name('orders.store');
Route::get('/orders-list', [LaundryOrderController::class, 'index'])->name('orders.index');

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

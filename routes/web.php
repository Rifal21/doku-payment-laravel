<?php

use App\Http\Controllers\AdminTransactionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductFrontController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', [AdminTransactionController::class , 'index'])->middleware(['auth', 'verified'])->name('dashboard.index');
Route::get('/dashboard/transactions' , [AdminTransactionController::class , 'transaction'] )->middleware(['auth', 'verified'])->name('dashboard.transactions');
Route::get('/dashboard/transactions/{invoice}' , [AdminTransactionController::class , 'show'] )->middleware(['auth', 'verified'])->name('dashboard.transactions.show');
Route::delete('/dashboard/transactions/{id}' , [AdminTransactionController::class , 'destroy'] )->middleware(['auth', 'verified'])->name('dashboard.transactions.delete');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [ProductFrontController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductFrontController::class, 'show'])->name('products.show');
// Route::post('/cart/checkout', [PaymentController::class, 'handleCheckout'])->name('cart.checkout');
Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
// Route::get('/payment/success', [PaymentController::class, 'handleSuccess'])->name('payment.success');
Route::post('/payment/headers' , [PaymentController::class, 'getPaymentHeaders'])->name('payment.headers');



// Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction.index');
// Route::post('/transaction/pay', [TransactionController::class, 'pay'])->name('transaction.pay');


Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    // Route::post('/cart/checkout', [CartController::class, 'storeTransaction'])->name('cart.checkout');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
    
});
Route::resource('/dashboard/coupon' , CouponController::class)->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';

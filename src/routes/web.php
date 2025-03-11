<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\UserController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('header')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/', [HomeController::class, 'search'])->name('home.search');
    Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/mypage', [HomeController::class, 'myPageIndex'])->name('mypage');
        Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/item/{item_id}/like', [LikeController::class, 'toggleLike'])->name('like');
        Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');
        Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('address.edit');
        Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('address.update');

        // チャット終了後の処理
        Route::post('/purchase/complete/{purchase_id}', [PurchaseController::class, 'complete'])->name('purchase.complete');
        Route::get('/purchase/{item_id}', [PurchaseController::class, 'index'])->name('purchase');
        Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');
        Route::get('/sell', [ItemController::class, 'create'])->name('sell.create');
        Route::post('/sell', [ItemController::class, 'store'])->name('sell.store');

        // ---------------------------------------------------
        Route::get('chat/{purchase_id}', [ChatController::class, 'index'])->name('chat');
        
        // API用ルーティング
        Route::post('chat/{purchase_id}/read', [ChatController::class, 'read'])->name('chat.read');
        Route::post('chat/{chat_id}/update', [ChatController::class, 'update'])->name('chat.update');
        Route::post('chat/{chat_id}/delete', [ChatController::class, 'delete'])->name('chat.delete');
        Route::post('chat/{purchase_id}/{receiver_id}', [ChatController::class, 'sendMessage'])->name('chat.send');
        
        Route::post('rating/{seller_id}', [UserController::class, 'rating'])->name('user.rating');
        // ---------------------------------------------------

        // stripeの成功・キャンセル用ルーティング
        Route::get('/payment/success/{purchase_id}', [PurchaseController::class, 'success'])->name('payment.success');
        Route::get('/payment/cancel/{purchase_id}', [PurchaseController::class, 'cancel'])->name('payment.cancel');
    });
});
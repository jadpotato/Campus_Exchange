<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MyItemsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

// 兼容直接访问 /logout，自动跳登录页
Route::get('/logout', function () {
    return redirect()->route('login');
});

// 主页：未登录 → 跳登录；已登录 → 跳物品列表（未来的主页）
Route::get('/', function () {
    if (Auth::check()) {
        // 已登录 → 以后跳物品列表，现在先跳dashboard
        return redirect()->route('dashboard');
    } else {
        // 未登录 → 跳登录页
        return redirect()->route('login');
    }
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/free-time', [ProfileController::class, 'updateFreeTime'])->name('profile.free-time.update');
});

// 物品管理路由
Route::resource('items', ItemController::class)->middleware(['auth', 'verified']);
Route::get('/my/items', [MyItemsController::class, 'index'])->name('my.items')->middleware('auth');

// 用户个人主页
Route::get('/users/{user}', [UserController::class, 'show'])
    ->name('users.show')
    ->middleware(['auth', 'verified']);

// 交易模块（全功能）
Route::middleware('auth')->group(function () {
    // 1. 交易创建
    Route::get('/trades/create', [TradeController::class, 'create'])->name('trades.create');
    Route::post('/trades', [TradeController::class, 'store'])->name('trades.store');
    
    // 2. 我的交易看板
    Route::get('/my/trades', [TradeController::class, 'myTrades'])->name('trades.my');
    
    // 3. 交易详情
    Route::get('/trades/{trade}', [TradeController::class, 'show'])->name('trades.show');
    
    // 4. 预约确认页
    Route::get('/trades/{trade}/appointment', [TradeController::class, 'appointment'])->name('trades.appointment');
    Route::post('/trades/{trade}/appointment', [TradeController::class, 'saveAppointment']);
    
    // API接口
    Route::get('/api/trades', [TradeController::class, 'apiIndex']);
    Route::put('/api/trades/{trade}/status', [TradeController::class, 'updateStatus']);
});

// 消息模块
Route::middleware('auth')->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{trade}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/api/messages/unread', [MessageController::class, 'unread']);
});

// 评价模块
Route::middleware('auth')->group(function () {
    Route::get('/trades/{trade}/review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/trades/{trade}/review', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/users/{user}/reviews', [ReviewController::class, 'index'])->name('reviews.index');
});

// 管理后台（✅ 单独认证，不与普通用户共用）
Route::prefix('admin')->name('admin.')->group(function () {
    // 登录路由
    Route::get('/login', [\App\Http\Controllers\Admin\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Admin\LoginController::class, 'login']);
    Route::post('/logout', [\App\Http\Controllers\Admin\LoginController::class, 'logout'])->name('logout');

    // 需要登录的路由
    Route::middleware('auth:admin')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'index'])->name('index');
        Route::get('/items', [\App\Http\Controllers\Admin\AdminController::class, 'items'])->name('items');
        Route::get('/users', [\App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users');
        Route::get('/trades', [\App\Http\Controllers\Admin\AdminController::class, 'trades'])->name('trades');
        Route::get('/reports', [\App\Http\Controllers\Admin\AdminController::class, 'reports'])->name('reports');

        // API接口
        Route::get('/api/stats', [\App\Http\Controllers\Admin\AdminController::class, 'apiStats']);
        Route::get('/api/items', [\App\Http\Controllers\Admin\AdminController::class, 'apiItems']);
        Route::get('/api/users', [\App\Http\Controllers\Admin\AdminController::class, 'apiUsers']);
        Route::get('/api/trades', [\App\Http\Controllers\Admin\AdminController::class, 'apiTrades']);
        
        // 操作接口
        Route::put('/api/items/{item}/status', [\App\Http\Controllers\Admin\AdminController::class, 'updateItemStatus']);
        Route::put('/api/users/{user}/status', [\App\Http\Controllers\Admin\AdminController::class, 'updateUserStatus']);
    });
});

// API路由（用于DHTMLX Grid）
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/items', [ItemController::class, 'apiIndex']);
    // 新增这一行
    Route::get('/my/items', [ItemController::class, 'myItems']);
});

// 测试DHTMLX Grid
Route::get('/test_dhtmlx', function () {
    return view('test_dhtmlx');
});

require __DIR__.'/auth.php';

<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Panel\UserController;
use App\Http\Controllers\User\IndexController;
use App\Http\Controllers\User\AntrianController;
use App\Http\Controllers\Panel\DocumentController;
use App\Http\Controllers\Panel\TakeEktpController;


use App\Http\Controllers\Panel\BlangkoStockController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Panel\AdminDashboardController;
use App\Http\Controllers\User\DocumentRequestController;
use App\Http\Controllers\Panel\AntrianController as PanelAntrianController;


// SMPT LUPA PASSWORD
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Verifikasi Email
Route::get('/email/verify', function () {
    if (auth()->user() && auth()->user()->hasVerifiedEmail()) {
        return redirect(auth()->user()->isAdmin() ? route('admin.index') : route('user.index'));
    }
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', App\Http\Controllers\Auth\VerificationController::class)
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Link verifikasi telah dikirim!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// index user awal
Route::get('/', function () {
    // return redirect()->route('login');
    return view('home.index');
});

Route::get('/refresh-config', function () {
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('optimize:clear');
    return 'Config cache cleared!';
});

// Route untuk form register user
Route::get('/register', [AuthController::class, 'showRegisterFormUser'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Route untuk form register admin
Route::get('/panel-admin/register', [AuthController::class, 'showRegisterFormAdmin'])->name('admin.register');
Route::post('/panel-admin/register', [AuthController::class, 'register'])->name('admin.register.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::prefix('panel-admin/dashboard')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    // Dashboard Admin
    Route::get('/', [AdminDashboardController::class, 'index'])->name('index');

    Route::get('/chart-data', [AdminDashboardController::class, 'chartData'])->name('chartData');

    // Antrian
    Route::get('/documents/antrian', [PanelAntrianController::class, 'index'])->name('antrian.index');
    Route::post('/documents/antrian', [PanelAntrianController::class, 'store'])->name('antrian.store');

    // Permintaan Dokumen
    Route::get('/documents', [AdminDashboardController::class, 'document'])->name('document.index');
    Route::get('/documents/print-document', [DocumentController::class, 'print'])->name('document.printDocument');
    Route::get('/documents/create/{userId}', [DocumentController::class, 'create'])->name('document.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('document.store');
    Route::get('/documents/{id}', [DocumentController::class, 'detailShow'])->name('document.detailShow');
    Route::get('/documents/{id}/pop', [DocumentController::class, 'detailPop'])->name('document.detailPop');
    Route::post('/documents/{id}/update-status', [DocumentController::class, 'updateStatus'])->name('document.updateStatus');
    
    Route::get('/documents/{id}/edit', [DocumentController::class, 'edit'])->name('document.edit');
    Route::put('/documents/{id}', [DocumentController::class, 'update'])->name('document.update');

    Route::get('/take-ektp', [AdminDashboardController::class, 'takeEktp'])->name('document.takeEktp');
    Route::get('/take-ektp/{id}/input', [TakeEktpController::class, 'show'])->name('document.takeEktpShow');
    Route::post('/take-ektp/{id}', [TakeEktpController::class, 'store'])->name('document.takeEktp.store');

    // Stock Blangko
    Route::get('/blangko-stock', [AdminDashboardController::class, 'blangko'])->name('blangko.index');
    Route::get('/blangko-stock/create', [BlangkoStockController::class, 'create'])->name('blangko.create');
    Route::post('/blangko-stock', [BlangkoStockController::class, 'store'])->name('blangko.store');
    Route::delete('/blangko-stock/destroy/{id}', [BlangkoStockController::class, 'destroy'])->name('blangko.destroy');
    Route::get('/blangko-stock/{id}', [BlangkoStockController::class, 'detail'])->name('blangko.detail');
    Route::get('/panel-admin/dashboard/blangko-stock/print-blangko', [BlangkoStockController::class, 'printAll'])->name('blangko.print');


    // Manajemen User
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.delete');
    Route::patch('/admin/users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

    // Riwayat Pendaftaran
    Route::get('/regist-history', [AdminDashboardController::class, 'registhistory'])->name('history.index');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('user/dashboard')->middleware(['auth', 'verified', 'role:user'])->name('user.')->group(function () {
    Route::get('/', [IndexController::class, 'index'])->name('index');

    Route::get('/antrian', [AntrianController::class, 'index'])->name('antrian.index');
    Route::post('/antrian/take', [AntrianController::class, 'takeAntrian'])->name('antrian.take');

    // Document Request
    Route::get('/document/create', [IndexController::class, 'documentRequest'])->name('document.create');
    Route::post('/document/store', [DocumentRequestController::class, 'store'])->name('document.store');
    Route::get('/document/status', [DocumentRequestController::class, 'status'])->name('document.status');

    Route::get('/document/{id}/edit', [DocumentRequestController::class, 'edit'])->name('document.edit');
    Route::put('/document/{id}', [DocumentRequestController::class, 'update'])->name('document.update');

    // Profile
    Route::get('/profile', [AuthController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [AuthController::class, 'update'])->name('profile.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

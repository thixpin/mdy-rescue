<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware('guest:admin')->group(function () {
    Route::get('login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
    Route::get('register', [App\Http\Controllers\Admin\AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [App\Http\Controllers\Admin\AuthController::class, 'register']);
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
        Route::get('register', [App\Http\Controllers\Admin\AuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [App\Http\Controllers\Admin\AuthController::class, 'register']);
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Donation Management Routes
        // Route::get('donations/{donation}/edit', [App\Http\Controllers\Admin\DonationController::class, 'edit'])->name('donations.edit');
        Route::resource('donations', App\Http\Controllers\Admin\DonationController::class);
        Route::post('donations/{donation}/toggle-verification', [App\Http\Controllers\Admin\DonationController::class, 'toggleVerification'])
            ->name('donations.toggle-verification');
        Route::post('donations/import', [App\Http\Controllers\Admin\DonationController::class, 'import'])
            ->name('donations.import');
        Route::post('donations/{donation}/verify-certificate', [App\Http\Controllers\Admin\DonationController::class, 'verifyCertificate'])
            ->name('donations.verify-certificate');
    });
});

// Public Certificate Verification Routes
Route::get('/', [App\Http\Controllers\CertificateController::class, 'showVerificationPage'])->name('certificates.verify');
Route::get('/verify-certificate', [App\Http\Controllers\CertificateController::class, 'showVerificationPage'])->name('certificates.verify');
Route::post('/verify-certificate/check', [App\Http\Controllers\CertificateController::class, 'verify'])->name('certificates.verify.check');

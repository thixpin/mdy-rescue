<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('guest:admin')->group(function () {
    Route::get('login', [App\Http\Controllers\Admin\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [App\Http\Controllers\Admin\AuthController::class, 'login']);
    Route::get('register', [App\Http\Controllers\Admin\AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [App\Http\Controllers\Admin\AuthController::class, 'register']);
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware(['auth:admin', 'admin.active'])->group(function () {
        Route::post('logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout')->withoutMiddleware('admin.active');
        Route::get('logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout')->withoutMiddleware('admin.active');

        Route::get('/', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

        // Donation Management Routes
        Route::resource('donations', App\Http\Controllers\Admin\DonationController::class);
        Route::post('donations/{donation}/toggle-verification', [App\Http\Controllers\Admin\DonationController::class, 'toggleVerification'])
            ->name('donations.toggle-verification');
        Route::post('donations/import', [App\Http\Controllers\Admin\DonationController::class, 'import'])
            ->name('donations.import');
        Route::post('donations/{donation}/verify-certificate', [App\Http\Controllers\Admin\DonationController::class, 'verifyCertificate'])
            ->name('donations.verify-certificate');

        // Admin Management Routes
        Route::resource('admins', App\Http\Controllers\AdminController::class);
        Route::post('admins/{admin}/activate', [App\Http\Controllers\AdminController::class, 'activate'])->name('admins.activate');
        Route::post('admins/{admin}/deactivate', [App\Http\Controllers\AdminController::class, 'deactivate'])->name('admins.deactivate');
    });
});

// Public Certificate Verification Routes
Route::get('/', [App\Http\Controllers\CertificateController::class, 'showVerificationPage'])->name('certificates.verify');
Route::get('/verify-certificate', [App\Http\Controllers\CertificateController::class, 'showVerificationPage'])->name('certificates.verify');
Route::post('/verify-certificate/check', [App\Http\Controllers\CertificateController::class, 'verify'])->name('certificates.verify.check');

Route::get('/test-certificate', function () {
    $donation = new stdClass;
    $donation->formated_date = App\Helpers\Formatter::convertToMmNumber(now()->format('d  m  Y'));
    $donation->name = 'ဦးမင်းမော်ကွန်း';
    $donation->amount = App\Helpers\Formatter::myanmarCurrency('100000.00');
    $donation->amount_in_text = 'တစ်သိန်းကျပ်';
    $donation->description = 'လူမှုရေးလုပ်ငန်းများအတွက် လှူဒါန်းခြင်း';
    $donation->short_id = 'TEST123';

    return view('certificates.donation', compact('donation'));
});

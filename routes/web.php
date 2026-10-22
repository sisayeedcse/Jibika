<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AssetRequestController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\FundingController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleDashboardController;

Route::get('/', function () { return view('pages.landing'); })->name('home');
Route::get('/how-it-works', function () { return view('pages.how-it-works'); })->name('how-it-works');

Route::get('login', [LoginController::class, 'show'])->name('login');
Route::post('login', [LoginController::class, 'authenticate'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'show'])->name('register');
Route::post('register', [RegisterController::class, 'store'])->name('register.post');

Route::middleware(['auth','active'])->group(function () {
    Route::get('dashboard', RoleDashboardController::class)->name('dashboard');

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('requests', [AssetRequestController::class, 'index'])->name('requests.index');
    Route::get('requests/create', [AssetRequestController::class, 'create'])->name('requests.create');
    Route::post('requests', [AssetRequestController::class, 'store'])->name('requests.store');
    Route::get('requests/{request}', [AssetRequestController::class, 'show'])->name('requests.show');
    Route::get('requests/{request}/edit', [AssetRequestController::class, 'edit'])->name('requests.edit');
    Route::put('requests/{request}', [AssetRequestController::class, 'update'])->name('requests.update');
    Route::post('requests/{request}/submit', [AssetRequestController::class, 'submit'])->name('requests.submit');
    Route::post('requests/{request}/assess', [AssetRequestController::class, 'assess'])->name('requests.assess');

    Route::get('assessment/{request}/{assessment}', [AssessmentController::class, 'show'])->name('assessment.show');
    Route::post('requests/{request}/assess-trigger', [AssessmentController::class, 'trigger'])->name('assessment.trigger');

    Route::get('verification', [VerificationController::class, 'index'])->name('verification.index');
    Route::get('verification/{verification}/review', [VerificationController::class, 'review'])->name('verification.review');
    Route::post('verification/{verification}/decide', [VerificationController::class, 'decide'])->name('verification.decide');

    Route::get('funding', [FundingController::class, 'index'])->name('funding.index');
    Route::post('requests/{request}/open-funding', [FundingController::class, 'openPool'])->name('funding.open');
    Route::post('funding/{pool}/contribute', [FundingController::class, 'contribute'])->name('funding.contribute');

    Route::get('vendor', [VendorController::class, 'index'])->name('vendor.index');
    Route::post('vendor/quote/{opportunity}', [VendorController::class, 'quote'])->name('vendor.quote');

    Route::get('procurement', [ProcurementController::class, 'index'])->name('procurement.index');
    Route::post('procurement/{order}/confirm-delivery', [ProcurementController::class, 'confirmDelivery'])->name('procurement.confirm');
    Route::post('procurement/{order}/resolve-dispute', [ProcurementController::class, 'resolveDispute'])->name('procurement.resolve-dispute');

    Route::get('requests/{request}/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::post('requests/{request}/monitoring', [MonitoringController::class, 'store'])->name('monitoring.store');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('notifications/count', [NotificationController::class, 'count'])->name('notifications.count');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('users', [AdminController::class, 'users'])->name('users');
        Route::post('users/{user}/status', [AdminController::class, 'setStatus'])->name('users.status');
        Route::get('requests', [AdminController::class, 'requests'])->name('requests');
        Route::get('audit', [AdminController::class, 'audit'])->name('audit');
        Route::post('quotations/{quotation}/select', [AdminController::class, 'selectQuotation'])->name('quotations.select');
        Route::post('quotations/{quotation}/reject', [AdminController::class, 'rejectQuotation'])->name('quotations.reject');
    });
});

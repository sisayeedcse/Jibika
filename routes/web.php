<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetRequestController;

Route::get('/', function () {
    return view('pages.landing');
})->name('home');

Route::get('/how-it-works', function () {
    return view('pages.how-it-works');
})->name('how-it-works');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::resource('requests', AssetRequestController::class)->only(['index','show','create','store']);
    Route::get('requests/{request}/assess', [AssetRequestController::class, 'assess'])->name('requests.assess');
    Route::view('dashboard', 'pages.dashboard')->name('dashboard');
    Route::view('profile', 'pages.profile')->name('profile');
});

Route::view('login', 'auth.login');
Route::view('register', 'auth.register');

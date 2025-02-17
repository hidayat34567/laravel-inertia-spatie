<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController; 
 // Controller untuk menangani sesi otentikasi
use App\Http\Controllers\Auth\ConfirmablePasswordController;  
// Controller untuk konfirmasi kata sandi
use App\Http\Controllers\Auth\EmailVerificationNotificationController; 
 // Controller untuk mengirim notifikasi verifikasi email
use App\Http\Controllers\Auth\EmailVerificationPromptController;  
// Controller untuk menampilkan prompt verifikasi email
use App\Http\Controllers\Auth\NewPasswordController; 
 // Controller untuk menangani pembuatan kata sandi baru
use App\Http\Controllers\Auth\PasswordController; 
 // Controller untuk mengatur pengubahan kata sandi pengguna
use App\Http\Controllers\Auth\PasswordResetLinkController; 
 // Controller untuk menangani pengaturan ulang kata sandi
use App\Http\Controllers\Auth\RegisteredUserController;  
// Controller untuk menangani proses registrasi pengguna
use App\Http\Controllers\Auth\VerifyEmailController; 

// Controller untuk menangani verifikasi email
use Illuminate\Support\Facades\Route;  // Mengimpor facade Route untuk mendefinisikan rute aplikasi

// Grup rute untuk pengguna yang belum login (guest)
Route::middleware('guest')->group(function () {
    // Rute untuk menampilkan formulir pendaftaran
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    // Rute untuk menangani penyimpanan data pendaftaran pengguna baru
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Rute untuk menampilkan formulir login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // Rute untuk menangani login pengguna
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Rute untuk menampilkan formulir permintaan reset kata sandi
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    // Rute untuk menangani permintaan pengiriman tautan reset kata sandi
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Rute untuk menampilkan formulir reset kata sandi setelah menerima token
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    // Rute untuk menangani penyimpanan kata sandi baru setelah reset
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Grup rute untuk pengguna yang sudah terautentikasi (auth)
Route::middleware('auth')->group(function () {
    // Rute untuk menampilkan halaman verifikasi email
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    // Rute untuk menangani verifikasi email setelah menerima token verifikasi
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])  // Middleware untuk memastikan tautan yang ditandatangani dan membatasi laju percakapan
        ->name('verification.verify');

    // Rute untuk mengirimkan kembali notifikasi verifikasi email
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')  // Membatasi jumlah permintaan notifikasi verifikasi yang dapat dilakukan
        ->name('verification.send');

    // Rute untuk menampilkan halaman konfirmasi kata sandi
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    // Rute untuk menangani konfirmasi kata sandi saat pengguna mengubah kata sandi
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Rute untuk mengubah kata sandi pengguna
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Rute untuk menangani logout pengguna
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

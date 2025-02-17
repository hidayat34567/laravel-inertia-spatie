<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // Menyajikan halaman 'Welcome' sebagai tampilan pertama saat mengunjungi root ('/')
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),  // Mengecek apakah rute login tersedia
        'canRegister' => Route::has('register'),  // Mengecek apakah rute registrasi tersedia
        'laravelVersion' => Application::VERSION,  // Menyertakan versi Laravel yang digunakan
        'phpVersion' => PHP_VERSION,  // Menyertakan versi PHP yang digunakan
    ]);
});

Route::get('/dashboard', function () {
    // Menyajikan halaman 'Dashboard' setelah login
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified']) // Middleware 'auth' untuk autentikasi dan 'verified' untuk memastikan email terverifikasi
  ->name('dashboard');  // Memberikan nama pada route ini yaitu 'dashboard'

Route::middleware('auth')->group(function () {
    // Rute-rute yang memerlukan autentikasi pengguna (middleware 'auth')

    // Rute untuk mengelola 'permissions' menggunakan controller PermissionController
    Route::resource('/permissions', PermissionController::class);

    // Rute untuk mengelola 'roles' menggunakan controller RoleController, 
    // tetapi mengecualikan fungsi 'show' (tidak tersedia untuk akses)
    Route::resource('roles', RoleController::class)->except('show');

    // Rute untuk mengelola 'users' menggunakan controller UserController
    Route::resource('/users', UserController::class);

    // Rute untuk mengedit profil pengguna, memanggil metode 'edit' dari ProfileController
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // Rute untuk memperbarui profil pengguna, memanggil metode 'update' dari ProfileController
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Rute untuk menghapus profil pengguna, memanggil metode 'destroy' dari ProfileController
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Menyertakan file autentikasi yang berisi rute terkait registrasi dan login
require __DIR__ . '/auth.php';

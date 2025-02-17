<?php

namespace App\Http\Controllers;  
// Menyatakan bahwa kelas ini berada dalam namespace 'App\Http\Controllers'

use App\Http\Requests\ProfileUpdateRequest; 
 // Mengimpor request khusus untuk validasi dan pengolahan data profil yang diperbarui
use Illuminate\Contracts\Auth\MustVerifyEmail;  
// Mengimpor kontrak untuk pengguna yang perlu memverifikasi email mereka
use Illuminate\Http\RedirectResponse; 
 // Mengimpor kelas untuk menangani respon pengalihan (redirect)
use Illuminate\Http\Request; 
// Mengimpor kelas Request untuk menangani data HTTP request
use Illuminate\Support\Facades\Auth;  
// Mengimpor facade Auth untuk menangani autentikasi pengguna
use Illuminate\Support\Facades\Redirect; 
 // Mengimpor facade Redirect untuk melakukan pengalihan
use Inertia\Inertia;  
// Mengimpor Inertia untuk merender halaman berbasis React/Vue
use Inertia\Response; 
 // Mengimpor Response untuk menangani respon dari Inertia


class ProfileController extends Controller  
// Mendefinisikan controller untuk menangani logika profil pengguna
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        // Merender tampilan profil pengguna (React/Vue)
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,  
            // Mengecek apakah pengguna harus memverifikasi email
            'status' => session('status'),  
            // Menyertakan status (misalnya, pesan sukses) jika ada
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Mengisi data profil pengguna dengan data yang telah tervalidasi
        $request->user()->fill($request->validated());

        // Jika email diperbarui, setel ulang status verifikasi email
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;  // Reset verifikasi email
        }

        // Menyimpan perubahan profil pengguna ke database
        $request->user()->save();

        // Setelah berhasil mengupdate, mengalihkan pengguna kembali ke halaman edit profil
        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Memvalidasi bahwa pengguna telah memasukkan kata sandi yang benar untuk menghapus akun
        $request->validate([
            'password' => ['required', 'current_password'],  
            // Memastikan kata sandi yang dimasukkan adalah kata sandi pengguna saat ini
        ]);

        $user = $request->user(); 
         // Mengambil data pengguna yang sedang login

        Auth::logout();  
        // Melakukan logout pengguna setelah menghapus akun

        $user->delete();  
        // Menghapus akun pengguna dari database

        // Menghapus sesi pengguna dan me-reset token untuk menghindari masalah keamanan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Mengalihkan pengguna ke halaman utama setelah menghapus akun
        return Redirect::to('/');
    }
}

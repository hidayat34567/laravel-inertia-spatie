<?php

use Illuminate\Foundation\Inspiring;
  // Mengimpor kelas Inspiring yang berisi kutipan-kutipan inspiratif
use Illuminate\Support\Facades\Artisan; 
 // Mengimpor facade Artisan untuk menjalankan perintah Artisan di Laravel
// Mendefinisikan perintah 'inspire' yang akan dijalankan setiap jam
Artisan::command('inspire', function () {
    // Menampilkan kutipan inspiratif dengan memanggil metode 'quote' dari kelas Inspiring
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')  // Menetapkan tujuan perintah ini sebagai 'Display an inspiring quote'
  ->hourly();  // Menjadwalkan perintah ini untuk dijalankan setiap jam

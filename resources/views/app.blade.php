<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<!-- Menetapkan bahasa dokumen sesuai dengan pengaturan lokal aplikasi -->

<head>
    <meta charset="utf-8">
    <!-- Mendefinisikan set karakter untuk dokumen ini menggunakan UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Membuat halaman dapat diresponsif, agar dapat menyesuaikan ukuran layar perangkat mobile -->
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    <!-- Menetapkan judul halaman menggunakan nilai konfigurasi 'app.name' atau 'Laravel' sebagai default -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <!-- Menghubungkan ke server font eksternal dengan preconnect untuk mengurangi waktu pemuatan -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Menyertakan font Figtree dari Bunny Fonts untuk aplikasi -->

    <!-- Scripts -->
    @routes
    <!-- Memuat rute aplikasi dengan Inertia.js -->
    @viteReactRefresh
    <!-- Menambahkan dukungan untuk hot-reloading React dengan Vite (hanya untuk pengembangan) -->
    @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])
    <!-- Mengimpor file JavaScript menggunakan Vite untuk aplikasi React, mengimpor app.jsx dan komponen berdasarkan nama halaman -->

    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
    <!-- Bagian ini akan dimuat dengan konten yang dihasilkan oleh Inertia.js -->
</body>

</html>

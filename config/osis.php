<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Akun admin awal
    |--------------------------------------------------------------------------
    |
    | Hanya dipakai oleh ProduksiSeeder saat instalasi baru. Isi lewat
    | ADMIN_NAME / ADMIN_EMAIL / ADMIN_PASSWORD di .env, lalu hapus
    | ADMIN_PASSWORD dari .env setelah akun berhasil dibuat.
    |
    */

    'admin' => [
        'name' => env('ADMIN_NAME', 'Administrator'),
        'email' => env('ADMIN_EMAIL'),
        'password' => env('ADMIN_PASSWORD'),
    ],

];

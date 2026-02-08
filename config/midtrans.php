<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi Midtrans Payment Gateway
    | Mode Sandbox untuk testing, Production untuk live
    |
    */

    // Sandbox Credentials (untuk testing)
    'server_key' => env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-xxxxx'), // Ganti dengan Server Key Sandbox Anda
    'client_key' => env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-xxxxx'), // Ganti dengan Client Key Sandbox Anda

    // Set ke true untuk production, false untuk sandbox
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    // Sanitize untuk keamanan
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),

    // Enable 3D Secure
    'is_3ds' => env('MIDTRANS_IS_3DS', true),

    /*
    |--------------------------------------------------------------------------
    | Production Credentials (uncomment jika sudah siap production)
    |--------------------------------------------------------------------------
    |
    | Setelah testing berhasil di sandbox, ganti dengan kredensial production:
    | 1. Daftar akun production di Midtrans
    | 2. Ganti server_key dan client_key dengan yang production
    | 3. Ubah is_production menjadi true
    |
    */

    // 'server_key' => env('MIDTRANS_SERVER_KEY', 'Mid-server-xxxxx'),
    // 'client_key' => env('MIDTRANS_CLIENT_KEY', 'Mid-client-xxxxx'),
    // 'is_production' => env('MIDTRANS_IS_PRODUCTION', true),
];

<?php
/**
 * Konfigurasi Utama Service Mobile JKN Bridging (PHP Native)
 * SIMRS Khanza - Antrean Online BPJS Kesehatan
 */

// Timezone Wajib Indonesia Barat
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi Database MySQL SIMRS Khanza
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sikbaru'); // Sesuaikan dengan nama database SIMRS Anda

// Konfigurasi Web Service BPJS Mobile JKN (Antrol v2)
// Catatan: Jika nilai di bawah dikosongkan, aplikasi akan otomatis membaca nilai dari database SIMRS
define('BPJS_CONS_ID', ''); 
define('BPJS_SECRET_KEY', ''); 
define('BPJS_USER_KEY', ''); 
define('BPJS_BASE_URL', ''); // Contoh: https://apijkn.bpjs-kesehatan.go.id/vclaim-rest (atau staging)

// Pengaturan Interval Refresh Worker Dashboard (dalam milidetik)
define('AUTO_SYNC_INTERVAL_MS', 600000); // 600000 ms = 10 Menit

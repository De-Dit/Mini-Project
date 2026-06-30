<?php
/**
 * =================================================================
 * KONEKSI DATABASE
 * =================================================================
 * File ini membuat satu koneksi PDO yang dipakai di seluruh halaman.
 * PDO + prepared statement dipakai supaya aman dari SQL Injection
 * (sesuai Persyaratan Keamanan pada dokumen SKPL Bab 5.2).
 * =================================================================
 */

// --- UBAH BAGIAN INI SESUAI SETTING HOSTING/XAMPP ANDA ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'sikalog_umkm');
define('DB_USER', 'root');
define('DB_PASS', 'raditz147');          // di XAMPP biasanya kosong
// -----------------------------------------------------------

// Konfigurasi tombol WhatsApp Admin (format internasional tanpa tanda +)
define('WA_NUMBER', '628123456789');

// Base URL website (penting untuk link gambar & redirect)
// Otomatis terdeteksi, tapi bisa di-hardcode jika perlu, contoh:
// define('BASE_URL', 'https://sikalogumkm.com');
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
// Jika dipanggil dari dalam folder /admin, naik satu level untuk BASE_URL
if (basename($scriptDir) === 'admin') {
    $scriptDir = dirname($scriptDir);
}
if (!defined('BASE_URL')) {
    define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . $scriptDir);
}

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Koneksi database gagal. Periksa pengaturan di config/database.php. Detail: ' . $e->getMessage());
}

<?php
// Mulai session di file terpusat ini (Hanya jika belum dimulai)
if (!isset($_SESSION)) {
    session_start();
}

// --- DEFINISIKAN BASE URL ANDA (Perbaikan Final Anti-Warning) ---
if (!defined('BASE_URL')) { 
    // ⚠️ SESUAIKAN DENGAN NAMA FOLDER PROYEK ANDA
    define('BASE_URL', '/web_keamanan_data-UTS/'); 
}
// -----------------------------------------------------------------


// --- KONFIGURASI DATABASE ANDA ---
$db_host = 'localhost';
$db_user = 'root'; 
$db_pass = ''; 
$db_name = 'web_keamanan_data'; 
// ---------------------------------

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

// Set charset untuk memastikan kompatibilitas dan keamanan
$conn->set_charset("utf8mb4");

?>
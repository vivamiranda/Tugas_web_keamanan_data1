<?php
// PERBAIKAN INCLUDE PATH: Naik satu level ke root, lalu masuk ke folder includes/
include '../includes/koneksi.php'; 

if (!isset($_SESSION['user_id'])) {
    // REDIRECT AMAN: Menggunakan ../ untuk kembali ke root login.php
    header('Location: ../login.php'); 
    exit();
}

$table_name = 'upload_articles'; 
$redirect_to = '../index.php'; // Kembali ke index.php di root

if (isset($_GET['id'])) { 
    // ⚠️ TIDAK ADA SANITASI: Raw Query (SQL INJECTION VULNERABILITY)
    $post_id_vul = $_GET['id'];
    
    // --- LOGIKA RENTAN: RAW QUERY & BAC DILANGGAR (IDOR) ---
    // Query ini hanya bergantung pada ID POSTINGAN.
    // Penyerang dapat memasukkan ID postingan orang lain untuk menghapusnya (IDOR/BAC)
    $sql_vul = "DELETE FROM " . $table_name . " WHERE id = '$post_id_vul'"; 
    
    if ($conn->query($sql_vul) === TRUE) {
        if ($conn->affected_rows > 0) {
            $_SESSION['message'] = "Postingan berhasil dihapus secara **RENTAN**. (Raw Query dan BAC Gagal)";
        } else {
            $_SESSION['error'] = "Rentan: Postingan tidak ditemukan (Coba eksploitasi ID!).";
        }
    } else {
        $_SESSION['error'] = "Rentan: Terjadi kesalahan saat menghapus postingan: " . $conn->error;
    }

} else {
    $_SESSION['error'] = "Permintaan hapus tidak valid.";
}

header('Location: ' . $redirect_to);
exit();
?>
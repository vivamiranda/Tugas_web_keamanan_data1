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

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $post_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // --- LOGIKA AMAN: PREPARED STATEMENTS & VERIFIKASI KEPEMILIKAN ---
    // SQLi Dihindari: Parameterized Query
    // BAC Dihindari: Klausa AND author_id = ? memastikan hanya pemilik yang bisa menghapus
    $sql = "DELETE FROM " . $table_name . " WHERE id = ? AND author_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $user_id); 
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['message'] = "Postingan berhasil dihapus secara **AMAN** (Prepared Statements & BAC Check).";
        } else {
            // Dipicu jika IDOR dilakukan (mencoba hapus post orang lain)
            $_SESSION['error'] = "Aman: Postingan tidak ditemukan atau Anda **tidak memiliki izin** untuk menghapusnya.";
        }
    } else {
        $_SESSION['error'] = "Aman: Terjadi kesalahan saat menghapus postingan.";
    }
    $stmt->close();

} else {
    $_SESSION['error'] = "Permintaan hapus tidak valid.";
}

header('Location: ' . $redirect_to);
exit();
?>
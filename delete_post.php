<?php
session_start();
include 'koneksi.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$mode = $_GET['mode'] ?? 'safe'; 
$table_name = 'upload_articles'; 
$redirect_to = 'index.php'; 

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $post_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    if ($mode === 'safe') {
        // --- LOGIKA AMAN: MENGGUNAKAN PREPARED STATEMENTS ---
        $sql = "DELETE FROM " . $table_name . " WHERE id = ? AND author_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $post_id, $user_id); 
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['message'] = "Postingan berhasil dihapus secara **AMAN** (Prepared Statements).";
            } else {
                $_SESSION['error'] = "Aman: Postingan tidak ditemukan atau Anda tidak memiliki izin untuk menghapusnya.";
            }
        } else {
            $_SESSION['error'] = "Aman: Terjadi kesalahan saat menghapus postingan.";
        }
        $stmt->close();

    } else {
        // --- LOGIKA RENTAN: MENGGUNAKAN RAW QUERY ---
        $post_id_vul = $_GET['id'];
        $sql_vul = "DELETE FROM " . $table_name . " WHERE id = '$post_id_vul' AND author_id = '$user_id'";
        
        if ($conn->query($sql_vul) === TRUE) {
            if ($conn->affected_rows > 0) {
                $_SESSION['message'] = "Postingan berhasil dihapus secara **RENTAN** (Raw Query).";
            } else {
                $_SESSION['error'] = "Rentan: Postingan tidak ditemukan atau Anda tidak memiliki izin untuk menghapusnya.";
            }
        } else {
            $_SESSION['error'] = "Rentan: Terjadi kesalahan saat menghapus postingan.";
        }
    }
} else {
    $_SESSION['error'] = "Permintaan hapus tidak valid.";
}

header('Location: ' . $redirect_to);
exit();
?>
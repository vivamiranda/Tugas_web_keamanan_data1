<?php
// File ini di-include dari artikel_view.php, jadi koneksi.php sudah tersedia.

$current_url = "artikel_view.php?id=$article_id&mode=safe"; 
$vul_url = "artikel_view.php?id=$article_id&mode=vul";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment_safe'])) {
    
    // 1. INPUT SANITASI: Gunakan htmlspecialchars pada input (Mitigasi XSS pada input)
    $comment_text = htmlspecialchars($_POST['comment_text']); 

    // 2. Simpan ke Database (Prepared Statements - AMAN)
    $stmt = $conn->prepare("INSERT INTO comments (article_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $article_id, $user_id, $comment_text);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Komentar berhasil dipublikasikan secara **AMAN** (Input Sanitized).";
    } else {
        $_SESSION['error'] = "Gagal menyimpan komentar: " . $stmt->error;
    }
    $stmt->close();
    header("Location: " . $current_url);
    exit();
}
?>

<div class="flex justify-end gap-2 mb-4">
    <span class="mode-safe">Mode: Aman</span>
    <a href="<?php echo htmlspecialchars($vul_url); ?>" class="mode-vul">Ganti ke Mode Rentan</a>
</div>

<form method="POST" action="<?php echo htmlspecialchars($current_url); ?>">
    <textarea name="comment_text" rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" placeholder="Tulis komentar Anda..." required></textarea>
    <button type="submit" name="submit_comment_safe" class="mt-3 btn-blue">Kirim Komentar</button>
</form>
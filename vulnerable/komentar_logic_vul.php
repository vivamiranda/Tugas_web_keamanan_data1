<?php
// File ini di-include dari artikel_view.php, jadi koneksi.php sudah tersedia.

$current_url = "artikel_view.php?id=$article_id&mode=vul"; 
$safe_url = "artikel_view.php?id=$article_id&mode=safe";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment_vul'])) {
    
    // ⚠️ TIDAK ADA SANITASI PADA INPUT (XSS VULNERABILITY)
    $comment_text = $_POST['comment_text']; 

    // Simpan ke Database (Prepared Statements, tapi data sudah rentan)
    $stmt = $conn->prepare("INSERT INTO comments (article_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $article_id, $user_id, $comment_text);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Komentar berhasil dipublikasikan secara **RENTAN** (Raw Input).";
    } else {
        $_SESSION['error'] = "Gagal menyimpan komentar: " . $stmt->error;
    }
    $stmt->close();
    header("Location: " . $current_url);
    exit();
}
?>

<div class="flex justify-end gap-2 mb-4">
    <span class="mode-safe bg-red-100 text-red-600 border-red-500">Mode: Rentan</span>
    <a href="<?php echo htmlspecialchars($safe_url); ?>" class="mode-vul text-green-600">Ganti ke Mode Aman</a>
</div>

<form method="POST" action="<?php echo htmlspecialchars($current_url); ?>">
    <textarea name="comment_text" rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500" placeholder="Tulis komentar Anda..." required></textarea>
    <button type="submit" name="submit_comment_vul" class="mt-3 btn-blue bg-red-600 hover:bg-red-700">Kirim Komentar</button>
</form>
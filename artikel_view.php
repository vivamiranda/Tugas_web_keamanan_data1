<?php
// PERBAIKAN INCLUDE PATH
include 'includes/koneksi.php'; 

$article_id = $_GET['id'] ?? null;
$mode = $_GET['mode'] ?? 'safe'; // Default mode adalah 'safe'

if (!$article_id || !is_numeric($article_id)) {
    $_SESSION['error'] = "Artikel tidak valid.";
    header('Location: index.php');
    exit();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'Guest';

// Ambil Detail Artikel (Menggunakan Prepared Statements - AMAN)
$article = null;
$sql = "SELECT 
            ua.*, 
            us.fullname 
        FROM upload_articles ua 
        JOIN sqli_users_safe us ON ua.author_id = us.id
        WHERE ua.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $article = $result->fetch_assoc();
} else {
    $_SESSION['error'] = "Artikel tidak ditemukan.";
    header('Location: index.php');
    exit();
}
$stmt->close();

// --- LOGIKA FORM KOMENTAR BERDASARKAN MODE ---
$comment_file = ($mode == 'vul') ? 'vulnerable/komentar_logic_vul.php' : 'secure/komentar_logic_safe.php';

// Ambil semua komentar untuk artikel ini (Menggunakan Prepared Statements - AMAN)
$comments = [];
$sql_comments = "SELECT 
                    c.comment_text, 
                    us.fullname, 
                    c.created_at
                 FROM comments c
                 JOIN sqli_users_safe us ON c.user_id = us.id
                 WHERE c.article_id = ?
                 ORDER BY c.created_at ASC";

$stmt_comments = $conn->prepare($sql_comments);
$stmt_comments->bind_param("i", $article_id);
$stmt_comments->execute();
$result_comments = $stmt_comments->get_result();
while ($row = $result_comments->fetch_assoc()) {
    $comments[] = $row;
}
$stmt_comments->close();

$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> | Detail</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide-icons"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F9FAFB; } 
        .primary-color { color: #10B981; } 
        .bg-primary { background-color: #10B981; }
        .hover\:bg-primary:hover { background-color: #059669; }
        .btn-blue { background-color: #3b82f6; color: white; padding: 10px 20px; border-radius: 6px; font-weight: 600; }
        .btn-blue:hover { background-color: #2563eb; }
        .mode-safe { border: 1px solid #10b981; color: #10b981; padding: 4px 8px; border-radius: 4px; font-size: 0.875rem; }
        .mode-vul { color: #ef4444; }
        .mode-vul:hover { text-decoration: underline; }
    </style>
</head>
<body class="bg-gray-100">
    <main class="container mx-auto max-w-4xl p-4 mt-8">
        <?php if ($message): /* ... (Kode alert success) ... */ endif; ?>
        <?php if ($error): /* ... (Kode alert error) ... */ endif; ?>

        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <a href="index.php" class="text-gray-600 hover:text-primary font-medium flex items-center mb-4">
                &larr; Kembali ke Dashboard
            </a>
            <h1 class="text-4xl font-extrabold text-gray-800 mb-4"><?php echo htmlspecialchars($article['title']); ?></h1>
            <p class="text-sm text-gray-500 mb-6">
                Oleh <span class="font-semibold text-gray-700"><?php echo htmlspecialchars($article['fullname']); ?></span>
                &bullet; <?php echo date('d F Y', strtotime($article['created_at'])); ?>
            </p>
            
            <?php if ($article['file_path']): ?>
                <img src="<?php echo htmlspecialchars($article['file_path']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" 
                     class="w-full h-80 object-cover object-center rounded-lg mb-6">
            <?php else: ?>
                 <div class="w-full h-80 bg-gray-200 flex items-center justify-center text-gray-500 rounded-lg mb-6">No Image Found</div>
            <?php endif; ?>

            <div class="text-gray-700 leading-relaxed pt-4 border-t border-gray-100">
                <p><?php echo nl2br(htmlspecialchars($article['body'])); ?></p>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl shadow-lg border border-gray-100">
            
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Tulis Komentar</h2>
            
            <?php if ($is_logged_in): ?>
                
                <?php include $comment_file; ?>

            <?php else: ?>
                <p class="text-gray-500">Anda harus <a href="login.php" class="text-primary font-semibold hover:underline">login</a> untuk meninggalkan komentar.</p>
            <?php endif; ?>

            <h2 class="text-2xl font-bold text-gray-800 mt-10 mb-4 border-t pt-6">Komentar</h2>
            
            <?php if (empty($comments)): ?>
                <p class="text-gray-500">Belum ada komentar.</p>
            <?php endif; ?>

            <div class="space-y-4">
                <?php foreach ($comments as $comment): ?>
                    <div class="p-4 border rounded-lg bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-gray-800"><?php echo htmlspecialchars($comment['fullname']); ?></span>
                            <span class="text-xs text-gray-500"><?php echo date('H:i, d F Y', strtotime($comment['created_at'])); ?></span>
                        </div>
                        <p class="text-gray-700">
                            <?php 
                            if ($mode == 'vul') {
                                // TAMPILAN RENTAN (XSS): Tanpa htmlspecialchars
                                echo $comment['comment_text'];
                            } else {
                                // TAMPILAN AMAN (XSS MITIGATION): htmlspecialchars()
                                echo htmlspecialchars($comment['comment_text']);
                            }
                            ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </main>
    </body>
</html>
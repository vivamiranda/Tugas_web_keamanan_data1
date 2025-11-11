<?php
session_start();
// 1. INCLUDE PATH YANG BENAR: Dari root ke folder includes/
include 'includes/koneksi.php'; 

// Cek status login
$is_logged_in = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? 'Guest';
$user_id = $_SESSION['user_id'] ?? null;

// Ambil pesan session (message/error) dan hapus setelah ditampilkan
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';

unset($_SESSION['message']);
unset($_SESSION['error']);

// Ambil data postingan
$articles = [];
$sql = "SELECT 
            ua.*, 
            us.fullname 
        FROM upload_articles ua 
        JOIN sqli_users_safe us ON ua.author_id = us.id
        ORDER BY ua.created_at DESC";

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Keamanan Data - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide-icons"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F9FAFB; } 
        .primary-color { color: #10B981; } 
        .bg-primary { background-color: #10B981; }
        .hover\:bg-primary:hover { background-color: #059669; }
    </style>
</head>
<body class="bg-gray-100">

    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="flex items-center justify-between h-16">

                <div class="flex items-center gap-6">
                    <a href="index.php" class="text-2xl font-extrabold primary-color">🛡️ Web Keamanan</a>
                    
                    <nav class="hidden sm:flex space-x-4">
                        <a href="secure/search_safe.php" class="text-sm font-semibold text-gray-700 hover:text-primary transition duration-150">Search (Safe)</a>
                        <a href="vulnerable/search_vul.php" class="text-sm font-semibold text-gray-700 hover:text-red-500 transition duration-150">Search (Vulnerable)</a>
                        <a href="secure/invoice_view_safe.php" class="text-xs font-medium text-gray-500 hover:text-primary border border-gray-200 px-2 py-1 rounded-full transition duration-150">Invoice (Safe)</a>
                        <a href="vulnerable/invoice_view_vul.php" class="text-xs font-medium text-gray-500 hover:text-red-500 border border-gray-200 px-2 py-1 rounded-full transition duration-150">Invoice (Vuln)</a>
                    </nav>
                </div>

                <div class="flex items-center gap-4">
                    <?php if ($is_logged_in): ?>
                        <a href="post.php" class="bg-primary text-white text-sm font-bold px-4 py-2 rounded-full hover:bg-green-700 transition duration-200 shadow-md">Post</a>
                        <div class="flex items-center space-x-3">
                            <a href="javascript:void(0)" id="edit-profile-button" class="text-sm font-semibold text-gray-700 hover:text-primary transition duration-150">
                                Hi, **<?php echo htmlspecialchars($username); ?>** </a>
                            <a href="logout.php" class="text-sm text-red-600 hover:bg-red-50 px-3 py-1 rounded-md transition duration-150">
                                Log out
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="text-sm font-semibold text-gray-700 hover:text-primary transition duration-150">Login</a>
                        <a href="register.php" class="bg-primary text-white text-sm font-bold px-3 py-1 rounded-md hover:bg-green-700 transition duration-200">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="container mx-auto max-w-7xl p-4 mt-8">

        <?php if ($message): ?>
            <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-lg shadow-sm mb-6" role="alert">
                <strong class="font-bold">✅ Sukses!</strong>
                <span class="block sm:inline"><?php echo $message; ?></span>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-lg shadow-sm mb-6" role="alert">
                <strong class="font-bold">❌ Error!</strong>
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <h1 class="text-4xl font-extrabold text-gray-800 mb-8">Postingan Terbaru</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($articles)): ?>
                <div class="md:col-span-3 text-center text-gray-500 p-10 bg-white rounded-xl shadow-lg">Belum ada postingan yang tersedia.</div>
            <?php endif; ?>
            
            <?php foreach ($articles as $article): ?>
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 flex flex-col overflow-hidden">
                    
                    <?php if ($article['file_path']): ?>
                        <a href="artikel_view.php?id=<?php echo $article['id']; ?>&mode=safe">
                            <img src="<?php echo htmlspecialchars($article['file_path']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" 
                                 class="w-full h-48 object-cover object-center">
                        </a>
                    <?php else: ?>
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">
                            <i data-lucide="file-text" class="w-12 h-12"></i>
                        </div>
                    <?php endif; ?>

                    <div class="p-6 flex flex-col flex-grow">
                        <a href="artikel_view.php?id=<?php echo $article['id']; ?>&mode=safe" class="text-xl font-bold text-gray-900 hover:text-primary transition duration-150 mb-2">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </a>
                        
                        <p class="text-sm text-gray-500 mb-4 flex-grow">
                            Oleh <span class="font-semibold text-gray-700"><?php echo htmlspecialchars($article['fullname']); ?></span>
                            &bullet; <?php echo date('d F Y', strtotime($article['created_at'])); ?>
                        </p>
                        
                        <?php if ($is_logged_in && $article['author_id'] == $user_id): ?>
                            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between gap-3">
                                
                                <a href="secure/delete_post_safe.php?id=<?php echo $article['id']; ?>"
                                   onclick="return confirm('ANDA YAKIN? Hapus menggunakan Logika AMAN (Prepared Statements & BAC Check).')"
                                   class="flex-1 text-center text-sm font-semibold text-green-700 border border-green-500 hover:bg-green-50 px-4 py-2 rounded-lg transition duration-150">
                                    Hapus (Aman)
                                </a>
                                
                                <a href="vulnerable/delete_post_vul.php?id=<?php echo $article['id']; ?>"
                                   onclick="return confirm('BAHAYA! Hapus menggunakan Logika RENTAN (Raw Query & IDOR Possible).')"
                                   class="flex-1 text-center text-sm font-semibold text-red-700 border border-red-500 hover:bg-red-50 px-4 py-2 rounded-lg transition duration-150">
                                    Hapus (Rentan)
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <?php if ($is_logged_in) include 'includes/modal_profile.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            try {
                lucide.createIcons();
            } catch (e) {
                console.error("Lucide icons gagal dimuat:", e);
            }
        });
    </script>
</body>
</html>
<?php
// Path include koneksi ../ sudah benar
include '../koneksi.php';

$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// AMBIL MODE DARI URL, default 'safe'
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'safe'; 

if ($article_id == 0) {
    die("Artikel tidak ditemukan.");
}

// ... (Sisa logika PHP di atas sama, tidak perlu diubah) ...
// ... (Saya akan langsung ke bagian HTML untuk mempersingkat) ...
$comment_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!isset($_SESSION['user_id'])) {
        $comment_error = "Anda harus login untuk berkomentar.";
    } else {
        $comment_text = trim($_POST['comment_text']);
        $user_id = $_SESSION['user_id'];
        
        if (empty($comment_text)) {
            $comment_error = "Komentar tidak boleh kosong.";
        } else {
            $stmt_comment = $conn->prepare("INSERT INTO comments (article_id, user_id, comment_text) VALUES (?, ?, ?)");
            $stmt_comment->bind_param("iis", $article_id, $user_id, $comment_text);
            
            if ($stmt_comment->execute()) {
                // PERBAIKAN: Gunakan path lengkap DAN BAWA MODE-nya
                header("Location: " . BASE_URL . "Artikel/Artikel1.php?id=$article_id&mode=$mode#komentar-section");
                exit();
            } else {
                $comment_error = "Gagal menyimpan komentar: " . $stmt_comment->error;
            }
            $stmt_comment->close();
        }
    }
}

$sql = "SELECT 
            a.id, a.title, a.body, a.file_path, a.created_at, 
            u.fullname 
        FROM upload_articles AS a
        JOIN sqli_users_safe AS u ON a.author_id = u.id
        WHERE a.id = ?";

$stmt_article = $conn->prepare($sql);
$stmt_article->bind_param("i", $article_id);
$stmt_article->execute();
$result_article = $stmt_article->get_result();

if ($result_article->num_rows == 0) {
    die("Artikel tidak ditemukan.");
}
$row_article = $result_article->fetch_assoc();
$stmt_article->close();

$title = htmlspecialchars($row_article['title']);
$body = nl2br(htmlspecialchars($row_article['body'])); 
$author_name = htmlspecialchars($row_article['fullname']);
$created_at = date('d-m-Y H:i', strtotime($row_article['created_at']));

$file_path_raw = $row_article['file_path'];
if (empty($file_path_raw) || !file_exists('../' . $file_path_raw)) { 
    $display_image_path = 'https://placehold.co/600x330/333/999?text=No+Image';
} else {
    $display_image_path = BASE_URL . htmlspecialchars($file_path_raw); 
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> Web Keamanan Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide-icons"></script> 
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F0F2F5; }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        .article-content p { margin-bottom: 1.5rem; line-height: 1.75; color: #374151; }
    </style>
</head>
<body class="text-gray-800">

    <header class="bg-white shadow-sm w-full sticky top-0 z-50">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-4">
                    <a href="<?php echo BASE_URL; ?>index.php" class="text-2xl font-bold text-blue-600">Web Keamanan Data</a>
                    <a href="<?php echo BASE_URL; ?>search_safe.php" class="text-sm font-medium text-green-600 hover:text-green-700">Search (Safe)</a>
                    <a href="<?php echo BASE_URL; ?>search_vul.php" class="text-sm font-medium text-red-600 hover:text-red-700">Search (Vulnerable)</a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo BASE_URL; ?>post.php" class="hidden sm:flex items-center bg-transparent text-blue-600 px-4 py-2 rounded-lg text-sm font-semibold border border-blue-600 hover:bg-blue-50">
                            <i data-lucide="edit-3" class="w-4 h-4 mr-1"></i>
                            Buat Post
                        </a>
                        
                        <div class="flex items-center space-x-3">
                            <a href="javascript:void(0)" id="edit-profile-button" class="text-sm font-semibold text-gray-700 hover:text-blue-600">
                                Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <a href="<?php echo BASE_URL; ?>logout.php" class="text-sm text-red-600 hover:bg-gray-100 px-3 py-1 rounded-md">
                                Log out
                            </a>
                        </div>
                        <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>login.php" class="hidden sm:flex items-center bg-transparent text-blue-600 px-4 py-2 rounded-lg text-sm font-semibold border border-blue-600 hover:bg-blue-50">
                            <i data-lucide="edit-3" class="w-4 h-4 mr-1"></i>
                            Buat Post
                        </a>
                        <a href="<?php echo BASE_URL; ?>login.php" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">
                            Masuk
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto max-w-7xl p-4 mt-4">
        <div class="flex justify-center">
            <main class="w-full lg:max-w-3xl">
                
                <div class="mb-4">
                    <a href="<?php echo BASE_URL; ?>index.php" class="text-blue-600 hover:underline">
                        &larr; Kembali ke Halaman Utama
                    </a>
                </div>

                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200 p-6 md:p-8">
                    
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight">
                        <?php echo $title; ?>
                    </h1>
                    
                    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
                        <span class="font-semibold text-gray-800"><?php echo $author_name; ?></span>
                        <span>•</span>
                        <span><?php echo $created_at; ?></span>
                    </div>

                    <img src="<?php echo $display_image_path; ?>" 
                         alt="<?php echo $title; ?>" 
                         class="w-full h-auto rounded-lg object-cover border mb-6"
                         onerror="this.src='https://placehold.co/600x330/333/999?text=Image+Error'">

                    <div class="article-content text-lg">
                        <p><?php echo $body; ?></p>
                    </div>
                </div>


                <div id="komentar-section" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 md:p-8 mt-6">
                    
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">Tulis Komentar</h2>
                        <div>
                            <?php if ($mode === 'vuln'): ?>
                                <span class="text-sm font-medium text-red-700 bg-red-100 px-3 py-1 rounded-full">Mode: Rentan (XSS Aktif)</span>
                                <a href="Artikel1.php?id=<?php echo $article_id; ?>&mode=safe#komentar-section" class="text-sm ml-2 text-green-600 hover:underline">Ganti ke Mode Aman</a>
                            <?php else: ?>
                                <span class="text-sm font-medium text-green-700 bg-green-100 px-3 py-1 rounded-full">Mode: Aman</span>
                                <a href="Artikel1.php?id=<?php echo $article_id; ?>&mode=vuln#komentar-section" class="text-sm ml-2 text-red-600 hover:underline">Ganti ke Mode Rentan</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($comment_error)): ?>
                        <p class="text-red-500 bg-red-100 p-3 rounded-lg mb-4"><?php echo $comment_error; ?></p>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form action="<?php echo BASE_URL; ?>Artikel/Artikel1.php?id=<?php echo $article_id; ?>&mode=<?php echo $mode; ?>#komentar-section" method="POST">
                            <textarea 
                                name="comment_text" 
                                rows="4" 
                                class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-600"
                                placeholder="Tulis komentar Anda..."></textarea>
                            <button 
                                type="submit" 
                                name="submit_comment"
                                class="mt-3 bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700">
                                Kirim Komentar
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-gray-600">
                            Silakan <a href="<?php echo BASE_URL; ?>login.php" class="text-blue-600 font-semibold hover:underline">Masuk</a> untuk berkomentar.
                        </p>
                    <?php endif; ?>

                    <h2 class="text-2xl font-bold mt-8 mb-4">Komentar</h2>
                    <div class="space-y-6">
                        <?php
                        $sql_comments = "SELECT 
                                            c.comment_text, c.created_at, 
                                            u.fullname 
                                        FROM comments AS c
                                        JOIN sqli_users_safe AS u ON c.user_id = u.id
                                        WHERE c.article_id = ?
                                        ORDER BY c.created_at DESC";
                        
                        $stmt_comments = $conn->prepare($sql_comments);
                        $stmt_comments->bind_param("i", $article_id);
                        $stmt_comments->execute();
                        $result_comments = $stmt_comments->get_result();

                        if ($result_comments->num_rows > 0):
                            while ($comment = $result_comments->fetch_assoc()):
                        ?>
                                <div class="border-b border-gray-200 pb-4">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($comment['fullname']); ?></span>
                                        <span class="text-xs text-gray-500"><?php echo date('d-m-Y H:i', strtotime($comment['created_at'])); ?></span>
                                    </div>
                                    <p class="text-gray-700 break-words">
                                        <?php
                                        if ($mode === 'vuln') {
                                            // VULNERABLE: Menampilkan output raw (Stored XSS)
                                            echo $comment['comment_text']; 
                                        } else {
                                            // SAFE: Menggunakan htmlspecialchars
                                            echo nl2br(htmlspecialchars($comment['comment_text']));
                                        }
                                        ?>
                                        </p>
                                </div>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <p class="text-gray-500">Belum ada komentar.</p>
                        <?php
                        endif;
                        $stmt_comments->close(); 
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php if (isset($_SESSION['user_id'])) include '../modal_profile.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            try {
                lucide.createIcons();
            } catch (e) {
                console.error("Lucide icons gagal dimuat:", e);
            }

            // MODIFIKASI: Logika avatarButton dan dropdownMenu DIHAPUS

            const editProfileButton = document.getElementById('edit-profile-button');
            const profileModal = document.getElementById('profile-modal');
            const closeModalButton = document.getElementById('close-modal-button');

            if (editProfileButton && profileModal && closeModalButton) {
                editProfileButton.addEventListener('click', function() {
                    profileModal.classList.remove('hidden');
                });
                closeModalButton.addEventListener('click', function() {
                    profileModal.classList.add('hidden');
                });
                profileModal.addEventListener('click', function(event) {
                    if (event.target === profileModal) {
                        profileModal.classList.add('hidden');
                    }
                });
            }

            <?php 
            $modal_error = $_SESSION['modal_error'] ?? null;
            $modal_success = $_SESSION['modal_success'] ?? null;
            if (($modal_error || $modal_success) && isset($_GET['profile_updated'])): ?>
                if (profileModal) profileModal.classList.remove('hidden');
            <?php endif; ?>
        });
    </script>

</body>
</html>
<?php
session_start();
include 'koneksi.php'; 

$message = $_SESSION['message'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['message']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Keamanan Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide-icons"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F9FAFB; } 
        .primary-color { color: #10B981; } 
        .bg-primary { background-color: #10B981; }
        .hover\:bg-primary:hover { background-color: #059669; }
        .text-blue-600 { color: #10B981; } 
    </style>
</head>
<body class="text-gray-800">

    <header class="bg-white shadow-md w-full sticky top-0 z-50">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="flex justify-between items-center h-16">
                
                <div class="flex items-center gap-6">
                    <a href="<?php echo BASE_URL; ?>index.php" class="text-2xl font-extrabold primary-color transition duration-150 hover:opacity-90">🛡️ Web Keamanan</a>
                    <nav class="hidden sm:flex space-x-4">
                        <a href="<?php echo BASE_URL; ?>search_safe.php" class="text-sm font-medium text-gray-600 hover:text-green-600 p-2 rounded-md transition duration-150">Search (Safe)</a>
                        <a href="<?php echo BASE_URL; ?>search_vul.php" class="text-sm font-medium text-gray-600 hover:text-red-600 p-2 rounded-md transition duration-150">Search (Vulnerable)</a>
                    </nav>
                </div>

                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo BASE_URL; ?>post.php" class="flex items-center bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-md hover:bg-green-700 transition duration-150">
                            <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Post
                        </a>
                        
                        <div class="flex items-center space-x-3">
                            <a href="javascript:void(0)" id="edit-profile-button" class="text-sm font-semibold text-gray-700 hover:text-primary transition duration-150">
                                Hi, **<?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>**
                            </a>
                            <a href="<?php echo BASE_URL; ?>logout.php" class="text-sm text-red-600 hover:bg-red-50 px-3 py-1 rounded-md transition duration-150">
                                Log out
                            </a>
                        </div>
                        <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>login.php" class="bg-primary text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition duration-150">
                            Masuk
                        </a>
                    <?php endif; ?>
                </div> 
            </div>
        </div>
    </header>

<div class="container mx-auto max-w-7xl p-4 mt-8">
    <div class="flex justify-center">
        <main class="w-full lg:max-w-7xl">
            <?php if ($message || $error): ?>
                <div class="mb-6 space-y-4"> 
                    <?php if ($message): ?>
                        <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-lg shadow-sm" role="alert">
                            <strong class="font-bold">✅ Sukses!</strong>
                            <span class="block sm:inline"><?php echo $message; ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-lg shadow-sm" role="alert">
                            <strong class="font-bold">❌ Error!</strong>
                            <span class="block sm:inline"><?php echo $error; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6"> 

            <?php
            $sql = "SELECT 
                        a.id, a.title, a.file_path, a.created_at, a.author_id, u.fullname,
                        COUNT(c.id) AS comment_count 
                    FROM upload_articles AS a
                    JOIN sqli_users_safe AS u ON a.author_id = u.id
                    LEFT JOIN comments AS c ON a.id = c.article_id
                    GROUP BY a.id, a.title, a.file_path, a.created_at, a.author_id, u.fullname
                    ORDER BY a.created_at DESC";
            
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0):
                while($row = $result->fetch_assoc()):
                    $post_id = $row['id'];
                    $title = htmlspecialchars($row['title']);
                    $created_at = date('d F Y', strtotime($row['created_at']));
                    $author_name = htmlspecialchars($row['fullname']); 
                    $comment_count = $row['comment_count'];
                    $author_id = $row['author_id']; 
                    
                    $file_path_raw = $row['file_path'];
                    $display_image_path = (empty($file_path_raw) || !file_exists($file_path_raw)) 
                        ? 'https://placehold.co/800x450/10B981/ffffff?text=No+Image+Found' 
                        : BASE_URL . htmlspecialchars($file_path_raw);
            ?>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden transition duration-300 hover:shadow-xl border border-gray-100">
                
                <a href="<?php echo BASE_URL; ?>Artikel/Artikel1.php?id=<?php echo $post_id; ?>&mode=safe" class="block">
                    <img src="<?php echo $display_image_path; ?>" alt="<?php echo $title; ?>" class="w-full object-cover" style="aspect-ratio: 16/9;" onerror="this.src='https://placehold.co/800x450/10B981/ffffff?text=No+Image+Found'">
                </a>

                <div class="p-4 sm:p-6"> 
                    
                    <a href="<?php echo BASE_URL; ?>Artikel/Artikel1.php?id=<?php echo $post_id; ?>&mode=safe" class="block hover:text-green-600 transition duration-150">
                        <h2 class="text-xl font-bold text-gray-900 mb-2 leading-snug">
                            <?php echo $title; ?>
                        </h2>
                    </a>

                    <div class="flex items-center space-x-2 text-xs text-gray-500 mb-4 pt-2 border-t border-gray-100">
                        <span class="flex items-center font-semibold text-gray-700"><?php echo $author_name; ?></span> 
                        <span class="text-gray-300">•</span>
                        <span class="flex items-center"><?php echo $created_at; ?></span>
                    </div>

                    <div class="pt-4 flex justify-between items-center border-t border-gray-100">
                        
                        <a href="<?php echo BASE_URL; ?>Artikel/Artikel1.php?id=<?php echo $post_id; ?>&mode=safe#komentar-section" class="flex items-center space-x-1 text-sm text-gray-600 hover:text-primary transition duration-150">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                            <span><?php echo $comment_count; ?> Komentar</span>
                        </a>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $author_id): ?>
                            <a href="<?php echo BASE_URL; ?>delete_post.php?id=<?php echo $post_id; ?>&mode=safe" 
                               onclick="return confirm('Hapus Postingan? Tindakan ini menggunakan logika AMAN.')"
                               class="flex items-center space-x-1 text-xs bg-red-50 text-red-600 px-3 py-1.5 rounded-lg border border-red-300 hover:bg-red-600 hover:text-white transition duration-150 shadow-sm">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                <span>Hapus</span>
                            </a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
            
            <?php
                endwhile; 
            else:
                echo "<p class='md:col-span-2 lg:col-span-2 text-center text-gray-500 mt-10 p-5 bg-white rounded-xl shadow-lg'>Belum ada post. Mulailah posting!</p>";
            endif;
            ?>

            </div>
        </main>
    </div>
</div>

<?php if (isset($_SESSION['user_id'])) include 'modal_profile.php'; ?>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        try {
            lucide.createIcons();
        } catch (e) {
            console.error("Lucide icons gagal dimuat:", e);
        }
        
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
    });
</script>

</body>
</html>
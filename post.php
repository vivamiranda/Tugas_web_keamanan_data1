<?php
// 1. Sertakan koneksi dan mulai sesi
session_start(); // Pastikan session_start() ada jika belum ada di koneksi.php
include 'koneksi.php';

// 2. Variabel untuk pesan
$message = '';
$error = '';

// 3. Cek apakah user sudah login. Jika belum, tidak bisa post.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 4. Logika POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $title = $_POST['judul'];
    $body = $_POST['deskripsi'];
    $author_id = $_SESSION['user_id']; // Ambil ID user dari sesi (BENAR)
    $file_path = null; 

    // --- 5. Logika Upload Gambar ---
    $upload_dir = 'uploads/'; 
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        // Logika nama file unik (Penting untuk keamanan)
        $file_name = uniqid('post_img_', true) . '-' . basename($_FILES['gambar']['name']);
        $target_file = $upload_dir . $file_name;

        // Cek/Buat direktori
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $file_path = $target_file; 
        } else {
            $error = "Gagal mengupload gambar. Pastikan folder 'uploads' writable.";
        }
    }

    // --- 6. Simpan ke Database (menggunakan Prepared Statement - AMAN) ---
    if (empty($error)) {
        // Asumsi nama tabel adalah upload_articles
        $stmt = $conn->prepare("INSERT INTO upload_articles (title, body, file_path, author_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $body, $file_path, $author_id);

        if ($stmt->execute()) {
            // Gunakan session message agar index.php bisa menampilkan notifikasi
            $_SESSION['message'] = "Postingan berhasil dipublikasikan!";
            header('Location: index.php');
            exit();
        } else {
            $error = "Gagal menyimpan post ke database: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posting - Web Keamanan Data</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide-icons"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #F9FAFB; } 
        /* Warna Primer: Hijau Mint/Emerald */
        .primary-color { color: #10B981; } 
        .bg-primary { background-color: #10B981; }
        .hover\:bg-primary:hover { background-color: #059669; }
        .focus\:ring-primary:focus { --tw-ring-color: #10B981; } /* Styling focus input */
    </style>
</head>
<body class="bg-gray-100">

    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="flex items-center justify-between h-16">

                <div class="flex items-center gap-4">
                    <a href="index.php" class="text-2xl font-extrabold primary-color">🛡️ Web Keamanan</a>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center space-x-3">
                        <a href="javascript:void(0)" id="edit-profile-button" class="text-sm font-semibold text-gray-700 hover:text-primary transition duration-150">
                            Hi, **<?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>** </a>
                        <a href="<?php echo BASE_URL; ?>logout.php" class="text-sm text-red-600 hover:bg-red-50 px-3 py-1 rounded-md transition duration-150">
                            Log out
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <main class="container mx-auto max-w-2xl p-4 mt-10">

        <div class="mb-6">
            <a href="index.php" class="text-gray-600 hover:text-primary font-medium flex items-center">
                <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i> Kembali ke Halaman Utama
            </a>
        </div>

        <div class="bg-white p-8 rounded-xl shadow-2xl border border-gray-100">
            <h1 class="text-3xl font-bold text-gray-800 mb-6 primary-color">Buat Postingan Baru</h1>

            <?php if ($message): ?>
                <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-lg shadow-sm mb-6" role="alert">
                    <strong class="font-bold">✅ Sukses!</strong>
                    <span class="block sm:inline">Postingan berhasil dipublikasikan.</span>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-lg shadow-sm mb-6" role="alert">
                    <strong class="font-bold">❌ Error!</strong>
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form class="space-y-6" method="post" action="post.php" enctype="multipart/form-data">
                <div>
                    <label for="judul" class="block text-sm font-semibold text-gray-700 mb-2">Judul Postingan</label>
                    <input type="text" id="judul" name="judul"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition duration-150"
                        placeholder="Masukkan judul (wajib)" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Unggah Gambar</label>
                    <label for="gambar-upload"
                        class="border-2 border-dashed border-gray-300 rounded-xl p-10 text-center cursor-pointer hover:border-primary block transition duration-150">
                        <i data-lucide="image" class="mx-auto h-12 w-12 text-gray-400"></i>
                        <p class="mt-2 text-gray-500">Drag gambarmu ke sini atau <span
                                class="primary-color font-semibold">klik untuk Upload</span></p>
                        <p id="file-name-display" class="mt-1 text-xs text-gray-400"></p>
                    </label>
                    <input type="file" id="gambar-upload" name="gambar" class="hidden" accept="image/png, image/jpeg, image/gif">
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea id="deskripsi" name="deskripsi" rows="6"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50 transition duration-150"
                        placeholder="Jelaskan postingan Anda..."></textarea>
                </div>

                <div>
                    <button type="submit"
                        class="w-full bg-primary text-white p-3 rounded-lg text-center font-bold text-lg hover:bg-green-700 transition duration-200 shadow-md">
                        <i data-lucide="send" class="w-5 h-5 inline mr-2"></i> Publikasikan Postingan
                    </button>
                </div>
            </form>
        </div>
    </main>

    <?php if (isset($_SESSION['user_id'])) include 'modal_profile.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            try {
                lucide.createIcons();
            } catch (e) {
                console.error("Lucide icons gagal dimuat:", e);
            }

            // Preview file name upload
            const fileInput = document.getElementById('gambar-upload');
            const fileNameDisplay = document.getElementById('file-name-display');
            if (fileInput && fileNameDisplay) {
                fileInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        fileNameDisplay.textContent = 'File terpilih: ' + this.files[0].name;
                        fileNameDisplay.classList.remove('text-gray-400');
                        fileNameDisplay.classList.add('primary-color');
                    } else {
                        fileNameDisplay.textContent = '';
                    }
                });
            }
            
            // Logika Modal Edit Profil
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
            // ... (Logika modal lainnya)
        });
    </script>

</body>
</html>
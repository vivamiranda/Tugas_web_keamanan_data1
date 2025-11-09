<?php
include 'koneksi.php';
$message = '';
$error = '';

$upload_dir = 'uploads/avatars/'; // Folder aman (berbeda/lebih spesifik)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    if ($_FILES['fileToUpload']['error'] == 0) {
        
        $file_name = $_FILES['fileToUpload']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // SAFE: Whitelist ekstensi yang diizinkan
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

        if (in_array($file_ext, $allowed_ext)) {
            // SAFE: Buat nama file unik
            $new_file_name = uniqid('safe_upload_', true) . '.' . $file_ext;
            $target_file = $upload_dir . $new_file_name;

            if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file)) {
                $message = "Sukses (Aman): File " . htmlspecialchars($file_name) . " berhasil di-upload sebagai " . $new_file_name;
            } else {
                $error = "Error saat memindahkan file.";
            }
        } else {
            // SAFE: Menolak ekstensi berbahaya (seperti .php)
            $error = "Invalid extension. Hanya " . implode(', ', $allowed_ext) . " yang diizinkan.";
        }
    } else {
        $error = "Error upload: " . $_FILES['fileToUpload']['error'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>File Upload - Safe</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container login-box" style="max-width: 600px;">
        <h1>File Upload (SAFE)</h1>
        <div class="alert alert-success">
            AMAN — Validasi ekstensi (whitelist) dan nama file di-randomize.
        </div>
        
        <?php if ($message) echo "<p class='safe-text'>$message</p>"; ?>
        <?php if ($error) echo "<p class='vulnerable-text'>$error</p>"; ?>

        <form method="post" enctype="multipart/form-data">
            <label for="fileToUpload" class="form-label">Pilih file (jpg, png, pdf)</label>
            <input type="file" name="fileToUpload" id="fileToUpload" class="form-control">
            <button type="submit" class="btn-main">Upload</button>
        </form>
        <p style="text-align: center;"><a href="index.php">Kembali ke Halaman Utama</a></p>
    </div>
</body>
</html>
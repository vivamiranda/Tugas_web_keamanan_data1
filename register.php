<?php
// Wajib: Sertakan koneksi database tunggal
include 'koneksi.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $fullname = $_POST['fullname'] ?? ''; 
    $mode = $_POST['logic_mode']; // Mengambil pilihan Rentan/Aman

    if ($mode === 'aman') {
        $table_name = 'sqli_users_safe';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // --- LOGIKA AMAN (SATU-SATUNYA LOGIKA) ---
        $stmt = $conn->prepare("INSERT INTO " . $table_name . " (username, password, fullname) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $fullname); 

        if ($stmt->execute()) {
            $message = "✅ Akun AMAN berhasil dibuat! Password tersimpan di-hash.";
        } else {
            $error = "Error Aman: " . $stmt->error;
        }
        $stmt->close();
        
    } else {
        $table_name = 'sqli_users_vul';
        
        // --- LOGIKA RENTAN: Concatenated Query dan Password Mentah ---
        // Kita tetap gunakan real_escape_string untuk mendemonstrasikan
        // bahwa itu saja tidak cukup untuk SQLi yang lebih kompleks
        $clean_username = $conn->real_escape_string($username);
        $clean_password = $conn->real_escape_string($password);
        $clean_fullname = $conn->real_escape_string($fullname);

        // Kueri rentan, tapi di sini hanya INSERT biasa
        $sql = "INSERT INTO " . $table_name . " (username, password, fullname) VALUES ('$clean_username', '$clean_password', '$clean_fullname')";

        if ($conn->query($sql) === TRUE) {
            $message = "⚠️ Akun RENTAN berhasil dibuat! Password tersimpan mentah.";
        } else {
            $error = "Error Rentan: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Demo Keamanan Data</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container login-box">
        <h1>CREATE USER</h1>
        <p>Buat akun untuk menguji perbedaan logika keamanan.</p>

        <?php if ($message) echo "<p class='safe-text'>$message</p>"; ?>
        <?php if ($error) echo "<p class='vulnerable-text'>$error</p>"; ?>
        
        <form method="post">
            
            <label for="logic_mode">Pilih Mode Akun:</label>
            <select name="logic_mode" id="logic_mode" required class="input-full">
                <option value="aman">Aman (Password di-Hash, ke sqli_users_safe)</option>
                <option value="rentan">Rentan (Password Mentah, ke sqli_users_vul)</option>
            </select>
            <br><br>

            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            
            <label for="fullname">Full name (opsional):</label>
            <input type="text" name="fullname" id="fullname">
            
            <button type="submit" class="btn-main">Buat Akun</button>
        </form>
        
        <p style="text-align: center;">Sudah punya akun? <a href="login.php">Masuk</a></p>
        <p style="text-align: center;"><a href="index.php">Kembali ke Halaman Utama</a></p>
    </div>
</body>
</html>
<?php
include 'koneksi.php'; // Ini sudah otomatis session_start()

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $mode = $_POST['logic_mode']; // Mengambil pilihan Rentan/Aman

    if ($mode === 'aman') {
        $table_name = 'sqli_users_safe'; // Tabel untuk user yang di-hash
        
        // --- LOGIKA AMAN: MENGGUNAKAN PREPARED STATEMENTS ---
        // MODIFIKASI: Tidak lagi mengambil profile_picture_path
        $stmt = $conn->prepare("SELECT id, username, password FROM " . $table_name . " WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verifikasi password yang di-hash
            if (password_verify($password, $user['password'])) {
                // Simpan semua data penting ke session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                // MODIFIKASI: Baris untuk session profile_picture_path DIHAPUS
                
                header('Location: index.php'); 
                exit();
            } else {
                $error = "Aman: Username atau password salah!";
            }
        } else {
            $error = "Aman: Username atau password salah!";
        }
        $stmt->close();
        
    } else {
        $table_name = 'sqli_users_vul'; // Tabel untuk user yang passwordnya mentah
        
        // --- LOGIKA RENTAN: MENGGUNAKAN CONCATENATED QUERY ---
        $sql = "SELECT id, username, password FROM " . $table_name . " WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            // MODIFIKASI: Baris untuk session profile_picture_path DIHAPUS
            
            header('Location: index.php'); 
            exit();
        } else {
            $error = "Rentan: Username atau password salah! Coba gunakan <b>' OR '1'='1' -- </b> di username atau password untuk bypass.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Web Keamanan Data</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-box">
            <div class="logo-area">
                <i class="fas fa-shield-alt logo-icon"></i>
                <h2>Sistem Demo Keamanan Data</h2>
                <p>Silakan masuk untuk melanjutkan</p>
            </div>
            
            <?php if (!empty($error)) echo "<div class='alert-error'><i class='fas fa-exclamation-circle'></i> $error</div>"; ?>

            <form method="post" class="login-form">
                
                <div class="input-group mode-select">
                    <label for="logic_mode"><i class="fas fa-handshake-angle"></i> Mode Demo</label>
                    <select name="logic_mode" id="logic_mode" required>
                        <option value="aman" <?= (isset($_POST['logic_mode']) && $_POST['logic_mode'] === 'aman') ? 'selected' : ''; ?>>🛡️ Aman (Prepared Statements)</option>
                        <option value="rentan" <?= (isset($_POST['logic_mode']) && $_POST['logic_mode'] === 'rentan') ? 'selected' : ''; ?>>⚠️ Rentan (Raw Query)</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" id="username" placeholder="Masukkan Username Anda" required>
                </div>

                <div class="input-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan Password Anda" required>
                </div>
                
                <button type="submit" class="btn-main">Masuk ke Sistem</button>
            </form>
            
            <div class="footer-links">
                <p>Belum punya akun? <a href="register.php">Daftar Akun Baru</a></p>
                <p><a href="index.php"><i class="fas fa-home"></i> Kembali ke Halaman Utama</a></p>
            </div>
        </div>
    </div>
</body>
</html>
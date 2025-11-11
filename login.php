<?php
// 🛠️ INCLUDE PATH: Menunjuk ke folder /includes/
include 'includes/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $mode = $_POST['logic_mode']; 

    if ($mode === 'aman') {
        $table_name = 'sqli_users_safe';
        
        // LOGIKA AMAN: PREPARED STATEMENTS
        $stmt = $conn->prepare("SELECT id, username, password FROM " . $table_name . " WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
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
        $table_name = 'sqli_users_vul';
        
        // LOGIKA RENTAN: CONCATENATED QUERY
        $sql = "SELECT id, username, password FROM " . $table_name . " WHERE username = '$username' AND password = '$password'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
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
    <link rel="stylesheet" href="includes/style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-outer-box">
        <div class="login-box">
            <div class="logo-area">
                <i class="fas fa-shield-alt logo-icon"></i>
                <h2>Sistem Demo Keamanan Data</h2>
                <p>Silakan masuk dan pilih mode Anda</p>
            </div>
            
            <?php if (!empty($error)) echo "<div class='alert-error'>$error</div>"; ?>

            <form method="post" class="login-form">
                
                <div class="input-group mode-select">
                    <label for="logic_mode"><i class="fas fa-handshake-angle"></i> Pilih Mode Demo:</label>
                    <select name="logic_mode" id="logic_mode" required>
                        <option value="aman" <?= (isset($_POST['logic_mode']) && $_POST['logic_mode'] === 'aman') ? 'selected' : ''; ?>>🛡️ Aman (Prepared Statements)</option>
                        <option value="rentan" <?= (isset($_POST['logic_mode']) && $_POST['logic_mode'] === 'rentan') ? 'selected' : ''; ?>>⚠️ Rentan (Raw Query)</option>
                    </select>
                </div>

                <div class="input-group">
                    <label for="username"><i class="fas fa-user"></i> Masukkan Username</label>
                    <input type="text" name="username" id="username" placeholder="Masukkan Username Anda" required>
                </div>

                <div class="input-group">
                    <label for="password"><i class="fas fa-lock"></i> Masukkan Password</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan Password Anda" required>
                </div>
                
                <button type="submit" class="btn-main">Masuk</button>
            </form>
            
            <div class="footer-links">
                <p>Belum punya akun? <a href="register.php">Daftar Akun </a></p>
                <p><a href="index.php">Kembali ke Halaman Utama</a></p>
            </div>
        </div>
    </div>
</body>
</html>
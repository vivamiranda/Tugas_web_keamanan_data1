<?php
if (!isset($conn)) {
    include 'koneksi.php'; 
}

if (!isset($_SESSION['user_id'])) {
    return; 
}

$user_id = $_SESSION['user_id'];

// Ambil data user saat ini dari database (SAFE: Prepared Statement)
$stmt_data = $conn->prepare("SELECT username, fullname FROM sqli_users_safe WHERE id = ?");
$stmt_data->bind_param("i", $user_id);
$stmt_data->execute();
$result_data = $stmt_data->get_result();
$current_user_data = $result_data->fetch_assoc();
$stmt_data->close();

if (!$current_user_data) {
    return;
}

$current_username = htmlspecialchars($current_user_data['username']);
$current_fullname = htmlspecialchars($current_user_data['fullname']);

$modal_error = $_SESSION['modal_error'] ?? null;
$modal_success = $_SESSION['modal_success'] ?? null;
unset($_SESSION['modal_error']);
unset($_SESSION['modal_success']);
?>

<div id="profile-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-[100]">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-xl font-bold text-green-600">Edit Profil</h3>
            <button id="close-modal-button" class="text-gray-500 hover:text-gray-800">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <?php if ($modal_success): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded-md"><?php echo $modal_success; ?></div>
        <?php endif; ?>
        <?php if ($modal_error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded-md"><?php echo $modal_error; ?></div>
        <?php endif; ?>

        <form action="update_profile.php" method="POST" class="space-y-4">
            
            <div>
                <label for="fullname" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="fullname" id="fullname" value="<?php echo $current_fullname; ?>" required
                       class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-green-600 focus:ring-1 focus:border-green-600">
            </div>

            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" value="<?php echo $current_username; ?>" required
                       class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-green-600 focus:ring-1 focus:border-green-600">
            </div>

            <h4 class="font-semibold pt-2">Ubah Password (Opsional)</h4>

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini (Wajib)</label>
                <input type="password" name="current_password" id="current_password" required
                       class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-green-600 focus:ring-1 focus:border-green-600">
            </div>

            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                <input type="password" name="new_password" id="new_password"
                       class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-green-600 focus:ring-1 focus:border-green-600">
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" id="confirm_password"
                       class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-green-600 focus:ring-1 focus:border-green-600">
            </div>

            <button type="submit" class="w-full bg-green-600 text-white p-2 rounded-lg font-semibold hover:bg-green-700 transition duration-150 mt-4">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
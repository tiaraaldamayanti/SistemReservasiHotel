<?php
session_start();
require_once '../admin/includes/db.php';  // Menghubungkan ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        // Ambil id, username, dan password
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($admin_id, $admin_username, $hashed_password);

        if ($stmt->fetch()) {
            if (password_verify($password, $hashed_password)) {
                $_SESSION['admin_id'] = $admin_id;
                $_SESSION['admin_username'] = $admin_username;
                $_SESSION['login_success'] = "Selamat datang, $admin_username! Anda berhasil login.";
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Username atau password salah.";
            }
        } else {
            $error = "Username atau password salah.";
        }

        $stmt->close();
    } else {
        $error = "Harap isi semua bidang.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded-lg shadow-xl w-96">
        <h2 class="text-3xl font-semibold text-center mb-4">Login Admin</h2>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-2 mb-4 rounded"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Form login -->
        <form method="POST">
            <label class="block mb-2">Username</label>
            <input type="text" name="username" class="w-full p-3 border rounded mb-4" required>

            <label class="block mb-2">Password</label>
            <input type="password" name="password" class="w-full p-3 border rounded mb-4" required>

            <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded hover:bg-indigo-700 transition">Login</button>
        </form>
    </div>
</body>
</html>

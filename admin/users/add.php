<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';  // Validasi login admin

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Validasi input
    if ($username && $email && $phone && $password) {
        // Insert pengguna baru ke database
        $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $phone, $hashed_password);

        if ($stmt->execute()) {
            header("Location: list.php"); // Redirect ke daftar pengguna
            exit;
        } else {
            $error = "Gagal menambahkan pengguna.";
        }

        $stmt->close();
    } else {
        $error = "Semua bidang harus diisi.";
    }
}

?>

<div class="p-6">
    <h1 class="text-2xl font-semibold mb-4">Tambah Pengguna Baru</h1>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 text-red-700 p-2 mb-4 rounded"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-4">
            <label class="block">Username</label>
            <input type="text" name="username" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label class="block">Email</label>
            <input type="email" name="email" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label class="block">Phone</label>
            <input type="text" name="phone" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label class="block">Password</label>
            <input type="password" name="password" class="w-full p-2 border rounded" required>
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded">Tambah Pengguna</button>
    </form>
</div>

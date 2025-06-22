<?php
require_once '../admin/includes/db.php';  // Menghubungkan ke database

// Ambil semua admin dari database
$users = $conn->query("SELECT id, password FROM admins");

while ($user = $users->fetch_assoc()) {
    // Hash password menggunakan password_hash
    $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);  
    $id = $user['id'];

    // Update password yang sudah di-hash ke database
    $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $id);
    $stmt->execute();
}

echo "Password berhasil diubah menjadi hash!";
?>

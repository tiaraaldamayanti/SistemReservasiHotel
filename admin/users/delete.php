<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';  // Validasi login admin

$id = $_GET['id'] ?? '';
if (!$id) {
    die("Pengguna tidak ditemukan.");
}

// Menghapus pengguna berdasarkan ID
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    $_SESSION['success'] = "Pengguna berhasil dihapus."; // ✅ Tambahkan notifikasi ke session
    header("Location: list.php"); // Redirect ke list.php
    exit;
} else {
    echo "<div class='bg-red-100 text-red-700 p-2 mb-4 rounded'>Gagal menghapus pengguna.</div>";
}

$stmt->close();
?>

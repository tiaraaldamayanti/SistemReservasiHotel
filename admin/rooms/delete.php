<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';  // Validasi login admin

$id = $_GET['id'] ?? '';
if (!$id) {
    die("Kamar tidak ditemukan.");
}

// Menghapus kamar berdasarkan ID
$stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    $_SESSION['success'] = "kamar berhasil dihapus."; // ✅ Tambahkan notifikasi ke session
    header("Location: list.php"); // Redirect ke daftar kamar setelah dihapus
    exit;
} else {
    echo "<div class='bg-red-100 text-red-700 p-2 mb-4 rounded'>Gagal menghapus kamar.</div>";
}

$stmt->close();
?>

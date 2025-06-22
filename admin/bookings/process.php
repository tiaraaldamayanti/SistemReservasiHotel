<?php
require_once '../includes/auth.php';  // Validasi login admin

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil ID dan aksi dari form
    $id = intval($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id && in_array($action, ['accept', 'reject'])) {
        $status = $action === 'accept' ? 'accepted' : 'rejected';

        // Update status pemesanan
        $stmt = $conn->prepare("UPDATE payments SET stat = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
    }
}

header("Location: list.php");  // Arahkan kembali ke daftar pemesanan
exit;

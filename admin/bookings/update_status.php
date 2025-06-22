<?php
session_start();
require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/db.php';

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if (!$id || !$action) {
    header("Location: list.php");
    exit;
}

if ($action === 'accept') {
    $stmt = $conn->prepare("UPDATE payments SET stat = 'accepted' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['accept_success'] = 'Pemesanan berhasil diterima.';
} elseif ($action === 'reject') {
    // Ambil data dari payments
    $stmt1 = $conn->prepare("SELECT user_id, room_type, checkin, checkout FROM payments WHERE id = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();
    $stmt1->bind_result($user_id, $roomtype, $checkin, $checkout);
    $stmt1->fetch();
    $stmt1->close();

    // Hapus dari bookings jika ada
    $stmt2 = $conn->prepare("DELETE FROM bookings WHERE user_id = ? AND roomtype = ? AND checkin_date = ? AND checkout_date = ?");
    $stmt2->bind_param("isss", $user_id, $roomtype, $checkin, $checkout);
    $stmt2->execute();
    $stmt2->close();

    // Update status di payments
    $stmt3 = $conn->prepare("UPDATE payments SET stat = 'rejected' WHERE id = ?");
    $stmt3->bind_param("i", $id);
    $stmt3->execute();
    $stmt3->close();

    $_SESSION['reject_success'] = 'Pemesanan berhasil ditolak.';
}

header("Location: list.php");
exit;

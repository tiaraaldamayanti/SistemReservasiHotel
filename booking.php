<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $roomtype = $_POST['roomtype'];
    $price = $_POST['price'];

    // Simpan ke tabel pembayaran atau booking
    $stmt = $conn->prepare("INSERT INTO bookings (roomtype, checkin_date, checkout_date, price, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->bind_param("sssd", $roomtype, $checkin, $checkout, $price);

    if ($stmt->execute()) {
        echo "<script>alert('Booking berhasil! Silakan lanjutkan ke pembayaran.'); window.location.href='reservation.php';</script>";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }
}
?>

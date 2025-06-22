<?php
require_once 'db.php';  // Menyertakan file koneksi database

if (isset($_POST['send_complaint'])) {
    // Ambil data dari form
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $created_at = date('Y-m-d H:i:s');

    // Masukkan data ke tabel
    $query = "INSERT INTO complaints (full_name, phone_number, email, message, created_at) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $fullname, $phone, $email, $message, $created_at);

    if ($stmt->execute()) {
        // ✅ Redirect ke dashboard.php dengan notifikasi sukses
        header("Location: dashboard.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

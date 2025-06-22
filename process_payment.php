<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f2f2f2] min-h-screen flex items-center justify-center p-6 text-gray-800">

<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "hotel1";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-lg w-full'>
            <strong class='font-bold'>Koneksi Gagal:</strong> <span class='block sm:inline'>" . $conn->connect_error . "</span>
        </div>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $fullname = $_POST['fullname'] ?? '';
    $room_type = $_POST['room_type'] ?? null;
    $duration = $_POST['duration'] ?? 0;
    $subtotal = $_POST['subtotal'] ?? 0;
    $voucher_sale = $_POST['voucher_sale'] ?? 0;
    $tax = $_POST['tax'] ?? 0;
    $total_amount = $_POST['total_amount'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? null;
    $checkin = $_POST['checkin'] ?? null;
    $checkout = $_POST['checkout'] ?? null;

    if (!$checkin || !$checkout) {
        echo "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded max-w-lg w-full'>
                <strong class='font-bold'>Data tidak lengkap:</strong> <span class='block sm:inline'>Check-in atau check-out tidak ditemukan.</span>
              </div>";
        exit;
    }

    $conn->begin_transaction();

    try {
        $sql_payment = "INSERT INTO payments 
                        (user_id, fullname, room_type, duration, subtotal, voucher_sale, tax, total_amount, payment_method, checkin, checkout, stat) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'accepted')";

        $stmt = $conn->prepare($sql_payment);
        if (!$stmt) {
            throw new Exception("Kesalahan SQL: " . $conn->error);
        }

        $stmt->bind_param("issidddssss", 
            $user_id, 
            $fullname,
            $room_type, 
            $duration, 
            $subtotal, 
            $voucher_sale, 
            $tax, 
            $total_amount, 
            $payment_method,
            $checkin,
            $checkout
        );

        if (!$stmt->execute()) {
            throw new Exception("Gagal menyimpan data pembayaran: " . $stmt->error);
        }

        $payment_id = $stmt->insert_id;

        $sql_booking = "INSERT INTO bookings (roomtype, checkin_date, checkout_date, price, status, user_id) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_booking = $conn->prepare($sql_booking);
        if (!$stmt_booking) {
            throw new Exception("Kesalahan SQL pada bookings: " . $conn->error);
        }

        $status = "Booked";
        $stmt_booking->bind_param("sssssi", $room_type, $checkin, $checkout, $total_amount, $status, $user_id);
        if (!$stmt_booking->execute()) {
            throw new Exception("Gagal menyimpan data pemesanan: " . $stmt_booking->error);
        }

        // Commit transaksi jika semuanya berhasil
        $conn->commit();

        echo "<div class='bg-white max-w-xl w-full rounded-2xl shadow-lg p-8 text-center'>
                <div class='text-green-600 text-4xl mb-4'>✅</div>
                <h2 class='text-2xl font-bold text-gray-800 mb-4'>Payment Successful!</h2>
                <p class='text-gray-600 mb-6'>Thank You, <span class='font-semibold text-gray-800'>" . htmlspecialchars($fullname) . "</span>, have made a payment.</p>
                
                <div class='grid grid-cols-2 gap-y-3 text-sm text-left px-12'>
                    <p class='font-semibold text-gray-600'>Payment Method</p>
                    <p class='text-gray-800'>: " . htmlspecialchars($payment_method) . "</p>

                    <p class='font-semibold text-gray-600'>Length of Stay</p>
                    <p class='text-gray-800'>: " . intval($duration) . " malam</p>

                    <p class='font-semibold text-gray-600'>Check-in</p>
                    <p class='text-gray-800'>: " . htmlspecialchars($checkin) . "</p>

                    <p class='font-semibold text-gray-600'>Check-out</p>
                    <p class='text-gray-800'>: " . htmlspecialchars($checkout) . "</p>

                    <p class='font-semibold text-gray-600'>Total Paid</p>
                    <p class='text-green-600 font-bold'>: Rp " . number_format($total_amount, 0, ',', '.') . "</p>
                </div>

                <a href='dashboard.php' class='mt-8 inline-block bg-[#0b1a4a] text-white px-6 py-2 rounded-full hover:opacity-90 transition'>
                    Back To Home Page
                </a>
                <a href='print.php?pid=" . $payment_id . "' target='_blank' class='mt-4 ml-4 inline-block bg-green-600 text-white px-6 py-2 rounded-full hover:opacity-90 transition'>
                    Invoice Details
                </a>
              </div>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-lg w-full'>
                <strong class='font-bold'>Terjadi kesalahan:</strong> <span class='block sm:inline'>" . $e->getMessage() . "</span>
              </div>";
    }

    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($stmt_booking)) {
        $stmt_booking->close();
    }
}

$conn->close();
?>

</body>
</html>

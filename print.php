<?php
include 'db.php';
ob_start();

$pid = $_GET['pid'] ?? null;

if (!$pid) {
    echo "<h2>ID transaksi tidak ditemukan.</h2>";
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM payments WHERE id = '$pid' LIMIT 1");

if (mysqli_num_rows($result) === 0) {
    echo "<h2>ID transaksi tidak ditemukan.</h2>";
    exit;
}

$row = mysqli_fetch_assoc($result);

$fullname = htmlspecialchars($row['fullname']);
$roomType = htmlspecialchars($row['room_type']);
$duration = $row['duration'];
$method = $row['payment_method'];
$tanggal = date('d M Y', strtotime($row['created_at']));
$checkin = date('d M Y', strtotime($row['checkin']));
$checkout = date('d M Y', strtotime($row['checkout']));
$subtotal = $row['subtotal'];
$voucher = $row['voucher_sale'];
$tax = $row['tax'];
$total = $row['total_amount'];

// Ambil harga kamar dari tabel rooms
$roomQuery = mysqli_query($conn, "SELECT price FROM rooms WHERE room_type = '$roomType' LIMIT 1");
$pricePerNight = 0;

if ($roomRow = mysqli_fetch_assoc($roomQuery)) {
    $pricePerNight = $roomRow['price'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Invoice #<?= $row['id'] ?></title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 40px;
      color: #333;
    }

    .invoice-box {
      max-width: 800px;
      margin: auto;
      background: #fff;
      border-radius: 10px;
      padding: 40px;
      box-shadow: 0 0 20px rgba(0,0,0,0.08);
    }

    .hotel-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 30px;
    }

    .hotel-logo img {
      height: 80px;
    }

    .hotel-info {
      text-align: right;
    }

    .invoice-title {
      font-size: 26px;
      font-weight: bold;
      margin-bottom: 10px;
      color: #008080;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table th, table td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }

    .text-right {
      text-align: right;
    }

    .section-title {
      margin-top: 30px;
      font-weight: bold;
      color: #008080;
    }

    .summary td {
      border: none;
      padding: 6px 10px;
    }

    .summary tr.total td {
      font-weight: bold;
      font-size: 16px;
      color: #008080;
    }

    .footer {
      text-align: center;
      margin-top: 50px;
      font-size: 12px;
      color: #777;
    }

    @media print {
      body {
        background: none;
      }
      .no-print {
        display: none;
      }
    }

    .no-print {
      margin-bottom: 20px;
      text-align: center;
    }

    .no-print button {
      padding: 10px 20px;
      background: #008080;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .no-print button:hover {
      background: #006666;
    }
  </style>
</head>
<body>

<div class="no-print">
  <button onclick="window.print()">🖨️ Cetak Invoice</button>
</div>

<div class="invoice-box">
  <div class="hotel-header">
    <div class="hotel-logo">
      <img src="uploads/logo.png" alt="Hotel Logo" onerror="this.style.display='none';">
    </div>
    <div class="hotel-info">
      <div class="invoice-title">INVOICE PEMBAYARAN</div>
      <div>ID Transaksi: <strong>#<?= $row['id'] ?></strong></div>
      <div>Tanggal Transaksi: <?= $tanggal ?></div>
    </div>
  </div>

  <div class="section-title">Data Tamu</div>
  <table>
    <tr><td>Nama Tamu</td><td><?= $fullname ?></td></tr>
    <tr><td>Tipe Kamar</td><td><?= $roomType ?></td></tr>
    <tr><td>Durasi</td><td><?= $duration ?> malam</td></tr>
    <tr><td>Check-In</td><td><?= $checkin ?></td></tr>
    <tr><td>Check-Out</td><td><?= $checkout ?></td></tr>
    <tr><td>Metode Pembayaran</td><td><?= $method ?></td></tr>
  </table>

  <div class="section-title">Rincian Pembayaran</div>
  <table class="summary">
    <tr><td>Harga per Malam</td><td class="text-right">Rp <?= number_format($pricePerNight, 0, ',', '.') ?></td></tr>
    <tr><td>Subtotal (<?= $duration ?> malam)</td><td class="text-right">Rp <?= number_format($subtotal, 0, ',', '.') ?></td></tr>
    <tr><td>Diskon Voucher</td><td class="text-right">- Rp <?= number_format($voucher, 0, ',', '.') ?></td></tr>
    <tr><td>PPN 10%</td><td class="text-right">Rp <?= number_format($tax, 0, ',', '.') ?></td></tr>
    <tr class="total"><td>Total Pembayaran</td><td class="text-right">Rp <?= number_format($total, 0, ',', '.') ?></td></tr>
  </table>

  <div class="footer">
    Terima kasih telah menginap di <strong>Sedadul Hotel</strong><br>
    Jl. Terusan Jenderal Sudirman, Cimahi - Jawa Barat 40511<br>
    Email: info@sedadul.com | Telp: (+62) 832-1231-8493
  </div>
</div>

</body>
</html>
<?php ob_end_flush(); ?>

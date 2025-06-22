<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM payments WHERE user_id = $user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Riwayat Pemesanan</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f2f5;
      margin: 0;
      padding: 0;
    }
    .header {
      background: #fff;
      padding: 20px;
      text-align: center;
      font-size: 22px;
      font-weight: bold;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      position: relative;
    }
    .menu-icon {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 24px;
      cursor: pointer;
    }
    .sidebar {
      height: 100%;
      width: 250px;
      position: fixed;
      top: 0;
      left: -250px;
      background-color: #008080;
      overflow-x: hidden;
      transition: 0.3s;
      padding-top: 60px;
      z-index: 999;
    }
    .sidebar a {
      padding: 12px 24px;
      text-decoration: none;
      font-size: 18px;
      color: white;
      display: block;
      transition: 0.3s;
    }
    .sidebar a:hover {
      background-color: #006666;
    }
    .closebtn {
      position: absolute;
      top: 10px;
      right: 20px;
      font-size: 30px;
      color: white;
      cursor: pointer;
    }
    .container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }
    .grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
    }
    .card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
      overflow: hidden;
      width: calc(50% - 20px);
      display: flex;
      flex-direction: column;
      transition: transform 0.2s ease;
    }
    .card:hover {
      transform: translateY(-4px);
    }
    .room-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }
    .card-body {
      padding: 20px;
    }
    .info {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      font-size: 14px;
    }
    .info-item {
      flex: 1 1 45%;
    }
    .info-item p {
      margin: 6px 0;
      display: flex;
      align-items: center;
      gap: 8px;
      color: #333;
    }
    .info-item i {
      color: #008080;
      font-size: 16px;
    }
    .buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
      gap: 10px;
      flex-wrap: wrap;
    }
    .buttons a, .buttons span {
      background: #008080;
      color: #fff;
      padding: 10px 14px;
      text-decoration: none;
      border-radius: 8px;
      font-size: 14px;
      transition: background 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .buttons a:hover {
      background: #006666;
    }
    .buttons span.disabled {
      background: #ccc;
      cursor: not-allowed;
    }
    .status {
      margin-top: 10px;
      font-weight: bold;
      display: inline-block;
    }
    .status.accepted { color: #28a745; }
    .status.rejected { color: #dc3545; }
    .status.pending { color: #ffc107; }
    @media (max-width: 768px) {
      .card {
        width: 100%;
      }
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div id="mySidebar" class="sidebar">
  <a href="javascript:void(0)" class="closebtn" onclick="closeSidebar()">&times;</a>
  <a href="dashboard.php"><i class="fas fa-home"></i> Beranda</a>
  <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
  <a href="riwayat_transaksi.php"><i class="fas fa-history"></i> Riwayat</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Header -->
<div class="header">
  <span class="menu-icon" onclick="openSidebar()"><i class="fas fa-bars"></i></span>
  Riwayat Pemesanan
</div>

<!-- Konten Utama -->
<div class="container">
  <div class="grid">
    <?php if (mysqli_num_rows($result) === 0): ?>
      <p style="text-align:center; width:100%; color: #777;">Belum ada transaksi yang ditemukan.</p>
    <?php else:
      while ($row = mysqli_fetch_assoc($result)):
        $checkin = new DateTime($row['checkin']);
        $checkout = new DateTime($row['checkout']);
        $now = new DateTime();

        if ($checkout < $now) {
            $checkinBaru = (new DateTime('tomorrow'));
            $checkoutBaru = (clone $checkinBaru)->modify('+2 day');
        } else {
            $checkinBaru = $checkin;
            $checkoutBaru = $checkout;
        }

        $roomType = mysqli_real_escape_string($conn, $row['room_type']);
        $roomImage = 'uploads/default.jpg';

        $roomQuery = "SELECT image FROM rooms WHERE room_type = '$roomType' LIMIT 1";
        $roomResult = mysqli_query($conn, $roomQuery);
        if ($roomRow = mysqli_fetch_assoc($roomResult)) {
            $roomImage = 'uploads/' . $roomRow['image'];
        }

        $status = strtolower($row['stat'] ?? 'pending');
    ?>
    <div class="card">
      <img src="<?= $roomImage ?>" class="room-image" alt="Room" onerror="this.src='uploads/default.jpg';">
      <div class="card-body">
        <div class="info">
          <div class="info-item">
            <p><i class="fas fa-bed"></i> Tipe Kamar: <?= htmlspecialchars($row['room_type']) ?></p>
            <p><i class="fas fa-calendar-check"></i> Check In: <?= $checkin->format('d M Y') ?></p>
            <p><i class="fas fa-calendar-check"></i> Check Out: <?= $checkout->format('d M Y') ?></p>
          </div>
          <div class="info-item">
            <p><i class="fas fa-money-check-alt"></i> Metode Bayar: <?= $row['payment_method'] ?></p>
            <p><i class="fas fa-clock"></i> Tanggal: <?= date('d M Y', strtotime($row['created_at'])) ?></p>
            <p><i class="fas fa-money-bill-wave"></i> Total: Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></p>
          </div>
        </div>

        <!-- Status -->
        <p class="status <?= $status ?>"><i class="fas fa-info-circle"></i> Status: <?= ucfirst($status) ?></p>

        <div class="buttons">
          <?php if ($status !== 'rejected'): ?>
            <a href="print.php?pid=<?= $row['id'] ?>"><i class="fas fa-file-download"></i> Bukti Bayar</a>
          <?php else: ?>
            <span class="disabled"><i class="fas fa-ban"></i> Tidak Tersedia</span>
          <?php endif; ?>

          <a href="reservasi.php?roomtype=<?= urlencode($row['room_type']) ?>&checkin=<?= $checkinBaru->format('Y-m-d') ?>&checkout=<?= $checkoutBaru->format('Y-m-d') ?>&price=<?= $row['total_amount'] ?>">
            <i class="fas fa-plus-circle"></i> Pesan Lagi
          </a>
        </div>
      </div>
    </div>
    <?php endwhile; endif; ?>
  </div>
</div>

<!-- JS Sidebar -->
<script>
function openSidebar() {
  document.getElementById("mySidebar").style.left = "0";
}
function closeSidebar() {
  document.getElementById("mySidebar").style.left = "-250px";
}
</script>

</body>
</html>

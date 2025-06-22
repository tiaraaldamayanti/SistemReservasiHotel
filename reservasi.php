<?php 
include 'db.php';

$today = date('Y-m-d');
$successMessage = '';
if (isset($_GET['success']) && $_GET['success'] === '1') {
    $successMessage = "Booking berhasil! Silakan lanjut ke halaman pembayaran.";
}

$checkin = $_POST['checkin'] ?? '';
$checkout = $_POST['checkout'] ?? '';
$roomtype = $_POST['roomtype'] ?? '';
$price = 0;
$error = '';

$roomDataResult = mysqli_query($conn, "SELECT * FROM rooms ORDER BY position ASC");
$rooms = [];
if ($roomDataResult && mysqli_num_rows($roomDataResult) > 0) {
    while ($row = mysqli_fetch_assoc($roomDataResult)) {
        $rooms[$row['room_type']] = $row;
    }
}

function isRoomAvailable($conn, $roomtype, $checkin, $checkout) {
    $stmt = $conn->prepare("SELECT total_rooms FROM rooms WHERE room_type = ?");
    $stmt->bind_param("s", $roomtype);
    $stmt->execute();
    $stmt->bind_result($total_rooms);
    $stmt->fetch();
    $stmt->close();

    if (!$total_rooms) return false;

    $stmt = $conn->prepare("
        SELECT COUNT(*) FROM bookings 
        WHERE roomtype = ? 
        AND (
            (checkin_date < ? AND checkout_date > ?) OR
            (checkin_date >= ? AND checkin_date < ?)
        )
    ");
    $stmt->bind_param("sssss", $roomtype, $checkout, $checkin, $checkin, $checkout);
    $stmt->execute();
    $stmt->bind_result($booked_rooms);
    $stmt->fetch();
    $stmt->close();

    return $booked_rooms < $total_rooms;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($checkin < $today || $checkout < $today) {
        $error = "Tanggal check-in dan check-out tidak boleh di masa lalu!";
    } elseif ($checkin >= $checkout) {
        $error = "Tanggal check-out harus setelah check-in!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Reservasi - SESADUL HOTEL</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f8f8f8;
    }

    .hero-section {
      background: url('uploads/dashboard.jpg') no-repeat center center/cover;
      color: white;
      text-align: center;
      padding: 150px 20px 80px;
    }

    .hero-section h1 {
      font-size: 48px;
      font-weight: bold;
      color: #F7E7CE;
    }

    .hero-section p {
      color: #EEDDC3;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .search-box {
      background: white;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      max-width: 1000px;
      margin: -40px auto 40px;
    }

    .search-item {
      display: flex;
      flex-direction: column;
      min-width: 200px;
    }

    .search-item label {
      font-weight: bold;
      color: #008080;
    }

    .search-item input,
    .search-item select {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 16px;
    }

    .btn-find {
      background: #008080;
      color: white;
      border: none;
      padding: 15px 30px;
      font-size: 16px;
      border-radius: 30px;
      cursor: pointer;
      align-self: flex-end;
      transition: all 0.3s ease-in-out;
    }

    .btn-find:hover {
      background-color: #006666;
      transform: scale(1.05);
    }

    .available-rooms {
      background: #f2f2f2;
      padding: 60px 20px;
      text-align: center;
    }

    .bed-options {
      display: flex;
      gap: 20px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .bed-card {
      background: white;
      border-radius: 15px;
      padding: 20px;
      width: 280px;
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      opacity: 0;
      transform: translateY(40px) scale(0.95);
      animation: fadeInUp 0.6s ease forwards;
      transition: transform 0.4s ease, box-shadow 0.4s ease, background 0.4s ease;
      position: relative;
      z-index: 1;
    }

    .bed-card:hover {
      transform: translateY(-12px) scale(1.03);
      background: linear-gradient(135deg, #e6f9f9, #f0fdfa);
      box-shadow: 0 16px 30px rgba(0, 128, 128, 0.3);
      z-index: 2;
    }

    .bed-card:nth-child(1) { animation-delay: 0.1s; }
    .bed-card:nth-child(2) { animation-delay: 0.2s; }
    .bed-card:nth-child(3) { animation-delay: 0.3s; }
    .bed-card:nth-child(4) { animation-delay: 0.4s; }
    .bed-card:nth-child(5) { animation-delay: 0.5s; }
    .bed-card:nth-child(6) { animation-delay: 0.6s; }

    .bed-card img {
      width: 100%;
      border-radius: 10px;
      margin-bottom: 10px;
      height: 180px; 
      object-fit: cover;
    }

    .bed-card h3 {
      color: #008080;
    }

    .bed-card p {
      flex-grow: 1;
    }

    .bed-card .btn-find {
      width: 100%;
      margin-top: 15px;
      box-sizing: border-box;
      border-radius: 10px;
    }

    .error {
      color: red;
      text-align: center;
    }

    .back-link {
      margin-top: 30px;
    }

    .back-link a {
      color: #008080;
      text-decoration: underline;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @keyframes scaleIn {
      from {
        opacity: 0;
        transform: scale(0.95);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }

    .success-alert {
      animation: scaleIn 0.5s ease forwards;
    }
  </style>
</head>
<body>

  <section class="hero-section">
    <h1>SESADUL HOTEL</h1>
    <p>STAY IN COMFORT, STAY WITH US</p>
  </section>

  <?php if ($successMessage): ?>
    <div class="success-alert" style="text-align: center; background: #d4edda; color: #155724; padding: 10px; border-radius: 10px; max-width: 600px; margin: 20px auto;">
      <?= htmlspecialchars($successMessage) ?>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="search-box">
      <div class="search-item">
        <label for="checkin">Check-in date:</label>
        <input type="date" name="checkin" id="checkin" min="<?= $today ?>" value="<?= htmlspecialchars($checkin) ?>">
      </div>
      <div class="search-item">
        <label for="checkout">Check-out date:</label>
        <input type="date" name="checkout" id="checkout" min="<?= $today ?>" value="<?= htmlspecialchars($checkout) ?>">
      </div>
      <div class="search-item">
        <label for="roomtype">Room type:</label>
        <select name="roomtype" id="roomtype">
          <?php foreach ($rooms as $type => $info): ?>
            <option value="<?= htmlspecialchars($type) ?>" <?= $roomtype === $type ? 'selected' : '' ?>><?= htmlspecialchars($type) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn-find">Find Deals</button>
    </div>
  </form>

  <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
    <section class="available-rooms">
      <h2>Room List</h2>
      <div class="bed-options">
        <?php foreach ($rooms as $room): ?>
          <div class="bed-card">
            <img src="uploads/<?= htmlspecialchars($room['image']) ?>" alt="<?= htmlspecialchars($room['room_type']) ?>">
            <h3><?= htmlspecialchars($room['room_type']) ?></h3>
            <p><?= htmlspecialchars($room['description']) ?></p>
            <p><strong>Harga:</strong> Rp <?= number_format($room['price'], 0, ',', '.') ?> / night</p>
            <form method="POST" action="reservasi.php" style="margin-top: auto;">
              <input type="hidden" name="checkin" value="<?= $today ?>">
              <input type="hidden" name="checkout" value="<?= date('Y-m-d', strtotime($today . ' +1 day')) ?>">
              <input type="hidden" name="roomtype" value="<?= htmlspecialchars($room['room_type']) ?>">
              <button type="submit" class="btn-find">Booking Now</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error): ?>
    <?php
      $roomInfo = $rooms[$roomtype] ?? null;
      if ($roomInfo) {
          $desc = $roomInfo['description'];
          $price = $roomInfo['price'];
          $image = $roomInfo['image'];
          $available = isRoomAvailable($conn, $roomtype, $checkin, $checkout);
      }
    ?>
    <section class="available-rooms">
      <h2>Available Room - <?= htmlspecialchars($roomtype) ?></h2>
      <div class="bed-options">
        <div class="bed-card">
          <img src="uploads/<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($roomtype) ?>">
          <h3><?= htmlspecialchars($roomtype) ?></h3>
          <p><?= htmlspecialchars($desc) ?></p>
          <p><strong>Check-in:</strong> <?= htmlspecialchars($checkin) ?></p>
          <p><strong>Check-out:</strong> <?= htmlspecialchars($checkout) ?></p>
          <p><strong>Harga:</strong> Rp <?= number_format($price, 0, ',', '.') ?> / night</p>
          <?php if ($available): ?>
            <form method="POST" action="reservation.php" style="margin-top: auto;">
              <input type="hidden" name="roomtype" value="<?= htmlspecialchars($roomtype) ?>">
              <input type="hidden" name="checkin" value="<?= htmlspecialchars($checkin) ?>">
              <input type="hidden" name="checkout" value="<?= htmlspecialchars($checkout) ?>">
              <input type="hidden" name="price" value="<?= $price ?>">
              <button type="submit" class="btn-find">Booking Now</button>
            </form>
          <?php else: ?>
            <button class="btn-find" style="background: #ccc; cursor: not-allowed;" disabled>Full</button>
          <?php endif; ?>
        </div>
      </div>
      <div class="back-link">
        <p><a href="dashboard.php">← Back To Home Page</a></p>
      </div>
    </section>
  <?php endif; ?>

</body>
</html>

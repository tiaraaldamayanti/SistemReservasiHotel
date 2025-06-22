<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();
$fullname = $user['username'] ?? '';

$tipe_kamar = $_POST['roomtype'] ?? '';
$checkin = $_POST['checkin'] ?? '';
$checkout = $_POST['checkout'] ?? '';
$harga = $_POST['price'] ?? 0;
$harga = (float)$harga;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Reservasi Kamar</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .modal-bg {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 50;
    }

    .modal-box {
      background: white;
      padding: 2rem;
      border-radius: 1rem;
      text-align: center;
      max-width: 90%;
      width: 350px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    }

    .modal-box h3 {
      font-size: 18px;
      margin-bottom: 1rem;
    }

    .modal-box .btn {
      padding: 0.5rem 1rem;
      border-radius: 8px;
      margin: 0 0.5rem;
      font-weight: bold;
      cursor: pointer;
    }

    .btn-confirm {
      background-color: #e53e3e;
      color: white;
    }

    .btn-cancel {
      background-color: #cbd5e0;
      color: #2d3748;
    }

    .btn-confirm:hover {
      background-color: #c53030;
    }

    .btn-cancel:hover {
      background-color: #a0aec0;
    }
  </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
    <h2 class="text-2xl font-semibold text-center mb-6">Reservation Form</h2>

    <form action="payment.php" method="POST" class="space-y-4">
      <!-- Full Name -->
      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">Full Name</label>
        <input name="fullname" type="text" value="<?= htmlspecialchars($fullname) ?>" readonly
               class="w-full px-4 py-2 border rounded-md bg-gray-100 cursor-not-allowed" />
      </div>

      <!-- Tipe Kamar -->
      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">Room Type</label>
        <input name="room_type" type="text" value="<?= htmlspecialchars($tipe_kamar) ?>" readonly
               class="w-full px-4 py-2 border rounded-md bg-gray-100 cursor-not-allowed" />
      </div>

      <!-- Tanggal Checkin -->
      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">Check-in Date</label>
        <input name="checkin" type="date" value="<?= htmlspecialchars($checkin) ?>" readonly
               class="w-full px-4 py-2 border rounded-md bg-gray-100 cursor-not-allowed" />
      </div>

      <!-- Tanggal Checkout -->
      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">Check-out Date</label>
        <input name="checkout" type="date" value="<?= htmlspecialchars($checkout) ?>" readonly
               class="w-full px-4 py-2 border rounded-md bg-gray-100 cursor-not-allowed" />
      </div>

      <!-- Harga per Malam (Tampil) -->
      <div>
        <label class="block mb-1 text-sm font-medium text-gray-700">Room Price</label>
        <input type="text" value="Rp <?= number_format($harga, 0, ',', '.') ?>" readonly
               class="w-full px-4 py-2 border rounded-md bg-gray-100 cursor-not-allowed" />
      </div>

      <!-- Harga per Malam (Hidden untuk dikirim) -->
      <input type="hidden" name="price" value="<?= $harga ?>" />

      <!-- Terms Checkbox -->
      <div class="flex items-start">
        <input type="checkbox" required class="mt-1 mr-2" id="terms" />
        <label for="terms" class="text-sm">I agree With <a href="#" class="text-black underline">terms and conditions</a></label>
      </div>

      <!-- Submit Button -->
      <button type="submit" name="submit"
              class="w-full bg-[#008080] text-white py-2 rounded-md hover:bg-[#006666] transition">
          Continue the payment
      </button>

      <!-- Tombol Batalkan -->
      <a href="reservasi.php"
        class="block text-center w-full bg-red-500 text-white py-2 rounded-md hover:bg-red-600 transition">
        Cancel
      </a>
    </form>

    <p class="text-center text-xs text-gray-500 mt-6">© SESADUL 2025</p>
  </div>

  <!-- Modal Konfirmasi Batalkan -->
  <div class="modal-bg" id="cancelModal">
    <div class="modal-box">
      <h3>Yakin ingin membatalkan reservasi ini?</h3>
      <div class="mt-4">
        <button class="btn btn-confirm" onclick="confirmCancel()">Ya, Batalkan</button>
        <button class="btn btn-cancel" onclick="closeModal()">Tidak</button>
      </div>
    </div>
  </div>

  <script>
    function showCancelModal() {
      document.getElementById('cancelModal').style.display = 'flex';
    }

    function closeModal() {
      document.getElementById('cancelModal').style.display = 'none';
    }

    function confirmCancel() {
      window.location.href = "reservasi.php";
    }
  </script>
</body>
</html>

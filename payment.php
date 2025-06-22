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

$tipe_kamar = $_POST['room_type'] ?? '';
$checkin = $_POST['checkin'] ?? '';
$checkout = $_POST['checkout'] ?? '';
$harga = $_POST['price'] ?? 0;
$harga = (float)$harga;
$voucher = $_POST['voucher'] ?? 0;
$voucher = (float)$voucher;

$duration = 0;
if ($checkin && $checkout) {
    $checkin_date = new DateTime($checkin);
    $checkout_date = new DateTime($checkout);
    $duration = $checkin_date->diff($checkout_date)->days;
    $duration = $duration > 0 ? $duration : 1;
}

$subtotal = $harga * $duration;
$total_after_voucher = max($subtotal - $voucher, 0);
$tax = $total_after_voucher * 0.10;
$total_amount = $total_after_voucher + $tax;
?>

<!DOCTYPE html>
  <html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-gray-50 py-10">
    <div class="max-w-xl mx-auto bg-white shadow-md rounded-lg p-8">
      <h2 class="text-2xl font-bold mb-4">Payment Confirmation</h2>

      <form action="process_payment.php" method="post" class="space-y-4">
    <h3 class="font-semibold">Payment Method:</h3>
    <div class="space-y-2">
      <label><input type="radio" name="payment_method" value="Virtual Account" checked> Virtual Account</label><br>
      <label><input type="radio" name="payment_method" value="SeaBank"> SeaBank</label><br>
      <label><input type="radio" name="payment_method" value="ShopeePay"> ShopeePay</label><br>
      <label><input type="radio" name="payment_method" value="OVO"> OVO</label>
    </div>

    <h3 class="font-semibold mt-6">Reservation Details:</h3>
    <table class="w-full text-sm">
      <tr><td class="py-1">Name:</td><td><?= htmlspecialchars($fullname) ?></td></tr>
      <tr><td>Room Type:</td><td><?= htmlspecialchars($tipe_kamar) ?></td></tr>
      <tr><td>Check-in:</td><td><?= htmlspecialchars($checkin) ?></td></tr>
      <tr><td>Check-out:</td><td><?= htmlspecialchars($checkout) ?></td></tr>
      <tr><td>Length of Stay:</td><td><?= $duration ?> malam</td></tr>
      <tr><td>Room Price:</td><td>Rp. <?= number_format($harga, 0, ',', '.') ?></td></tr>
      <tr><td>Subtotal:</td><td>Rp. <?= number_format($subtotal, 0, ',', '.') ?></td></tr>
      <tr><td>Voucher:</td><td>- Rp. <?= number_format($voucher, 0, ',', '.') ?></td></tr>
      <tr><td>Tax (10%):</td><td>Rp. <?= number_format($tax, 0, ',', '.') ?></td></tr>
      <tr class="font-semibold"><td>Total:</td><td>Rp. <?= number_format($total_amount, 0, ',', '.') ?></td></tr>
    </table>

    <!-- Data tersembunyi yang dikirim ke process_payment.php -->
    <input type="hidden" name="user_id" value="<?= $user_id ?>">
    <input type="hidden" name="fullname" value="<?= htmlspecialchars($fullname) ?>">
    <input type="hidden" name="room_type" value="<?= htmlspecialchars($tipe_kamar) ?>">
    <input type="hidden" name="price" value="<?= $harga ?>">
    <input type="hidden" name="voucher" value="<?= $voucher ?>">
    <input type="hidden" name="checkin" value="<?= $checkin ?>">
    <input type="hidden" name="checkout" value="<?= $checkout ?>">
    <input type="hidden" name="duration" value="<?= $duration ?>">
    <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
    <input type="hidden" name="tax" value="<?= $tax ?>">
    <input type="hidden" name="total_amount" value="<?= $total_amount ?>">

    <div class="flex items-center space-x-2">
      <input type="checkbox" required>
      <label class="text-sm">I agree with <a href="#" class="underline">terms and conditions</a></label>
    </div>

    <div class="flex justify-between space-x-4">
      <a href="dashboard.php" class="w-full text-center bg-red-500 text-white py-2 rounded-md hover:bg-red-600 transition">Cancel</a>
      <button type="submit" class="w-full bg-[#008080] text-white py-2 rounded-md hover:bg-[#006666] transition">Pay Now</button>
    </div>
  </form>
  </div>

  <!-- Modal Konfirmasi -->
  <div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-80 text-center">
      <h3 class="text-lg font-semibold mb-4 text-gray-800">Are you sure you want to cancel this payment?</h3>
      <div class="flex justify-center space-x-4">
        <button onclick="confirmCancel()"
                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Yes, Cancel</button>
        <button onclick="closeCancelModal()"
                class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">No</button>
      </div>
    </div>
  </div>

  <script>
    function showCancelModal() {
      const modal = document.getElementById('cancelModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeCancelModal() {
      const modal = document.getElementById('cancelModal');
      modal.classList.remove('flex');
      modal.classList.add('hidden');
    }

    function confirmCancel() {
      window.location.href = "dashboard.php";
    }
  </script>
</body>
</html>

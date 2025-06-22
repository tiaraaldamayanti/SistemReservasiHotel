<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';  // Validasi login admin

$id = $_GET['id'] ?? '';
if (!$id) {
    die("Pemesanan tidak ditemukan.");
}

$stmt = $conn->prepare("SELECT * FROM payments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
if (!$booking) {
    die("Pemesanan tidak ditemukan.");
}

require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/header.php';
?>

<div class="max-w-4xl mx-auto mt-10 mb-28 bg-white p-8 rounded-xl shadow-lg">
  <h1 class="text-3xl font-bold text-teal-700 mb-6 border-b pb-4">Detail Pemesanan</h1>

  <div class="space-y-4 text-gray-700 text-sm sm:text-base">
    <p><span class="font-semibold">Nama:</span> <?= htmlspecialchars($booking['fullname']) ?></p>
    <p><span class="font-semibold">Tipe Kamar:</span> <?= htmlspecialchars($booking['room_type']) ?></p>
    <p><span class="font-semibold">Check-in:</span> <?= htmlspecialchars($booking['checkin']) ?></p>
    <p><span class="font-semibold">Check-out:</span> <?= htmlspecialchars($booking['checkout']) ?></p>
    <p><span class="font-semibold">Durasi:</span> <?= htmlspecialchars($booking['duration']) ?> malam</p>
    <p><span class="font-semibold">Harga Kamar:</span> Rp <?= number_format($booking['subtotal'], 0, ',', '.') ?></p>
    <?php if ($booking['voucher_sale'] > 0): ?>
      <p><span class="font-semibold">Diskon Voucher:</span> -Rp <?= number_format($booking['voucher_sale'], 0, ',', '.') ?></p>
    <?php endif; ?>
    <p><span class="font-semibold">Pajak:</span> Rp <?= number_format($booking['tax'], 0, ',', '.') ?></p>
    <p><span class="font-semibold text-lg">Total Pembayaran:</span> <span class="text-teal-600 font-bold">Rp <?= number_format($booking['total_amount'], 0, ',', '.') ?></span></p>
    <p><span class="font-semibold">Metode Pembayaran:</span> <?= htmlspecialchars($booking['payment_method']) ?></p>
    <p><span class="font-semibold">Status:</span>
      <?php
        $stat = $booking['stat'];
        $color = match ($stat) {
          'pending' => 'bg-yellow-400 text-white',
          'accepted' => 'bg-green-500 text-white',
          'rejected' => 'bg-red-500 text-white',
          default => 'bg-gray-300 text-black'
        };
      ?>
      <span class="inline-block px-3 py-1 text-xs font-bold rounded-full <?= $color ?>"><?= $stat ?></span>
    </p>
  </div>

  <!-- Tombol -->
  <div class="mt-8 flex flex-wrap gap-3">
    <a href="list.php"
       class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg text-sm font-semibold transition">
       Kembali ke Daftar
    </a>
  </div>
</div>

<?php require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/footer.php'; ?>

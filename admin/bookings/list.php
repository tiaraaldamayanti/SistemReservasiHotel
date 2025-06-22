<?php

if (file_exists('../includes/db.php')) {
    require_once '../includes/db.php';
} else {
    die("File db.php tidak ditemukan!");
}
require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/header.php';
require_once '../includes/auth.php';

$limit = 10;
$page = max(1, $_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

$total = $conn->query("SELECT COUNT(*) FROM payments")->fetch_row()[0];
$result = $conn->query("SELECT * FROM payments ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
?>

<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ✅ Notifikasi sukses tolak -->
<?php if (isset($_SESSION['reject_success'])): ?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
      title: 'Berhasil!',
      text: '<?= $_SESSION['reject_success'] ?>',
      icon: 'success',
      confirmButtonText: 'OK',
      timer: 3000,
      timerProgressBar: true
    });
  });
</script>
<?php unset($_SESSION['reject_success']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto p-6 mt-6 bg-white shadow-md rounded-xl">
  <h1 class="text-3xl font-bold text-teal-700 text-center mb-6">Daftar Pemesanan Baru</h1>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm text-left border border-gray-200">
      <thead class="text-xs uppercase bg-teal-100 text-teal-800 font-semibold">
        <tr>
          <th class="px-4 py-3">No</th>
          <th class="px-4 py-3">Nama</th>
          <th class="px-4 py-3">Tipe Kamar</th>
          <th class="px-4 py-3">Check-in</th>
          <th class="px-4 py-3">Check-out</th>
          <th class="px-4 py-3">Total</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody class="text-gray-700 bg-white">
        <?php $no = $offset + 1; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr class="border-t hover:bg-gray-50 transition">
          <td class="px-4 py-3"><?= $no++ ?></td>
          <td class="px-4 py-3"><?= htmlspecialchars($row['fullname']) ?></td>
          <td class="px-4 py-3"><?= htmlspecialchars($row['room_type']) ?></td>
          <td class="px-4 py-3"><?= htmlspecialchars($row['checkin']) ?></td>
          <td class="px-4 py-3"><?= htmlspecialchars($row['checkout']) ?></td>
          <td class="px-4 py-3">Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></td>
          <td class="px-4 py-3">
            <?php
              $status = htmlspecialchars($row['stat']);
              $badgeColor = match ($status) {
                'pending' => 'bg-yellow-400 text-white',
                'accepted' => 'bg-green-500 text-white',
                'rejected' => 'bg-red-500 text-white',
                default => 'bg-gray-300 text-black'
              };
            ?>
            <span class="inline-block text-xs font-bold px-3 py-1 rounded-full <?= $badgeColor ?>">
              <?= $status ?>
            </span>
          </td>
          <td class="px-4 py-3 text-center">
            <a href="view.php?id=<?= $row['id'] ?>" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-semibold">Lihat</a>
            <a href="#"
               onclick="event.preventDefault(); showModalTolak(<?= $row['id'] ?>);"
               class="inline-block bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-semibold ml-2">Tolak</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="flex justify-center mt-6">
    <nav class="flex flex-wrap gap-2">
      <?php $total_pages = ceil($total / $limit); ?>
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>"
           class="px-3 py-1 rounded-md text-sm font-medium <?= $i == $page ? 'bg-teal-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-800' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>
    </nav>
  </div>

  <div class="text-center text-sm text-gray-500 mt-4">
    <p>Per Tanggal: <?= date('Y-m-d') ?></p>
  </div>
</div>

<!-- Modal Tolak -->
<div id="modalTolak" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
  <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-xl text-center">
    <h2 class="text-xl font-semibold text-red-600 mb-4">Konfirmasi Penolakan</h2>
    <p class="text-gray-700 mb-6">Apakah Anda yakin ingin <strong>menolak</strong> pemesanan ini?</p>
    <div class="flex justify-center gap-4">
      <button onclick="confirmTolak()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-semibold">
        Ya, Tolak
      </button>
      <button onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md font-semibold">
        Batal
      </button>
    </div>
  </div>
</div>

<script>
  let tolakId = null;

  function showModalTolak(id) {
    tolakId = id;
    document.getElementById('modalTolak').classList.remove('hidden');
  }

  function closeModal() {
    document.getElementById('modalTolak').classList.add('hidden');
    tolakId = null;
  }

  function confirmTolak() {
    if (tolakId !== null) {
      window.location.href = `update_status.php?id=${tolakId}&action=reject`;
    }
  }
</script>

<?php require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/footer.php'; ?>

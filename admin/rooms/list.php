<?php

require_once '../includes/db.php';
require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/header.php';
require_once '../includes/auth.php';

function generateStars($rating) {
    $rating = (int)$rating;
    return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
}

$limit = 10;
$page = max(1, $_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

$total = $conn->query("SELECT COUNT(*) FROM rooms")->fetch_row()[0];
$result = $conn->query("SELECT * FROM rooms ORDER BY position ASC LIMIT $limit OFFSET $offset");
?>

<!-- ✅ SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ✅ Notifikasi sukses di TENGAH layar -->
<?php if (isset($_SESSION['success'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= addslashes($_SESSION['success']) ?>',
        position: 'center',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
});
</script>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-teal-700">Daftar Kamar</h1>
        <a href="create.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow text-sm font-semibold transition">
            + Tambah Kamar
        </a>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full table-auto text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-3">Tipe Kamar</th>
                    <th class="px-6 py-3">Harga</th>
                    <th class="px-6 py-3">Deskripsi</th>
                    <th class="px-6 py-3">Rating</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="sortable-room-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50" data-id="<?= $row['id'] ?>">
                        <td class="px-6 py-4"><?= htmlspecialchars($row['room_type']) ?></td>
                        <td class="px-6 py-4">Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($row['description']) ?></td>
                        <td class="px-6 py-4 text-yellow-500 text-lg"><?= generateStars($row['rating']) ?></td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="edit.php?id=<?= $row['id'] ?>" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-medium shadow transition">
                                    ✎ Edit
                                </a>
                                <a href="#" onclick="event.preventDefault(); confirmDelete(<?= $row['id'] ?>);" class="inline-flex items-center bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-xs font-medium shadow transition">
                                    ✖ Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center space-x-2">
        <?php
        $total_pages = ceil($total / $limit);
        for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>"
               class="px-3 py-1.5 rounded border text-sm font-medium transition <?= $i == $page ? 'bg-blue-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>

<!-- ✅ Konfirmasi Hapus -->
<script>
function confirmDelete(roomId) {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data kamar akan dihapus permanen.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `delete.php?id=${roomId}`;
        }
    });
}
</script>

<!-- ✅ SortableJS untuk drag & drop urutan kamar -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
new Sortable(document.getElementById('sortable-room-list'), {
    animation: 150,
    onEnd: function () {
        const order = [];
        document.querySelectorAll('#sortable-room-list tr').forEach(function (row) {
            order.push(row.getAttribute('data-id'));
        });

        fetch('update_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order: order })
        });
    }
});
</script>

<?php require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/footer.php'; ?>

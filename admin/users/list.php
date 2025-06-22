<?php

require_once '../includes/db.php';
require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/header.php';
require_once '../includes/auth.php';

$limit = 10;
$page = max(1, $_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

$total = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
?>

<!-- Tailwind CDN & SweetAlert -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['success'])): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= $_SESSION['success'] ?>',
        confirmButtonColor: '#14b8a6',
        confirmButtonText: 'OK'
    });
</script>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="max-w-6xl mx-auto mt-10 bg-white p-8 rounded-lg shadow">
    <h1 class="text-3xl font-bold text-teal-700 mb-6 text-center">Daftar Pengguna</h1>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
            <thead class="bg-teal-600 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Username</th>
                    <th class="py-3 px-4 text-left">Email</th>
                    <th class="py-3 px-4 text-left">Phone</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="py-3 px-4"><?= htmlspecialchars($row['username']) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($row['phone']) ?></td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="edit.php?id=<?= $row['user_id'] ?>"
                                   class="inline-flex items-center px-3 py-1.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded shadow transition">
                                    ✏️ Edit
                                </a>
                                <button onclick="confirmDelete(<?= $row['user_id'] ?>)"
                                   class="inline-flex items-center px-3 py-1.5 text-sm text-white bg-red-600 hover:bg-red-700 rounded shadow transition">
                                    🗑️ Hapus
                                </button>
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
               class="px-4 py-2 rounded border text-sm <?= $i == $page ? 'bg-teal-600 text-white' : 'bg-white text-teal-600 border-teal-500 hover:bg-teal-50' ?>">
               <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>

<!-- Script Konfirmasi Hapus -->
<script>
function confirmDelete(userId) {
    Swal.fire({
        title: 'Yakin hapus pengguna ini?',
        text: "Aksi ini tidak bisa dibatalkan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `delete.php?id=${userId}`;
        }
    });
}
</script>

<?php require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/footer.php'; ?>

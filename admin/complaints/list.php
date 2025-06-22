<?php
require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/auth.php';
require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/header.php';
require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/db.php';

// Query data pengaduan dari ID kecil ke besar
$query = "SELECT * FROM complaints ORDER BY id ASC";
$result = $conn->query($query);
?>

<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-6xl mx-auto mt-10 px-6">
    <div class="bg-white shadow-xl rounded-lg p-6">
        <h1 class="text-2xl font-bold text-teal-700 mb-6 text-center">Manajemen Pengaduan</h1>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-teal-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Nama Lengkap</th>
                        <th class="px-4 py-3 text-left">No. Telepon</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Pesan</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3"><?= htmlspecialchars($row['id']) ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($row['full_name']) ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($row['phone_number']) ?></td>
                            <td class="px-4 py-3"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="px-4 py-3">
                                <?= htmlspecialchars(mb_strimwidth($row['message'], 0, 50, "...")) ?>
                            </td>
                            <td class="px-4 py-3"><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-4 py-5 text-center text-gray-500">Tidak ada data pengaduan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/footer.php'; ?>

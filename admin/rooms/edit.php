<?php

require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/db.php';
require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/auth.php';
require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/header.php';

$id = $_GET['id'] ?? '';
if (!$id) die("Kamar tidak ditemukan.");

$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();
if (!$room) die("Kamar tidak ditemukan.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_type = $_POST['room_type'] ?? '';
    $price = $_POST['price'] ?? 0;
    $description = $_POST['description'] ?? '';
    $total_rooms = $_POST['total_rooms'] ?? 0;
    $rating = $_POST['rating'] ?? 5;
    $imagePath = $room['image'];

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'C:/xampp/htdocs/Reservasi1/Reservasi1/uploads/';
        $imageName = basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;
        $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = $imageName; // Simpan nama file saja
            } else {
                $error = "Gagal mengupload gambar.";
            }
        } else {
            $error = "Format gambar tidak didukung.";
        }
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("UPDATE rooms SET room_type = ?, price = ?, description = ?, image = ?, total_rooms = ?, rating = ? WHERE id = ?");
        $stmt->bind_param("sissiii", $room_type, $price, $description, $imagePath, $total_rooms, $rating, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Perubahan kamar berhasil disimpan!";
            header("Location: list.php");
            exit;
        } else {
            $error = "Gagal mengupdate kamar.";
        }
    }
}
?>

<!-- Tailwind CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-xl mx-auto mt-10 bg-white shadow-lg rounded-lg p-8">
    <h2 class="text-2xl font-bold text-center text-teal-600 mb-6">Edit Kamar</h2>

    <?php if (isset($error)): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-4" id="formEdit">
        <div>
            <label for="room_type" class="block font-semibold mb-1">Tipe Kamar</label>
            <input type="text" id="room_type" name="room_type" value="<?= htmlspecialchars($room['room_type']) ?>" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-teal-300" required>
        </div>

        <div>
            <label for="price" class="block font-semibold mb-1">Harga (Rp)</label>
            <input type="number" id="price" name="price" value="<?= htmlspecialchars($room['price']) ?>" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-teal-300" required>
        </div>

        <div>
            <label for="description" class="block font-semibold mb-1">Deskripsi</label>
            <textarea id="description" name="description" class="w-full border rounded px-3 py-2 h-28 focus:outline-none focus:ring focus:ring-teal-300" required><?= htmlspecialchars($room['description']) ?></textarea>
        </div>

        <div>
            <label for="total_rooms" class="block font-semibold mb-1">Jumlah Unit Kamar</label>
            <input type="number" id="total_rooms" name="total_rooms" min="1" value="<?= htmlspecialchars($room['total_rooms']) ?>" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-teal-300" required>
        </div>

        <div>
            <label for="rating" class="block font-semibold mb-1">Rating (1 - 5)</label>
            <input type="number" id="rating" name="rating" min="1" max="5" value="<?= htmlspecialchars($room['rating']) ?>" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-teal-300" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Gambar Saat Ini</label>
            <img src="/Reservasi1/Reservasi1/uploads/<?= htmlspecialchars($room['image']) ?>" alt="Gambar Kamar" class="w-40 rounded shadow mb-2">
        </div>

        <div>
            <label for="image" class="block font-semibold mb-1">Ganti Gambar (opsional)</label>
            <input type="file" id="image" name="image" accept="image/*" class="w-full border rounded px-3 py-2 bg-white focus:outline-none focus:ring focus:ring-teal-300">
            <small class="text-gray-500">Format: jpg, jpeg, png, gif</small>
        </div>

        <div class="pt-6 flex justify-between">
            <a href="list.php" class="bg-gray-400 hover:bg-gray-500 text-white font-semibold px-4 py-2 rounded shadow transition">
                ← Kembali ke Daftar Kamar
            </a>

            <button type="button" onclick="showConfirmation()" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-2 rounded shadow transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<!-- SweetAlert Konfirmasi Simpan -->
<script>
function showConfirmation() {
    Swal.fire({
        title: 'Simpan Perubahan?',
        text: "Pastikan data sudah benar.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#14b8a6',
        cancelButtonColor: '#d1d5db',
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formEdit').submit();
        }
    });
}
</script>

<?php require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/footer.php'; ?>

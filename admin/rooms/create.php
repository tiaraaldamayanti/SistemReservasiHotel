<?php

require_once '../includes/db.php';
require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/header.php';
require_once '../includes/auth.php';

$room_type = $price = $description = $total_rooms = $rating = "";
$image = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_type = trim($_POST['room_type']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $total_rooms = trim($_POST['total_rooms']);
    $rating = trim($_POST['rating']);

    if (empty($room_type)) $errors[] = "Tipe kamar tidak boleh kosong.";
    if (!is_numeric($price) || $price <= 0) $errors[] = "Harga harus berupa angka positif.";
    if (empty($description)) $errors[] = "Deskripsi tidak boleh kosong.";
    if (!is_numeric($total_rooms) || $total_rooms <= 0) $errors[] = "Jumlah unit kamar harus berupa angka positif.";
    if (!is_numeric($rating) || $rating < 1 || $rating > 5) $errors[] = "Rating harus antara 1 sampai 5.";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_size = $_FILES['image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_ext)) {
            $errors[] = "Format gambar tidak valid. Hanya jpg, jpeg, png, gif.";
        } elseif ($file_size > 2 * 1024 * 1024) {
            $errors[] = "Ukuran gambar maksimal 2MB.";
        } else {
            $new_filename = uniqid("room_", true) . "." . $file_ext;
            $upload_path = __DIR__ . "/../../uploads/" . $new_filename;

            if (!move_uploaded_file($file_tmp, $upload_path)) {
                $errors[] = "Gagal mengunggah gambar.";
            } else {
                $image = $new_filename;
            }
        }
    } else {
        $errors[] = "Gambar kamar wajib diunggah.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO rooms (room_type, price, description, image, total_rooms, rating, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sdssii", $room_type, $price, $description, $image, $total_rooms, $rating);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Kamar berhasil ditambahkan.";
            header("Location: list.php");
            exit;
        } else {
            $errors[] = "Gagal menyimpan data: " . $stmt->error;
        }
    }
}
?>

<!-- TAILWIND + SweetAlert -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-xl mx-auto mt-10 bg-white shadow-lg rounded-lg p-8">
    <h2 class="text-2xl font-bold text-center text-teal-600 mb-6">Tambah Kamar Baru</h2>

    <?php if (!empty($errors)): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="formCreate" class="space-y-4">
        <div>
            <label for="room_type" class="block font-semibold mb-1">Tipe Kamar</label>
            <input type="text" id="room_type" name="room_type" value="<?= htmlspecialchars($room_type) ?>" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-teal-300" required>
        </div>

        <div>
            <label for="price" class="block font-semibold mb-1">Harga (Rp)</label>
            <input type="number" id="price" name="price" value="<?= htmlspecialchars($price) ?>" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-teal-300" required>
        </div>

        <div>
            <label for="description" class="block font-semibold mb-1">Deskripsi</label>
            <textarea id="description" name="description" class="w-full border rounded px-3 py-2 h-28 focus:outline-none focus:ring focus:ring-teal-300" required><?= htmlspecialchars($description) ?></textarea>
        </div>

        <div>
            <label for="total_rooms" class="block font-semibold mb-1">Jumlah Unit Kamar</label>
            <input type="number" id="total_rooms" name="total_rooms" min="1" value="<?= htmlspecialchars($total_rooms) ?>" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-teal-300" required>
        </div>

        <div>
            <label for="rating" class="block font-semibold mb-1">Rating (1 - 5)</label>
            <input type="number" id="rating" name="rating" min="1" max="5" value="<?= htmlspecialchars($rating) ?>" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-teal-300" required>
        </div>

        <div>
            <label for="image" class="block font-semibold mb-1">Gambar Kamar</label>
            <input type="file" id="image" name="image" accept="image/*" class="w-full border rounded px-3 py-2 bg-white focus:outline-none focus:ring focus:ring-teal-300" required>
        </div>

        <div class="text-center pt-4">
            <button type="button" onclick="validateAndShowModal()" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-2 rounded shadow transition">
                Simpan Kamar
            </button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <a href="list.php" class="text-blue-600 hover:underline text-sm">← Kembali ke Daftar Kamar</a>
    </div>
</div>

<!-- VALIDASI + POPUP KONFIRMASI -->
<script>
function validateAndShowModal() {
    const form = document.getElementById('formCreate');
    const room_type = form.room_type.value.trim();
    const price = form.price.value.trim();
    const description = form.description.value.trim();
    const total_rooms = form.total_rooms.value.trim();
    const rating = form.rating.value.trim();
    const image = form.image.files.length;

    if (!room_type || !price || !description || !total_rooms || !rating || image === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Semua kolom wajib diisi termasuk gambar kamar!'
        });
        return;
    }

    if (parseInt(rating) < 1 || parseInt(rating) > 5) {
        Swal.fire({
            icon: 'error',
            title: 'Rating Tidak Valid',
            text: 'Rating harus antara 1 sampai 5.'
        });
        return;
    }

    if (parseFloat(price) <= 0 || parseInt(total_rooms) <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Input Tidak Valid',
            text: 'Harga dan jumlah kamar harus lebih dari 0.'
        });
        return;
    }

    Swal.fire({
        title: 'Simpan Kamar?',
        text: "Kamar akan ditambahkan ke daftar.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#14b8a6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>

<?php require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/footer.php'; ?>

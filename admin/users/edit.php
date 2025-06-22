<?php

require_once '../includes/db.php';
require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/header.php';
require_once '../includes/auth.php';

$id = $_GET['id'] ?? '';
if (!$id) die("Pengguna tidak ditemukan.");

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) die("Pengguna tidak ditemukan.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashed_password = $password ? password_hash($password, PASSWORD_DEFAULT) : $user['password'];

    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, phone = ?, password = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $username, $email, $phone, $hashed_password, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Perubahan berhasil disimpan!";
        header("Location: list.php");
        exit;
    } else {
        $error = "Gagal mengupdate data pengguna.";
    }
    $stmt->close();
}
?>

<!-- Tailwind & SweetAlert -->
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-lg mx-auto mt-10 bg-white p-8 rounded-lg shadow">
    <h1 class="text-2xl font-bold text-center text-teal-600 mb-6">Edit Pengguna</h1>

    <?php if (isset($error)): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4" id="editUserForm">
        <div>
            <label for="username" class="block font-medium text-gray-700">Username</label>
            <input type="text" name="username" id="username"
                   value="<?= htmlspecialchars($user['username']) ?>"
                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-teal-300" required>
        </div>

        <div>
            <label for="email" class="block font-medium text-gray-700">Email</label>
            <input type="email" name="email" id="email"
                   value="<?= htmlspecialchars($user['email']) ?>"
                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-teal-300" required>
        </div>

        <div>
            <label for="phone" class="block font-medium text-gray-700">No. Telepon</label>
            <input type="text" name="phone" id="phone"
                   value="<?= htmlspecialchars($user['phone']) ?>"
                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-teal-300" required>
        </div>

        <div>
            <label for="password" class="block font-medium text-gray-700">Password Baru <span class="text-sm text-gray-500">(kosongkan jika tidak ingin mengubah)</span></label>
            <input type="password" name="password" id="password"
                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:ring-teal-300">
        </div>

        <div class="pt-4 flex justify-between items-center">
            <button type="button" onclick="confirmBack()" class="bg-gray-400 hover:bg-gray-500 text-white font-semibold px-5 py-2 rounded shadow transition">
                ← Kembali
            </button>
            <button type="button" onclick="confirmSave()" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-2 rounded shadow transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
    function confirmBack() {
        Swal.fire({
            title: 'Yakin kembali?',
            text: "Perubahan yang belum disimpan akan hilang.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kembali',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "list.php";
            }
        });
    }

    function confirmSave() {
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: "Pastikan data sudah benar.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('editUserForm').submit();
            }
        });
    }
</script>

<?php require_once 'C:/xampp/htdocs/Reservasi1/Reservasi1/admin/includes/footer.php'; ?>

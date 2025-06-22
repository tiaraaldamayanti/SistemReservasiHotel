<?php
require_once '../includes/auth.php';  // Validasi login admin
require_once '../includes/header.php';  // Memasukkan header.php yang benar
require_once '../includes/db.php';  // Memasukkan koneksi database

// Cek apakah ada ID pengaduan yang diterima dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Pengaduan tidak ditemukan.");
}

$id = $_GET['id'];

// Ambil data pengaduan berdasarkan ID
$query = "SELECT * FROM complaints WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Jika pengaduan tidak ditemukan
if ($result->num_rows == 0) {
    die("Pengaduan tidak ditemukan.");
}

$complaint = $result->fetch_assoc();

// Proses form jika tanggapan dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response_text = $_POST['response_text'];
    $status = 1;  // Menandakan pengaduan sudah ditanggapi
    $response_at = date('Y-m-d H:i:s');

    // Update tanggapan dan status pengaduan
    $update_query = "UPDATE complaints SET response_text = ?, status = ?, response_at = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sisi", $response_text, $status, $response_at, $id);
    
    if ($update_stmt->execute()) {
        echo "<div class='text-green-500 text-center p-4'>Tanggapan berhasil disimpan.</div>";
    } else {
        echo "<div class='text-red-500 text-center p-4'>Terjadi kesalahan, coba lagi.</div>";
    }
}
?>

<div class="p-6">
    <h1 class="text-3xl font-semibold text-center mb-6">Tanggapi Pengaduan</h1>

    <!-- Form untuk menanggapi pengaduan -->
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Pengaduan dari: <?php echo $complaint['user_name']; ?></h2>
        <p><strong>Tanggal Pengaduan:</strong> <?php echo $complaint['created_at']; ?></p>
        <p><strong>Isi Pengaduan:</strong></p>
        <p><?php echo nl2br($complaint['complaint_text']); ?></p>

        <form method="POST" class="mt-4">
            <label for="response_text" class="block text-lg font-semibold">Tanggapan Anda:</label>
            <textarea id="response_text" name="response_text" rows="5" class="w-full p-4 mt-2 border rounded-lg" required></textarea>
            
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 mt-4 rounded-lg hover:bg-blue-600">Kirim Tanggapan</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php';  // Menambahkan footer.php ?>

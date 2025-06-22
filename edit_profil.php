<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['update'])) {
    $username = htmlspecialchars($_POST['username']);
    $phone = htmlspecialchars($_POST['phone']);

    // Jika ada file foto diunggah
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = mime_content_type($_FILES['profile_picture']['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $uploadPath = $uploadDir . $user_id . '.' . $extension;

            move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadPath);

            // Simpan username, phone, dan foto profil
            $stmt = $conn->prepare("UPDATE users SET username = ?, phone = ?, profile_picture = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $username, $phone, $uploadPath, $user_id);
        } else {
            echo "<script>alert('Format file tidak valid. Hanya JPG dan PNG diperbolehkan.');</script>";
            exit();
        }
    } else {
        // Simpan tanpa update foto
        $stmt = $conn->prepare("UPDATE users SET username = ?, phone = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $username, $phone, $user_id);
    }

    $stmt->execute();
    $stmt->close();

    // Kirim notifikasi ke halaman profile.php
    $_SESSION['success_message'] = 'Perubahan profil berhasil disimpan!';
    header("Location: profile.php");
    exit();
}

// Ambil data user
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Profil</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      width: 400px;
      padding: 30px;
      border: 1px solid #ddd;
      border-radius: 10px;
      background-color: white;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      color: #3f3d56;
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }
    input[type="text"],
    input[type="email"],
    input[type="file"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #aaa;
      border-radius: 8px;
      margin-top: 5px;
      font-size: 14px;
    }
    input[readonly] {
      background-color: #eee;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #008080;
      color: white;
      border: none;
      border-radius: 8px;
      margin-top: 20px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover { background-color: #006666; }
    .cancel-btn {
      display: inline-block;
      text-align: center;
      width: 100%;
      padding: 12px;
      background-color: #999;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      margin-top: 10px;
      font-size: 16px;
    }
    .cancel-btn:hover { background-color: #777; }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    .modal-content {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 6px 20px rgba(0,0,0,0.25);
      width: 90%;
      max-width: 400px;
    }
    .modal-content .icon {
      font-size: 40px;
      margin-bottom: 10px;
    }
    .modal-content h3 {
      font-size: 18px;
      color: #333;
      margin-bottom: 20px;
    }
    .modal-content button {
      padding: 10px 25px;
      margin: 8px;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.2s ease-in-out;
    }
    .btn-yes {
      background-color: #008080;
      color: white;
    }
    .btn-yes:hover { background-color: #006666; }
    .btn-no {
      background-color: #ccc;
      color: #333;
    }
    .btn-no:hover { background-color: #aaa; }
  </style>
</head>
<body>

  <div class="container">
    <h2>Edit Profil</h2>
    <form id="editForm" method="POST" enctype="multipart/form-data">
      <label>Email</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>

      <label>Username</label>
      <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

      <label>Phone Number</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

      <label>Foto Profil</label>
      <input type="file" name="profile_picture" accept="image/*">

      <input type="hidden" name="update" value="1">
      <button type="button" onclick="showSaveModal()">💾 Simpan Perubahan</button>
      <a href="#" class="cancel-btn" onclick="showCancelModal()">❌ Batalkan</a>
    </form>
  </div>

  <!-- Modal Simpan -->
  <div class="modal" id="saveModal">
    <div class="modal-content">
      <div class="icon">💾</div>
      <h3>Yakin ingin menyimpan perubahan profil?</h3>
      <button class="btn-yes" onclick="submitForm()">Ya, Simpan</button>
      <button class="btn-no" onclick="closeModal('saveModal')">Batal</button>
    </div>
  </div>

  <!-- Modal Batal -->
  <div class="modal" id="cancelModal">
    <div class="modal-content">
      <div class="icon">⚠️</div>
      <h3>Semua perubahan akan dibatalkan. Kembali ke profil?</h3>
      <button class="btn-yes" onclick="window.location.href='profile.php'">Ya, Kembali</button>
      <button class="btn-no" onclick="closeModal('cancelModal')">Tetap di sini</button>
    </div>
  </div>

  <script>
    function showSaveModal() {
      document.getElementById('saveModal').style.display = 'flex';
    }

    function showCancelModal() {
      document.getElementById('cancelModal').style.display = 'flex';
    }

    function closeModal(id) {
      document.getElementById(id).style.display = 'none';
    }

    function submitForm() {
      document.getElementById('editForm').submit();
    }
  </script>

</body>
</html>

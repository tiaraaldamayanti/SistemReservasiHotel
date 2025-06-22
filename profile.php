<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
$user = $result->fetch_assoc();

$profile_image = 'uploads/' . $user_id . '.jpg';
$successMessage = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil Saya</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f0f0f0;
    }

    .header {
      background: #fff;
      padding: 20px;
      text-align: center;
      font-size: 22px;
      font-weight: bold;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      position: relative;
      z-index: 998;
    }

    .menu-icon {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 24px;
      cursor: pointer;
    }

    /* Sidebar */
    .sidebar {
      height: 100%;
      width: 250px;
      position: fixed;
      top: 0;
      left: -250px;
      background-color: #008080;
      overflow-x: hidden;
      transition: left 0.3s ease;
      padding-top: 60px;
      z-index: 999;
    }

    .sidebar a {
      padding: 12px 24px;
      text-decoration: none;
      font-size: 18px;
      color: white;
      display: block;
      transition: background 0.3s;
    }

    .sidebar a:hover {
      background-color: #006666;
    }

    .closebtn {
      position: absolute;
      top: 10px;
      right: 20px;
      font-size: 30px;
      color: white;
      cursor: pointer;
    }

    .container {
      max-width: 600px;
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      margin: 40px auto;
      position: relative;
      z-index: 1;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 24px;
      color: #3f3d56;
    }

    .profile-image {
      display: block;
      margin: 0 auto 20px;
      width: 100px;
      height: 100px;
      background-color: #ddd;
      border-radius: 50%;
      overflow: hidden;
    }

    .profile-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .profile-info {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 10px;
      margin-bottom: 20px;
    }

    .profile-info label {
      font-weight: bold;
    }

    .profile-info p {
      margin: 0;
    }

    a, button.logout {
      display: inline-block;
      text-align: center;
      background-color: #008080;
      color: #fff;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      margin-top: 20px;
      width: 100%;
      border: none;
      cursor: pointer;
    }

    a:hover {
      background-color: rgb(2, 83, 83);
    }

    .footer {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: #aaa;
    }

    button.logout {
      background-color: #e74c3c;
      margin-top: 10px;
    }

    button.logout:hover {
      background-color: #c0392b;
    }

    /* Modal Logout */
    #logoutModal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal-content {
      background: #fff;
      padding: 20px 30px;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .modal-content h3 {
      margin-bottom: 20px;
    }

    .modal-content button {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      margin: 0 5px;
      cursor: pointer;
    }

    .btn-yes {
      background-color: #e74c3c;
      color: #fff;
    }

    .btn-no {
      background-color: #ccc;
      color: #000;
    }

    .btn-yes:hover {
      background-color: #c0392b;
    }

    .btn-no:hover {
      background-color: #aaa;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div id="mySidebar" class="sidebar">
  <a href="javascript:void(0)" class="closebtn" onclick="closeSidebar()">&times;</a>
  <a href="dashboard.php"><i class="fas fa-home"></i> Beranda</a>
  <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
  <a href="riwayat_transaksi.php"><i class="fas fa-history"></i> Riwayat</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Header -->
<div class="header">
  <span class="menu-icon" onclick="openSidebar()"><i class="fas fa-bars"></i></span>
  Profil Saya
</div>

<!-- Konten Profil -->
<div class="container">
  <h2>Profil Saya</h2>

  <div class="profile-image">
    <img src="<?php echo $profile_image; ?>" alt="Foto Profil">
  </div>

  <div class="profile-info">
    <label>Email</label>
    <p><?= htmlspecialchars($user['email']) ?></p>

    <label>Username</label>
    <p><?= htmlspecialchars($user['username']) ?></p>

    <label>Phone Number</label>
    <p><?= htmlspecialchars($user['phone']) ?></p>
  </div>

  <a href="edit_profil.php">Edit Profil</a>
  <button class="logout" onclick="showLogoutModal()">Logout</button>

  <div class="footer">
    &copy; SESADUL 2025
  </div>
</div>

<!-- Modal Logout -->
<div id="logoutModal">
  <div class="modal-content">
    <h3>Yakin ingin logout?</h3>
    <button class="btn-yes" onclick="confirmLogout()">Ya</button>
    <button class="btn-no" onclick="hideLogoutModal()">Batal</button>
  </div>
</div>

<!-- Script -->
<script>
  function showLogoutModal() {
    document.getElementById('logoutModal').style.display = 'flex';
  }

  function hideLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
  }

  function confirmLogout() {
    window.location.href = 'logout.php';
  }

  function openSidebar() {
    document.getElementById("mySidebar").style.left = "0";
  }

  function closeSidebar() {
    document.getElementById("mySidebar").style.left = "-250px";
  }

  <?php if ($successMessage): ?>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?= addslashes($successMessage) ?>',
    position: 'center',
    timer: 3000,
    showConfirmButton: false,
    timerProgressBar: true
  });
  <?php endif; ?>
</script>

</body>
</html>

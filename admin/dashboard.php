<?php

require_once '../admin/includes/auth.php';
require_once '../admin/includes/header.php';

// Notifikasi login berhasil
if (isset($_SESSION['login_success'])) {
    echo '<div id="loginAlert" class="bg-green-100 text-green-800 p-4 rounded-lg shadow mb-4 text-center max-w-3xl mx-auto transition-opacity duration-500">'
        . $_SESSION['login_success'] .
        '</div>';
    unset($_SESSION['login_success']);
}
?>

<!-- Konten utama -->
<div class="flex-grow bg-gradient-to-br from-cyan-100 via-white to-purple-100 p-10 mb-28">
  <div class="max-w-7xl mx-auto">
    <h1 class="text-4xl font-bold text-center text-teal-800 drop-shadow-md mb-12">
      Dashboard Admin
    </h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">

      <!-- Manajemen Pemesanan -->
      <a href="bookings/list.php" class="bg-white p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-transform hover:-translate-y-1 border border-gray-200 hover:border-teal-300">
        <div class="flex items-center space-x-4">
          <div class="bg-gradient-to-tr from-teal-100 to-blue-100 text-teal-600 p-3 rounded-full">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M8 7V3m8 4V3m-9 8h10m-11 8h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-lg font-semibold text-gray-800">Manajemen Pemesanan</h2>
            <p class="text-gray-500 text-sm">Lihat, terima, atau tolak pemesanan kamar.</p>
          </div>
        </div>
      </a>

      <!-- Manajemen Kamar -->
      <a href="rooms/list.php" class="bg-white p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-transform hover:-translate-y-1 border border-gray-200 hover:border-teal-300">
        <div class="flex items-center space-x-4">
          <div class="bg-gradient-to-tr from-teal-100 to-blue-100 text-teal-600 p-3 rounded-full">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M4 10V6a2 2 0 012-2h12a2 2 0 012 2v4m0 4v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4"/>
            </svg>
          </div>
          <div>
            <h2 class="text-lg font-semibold text-gray-800">Manajemen Kamar</h2>
            <p class="text-gray-500 text-sm">Kelola daftar kamar, harga, dan fasilitas.</p>
          </div>
        </div>
      </a>

      <!-- Manajemen Pengguna -->
      <a href="users/list.php" class="bg-white p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-transform hover:-translate-y-1 border border-gray-200 hover:border-teal-300">
        <div class="flex items-center space-x-4">
          <div class="bg-gradient-to-tr from-teal-100 to-blue-100 text-teal-600 p-3 rounded-full">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M5 20h14M12 4a4 4 0 110 8 4 4 0 010-8zm0 14a6 6 0 00-6-6H5a6 6 0 0112 0h-1a6 6 0 00-6 6z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-lg font-semibold text-gray-800">Manajemen Pengguna</h2>
            <p class="text-gray-500 text-sm">Kelola data pengguna yang melakukan reservasi.</p>
          </div>
        </div>
      </a>

      <!-- Manajemen Pengaduan -->
      <a href="complaints/list.php" class="bg-white p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-transform hover:-translate-y-1 border border-gray-200 hover:border-teal-300">
        <div class="flex items-center space-x-4">
          <div class="bg-gradient-to-tr from-teal-100 to-blue-100 text-teal-600 p-3 rounded-full">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M9 13h6m2 6H7a2 2 0 01-2-2V5a2 2 0 012-2h4l2 2h5a2 2 0 012 2v10a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-lg font-semibold text-gray-800">Manajemen Pengaduan</h2>
            <p class="text-gray-500 text-sm">Tanggapi keluhan pelanggan dengan cepat dan tepat.</p>
          </div>
        </div>
      </a>

    </div>
  </div>
</div>

<?php require_once '../admin/includes/footer.php'; ?>

<!-- Script untuk menghilangkan notifikasi setelah 4 detik -->
<script>
  setTimeout(function () {
    const alertBox = document.getElementById("loginAlert");
    if (alertBox) {
      alertBox.classList.add("opacity-0"); // efek memudar
      setTimeout(() => alertBox.remove(), 500); // hapus elemen setelah fade-out
    }
  }, 3000); // 4 detik
</script>

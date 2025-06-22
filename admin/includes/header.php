<?php
// BASE URL - sesuaikan dengan struktur proyekmu
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/Reservasi1/Reservasi1/";
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ADMIN Hotel SESADUL</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#0f766e',
            secondary: '#1e3a8a',
            accent: '#6366f1',
          }
        }
      }
    };
  </script>
</head>

<body class="bg-gray-50 font-sans antialiased text-gray-800">

<!-- Sticky Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-gradient-to-r from-blue-800 via-teal-600 to-cyan-500 text-white shadow-md">
  <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
    <div class="text-2xl font-extrabold tracking-wider">
      <a href="<?= $base_url ?>admin/dashboard.php" class="hover:text-cyan-200 transition duration-300">ADMIN SESADUL</a>
    </div>
    <ul class="hidden md:flex space-x-6 font-medium text-sm tracking-wide">
      <li><a href="<?= $base_url ?>admin/dashboard.php" class="hover:text-yellow-200 transition">Dashboard</a></li>
      <li><a href="<?= $base_url ?>admin/bookings/list.php" class="hover:text-yellow-200 transition">Pemesanan</a></li>
      <li><a href="<?= $base_url ?>admin/rooms/list.php" class="hover:text-yellow-200 transition">Kamar</a></li>
      <li><a href="<?= $base_url ?>admin/users/list.php" class="hover:text-yellow-200 transition">Pengguna</a></li>
      <li><a href="<?= $base_url ?>admin/complaints/list.php" class="hover:text-yellow-200 transition">Pengaduan</a></li>
      <li><a href="<?= $base_url ?>admin/logout.php" class="hover:text-red-200 transition">Logout</a></li>
    </ul>
  </div>
</nav>

<!-- Mulai konten utama dengan padding top untuk header -->
<div class="pt-20 pb-24 min-h-screen">

<?php
session_start(); // Memulai sesi untuk mengakses data session
session_destroy(); // Menghancurkan session yang ada

// Mengarahkan pengguna kembali ke halaman login
header("Location: login.php");
exit();
?>

<?php
require_once 'db.php'; // Koneksi ke database

if (isset($_POST['forgot_password'])) {
    $email = htmlspecialchars($_POST['email']);

    $conn = new mysqli("localhost", "root", "", "hotel");

    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Debugging
        echo "Email ditemukan. Mengarahkan ke halaman reset.";
        header("Location: reset_password.php?email=$email");
        exit();
    } else {
        // Debugging
        
        $error = "Email tidak ditemukan.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lupa Password</title>
    <link rel="stylesheet" href="../assets/css/auth.css" />
    <style>
        body { font-family: Arial, sans-serif; background-color:rgb(236, 226, 249); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 20px rgba(24, 12, 12, 0.1); width: 400px; }
        h2 { text-align: center; margin-bottom: 20px; }
        label { display: block; margin-bottom: 10px; }
        input[type="email"] { width: 93%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; padding: 12px; background-color:#008080; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color:rgb(0, 128, 128); }
        .error { color: red; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Lupa Password</h2>
        <form method="POST">
            <label for="email">Masukkan Email Anda:</label>
            <input type="email" name="email" required>
            <button type="submit" name="forgot_password">Submit</button>
        </form>

        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    </div>
</body>
</html>

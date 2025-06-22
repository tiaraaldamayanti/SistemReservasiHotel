<?php
require_once 'db.php'; // koneksi sudah di sini

if (isset($_GET['email'])) {
    $email = htmlspecialchars($_GET['email']);
} else {
    header("Location: forgot_password.php");
    exit();
}

if (isset($_POST['reset_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($new_password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Password tidak sama.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Cek apakah email ada di database
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 1) {
            $sql = "UPDATE users SET password = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $hashed_password, $email);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    header("Location: login.php?reset_success=true");
                    exit();
                } else {
                    $error = "Password sama dengan sebelumnya atau tidak ada perubahan.";
                }
            } else {
                $error = "Gagal mengubah password.";
            }

            $stmt->close();
        } else {
            $error = "Email tidak ditemukan.";
        }

        $check->close();
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/auth.css" />
    <style>
        body { font-family: Arial, sans-serif; background-color:rgb(236, 226, 249); display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); width: 400px; }
        h2 { text-align: center; margin-bottom: 20px; }
        label { display: block; margin-bottom: 10px; }
        input[type="password"] { width: 93%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        button { width: 100%; padding: 12px; background-color:#5d50c6; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: rgb(128, 106, 225); }
        .error { color: red; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form method="POST">
            <label>Password Baru:</label>
            <input type="password" name="new_password" required>
            <label>Konfirmasi Password:</label>
            <input type="password" name="confirm_password" required>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>

        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    </div>
</body>
</html>
<?php
require_once 'db.php';
session_start();

if (isset($_POST['login'])) {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['username']; // Simpan username
                $_SESSION['login_success'] = true; // Tandai login berhasil

                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Email not found.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="../assets/css/auth.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: Arial, sans-serif;
      background-image: url('uploads/dashboard.jpg'); 
      background-size: cover;
      background-position: center; 
      background-repeat: no-repeat; 
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .alert-box {
      background-color: #ffdddd;
      color: #a94442;
      padding: 12px 20px;
      border: 1px solid #f5c6cb;
      border-radius: 5px;
      margin-bottom: 20px;
      text-align: center;
      width: 400px;
      font-size: 14px;
    }

    .container {
      width: 400px;
      padding: 30px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background-color: white;
      box-shadow: 0 2px 8px rgba(4, 4, 11, 0.1);
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

    input[type="email"],
    input[type="password"],
    input[type="text"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #aaa;
      border-radius: 8px;
      margin-top: 5px;
      font-size: 14px;
      height: 44px;
      font-family: inherit;
      line-height: 1.5;
      box-sizing: border-box;
    }

    .password-wrapper {
      position: relative;
    }

    .password-wrapper input {
      padding-right: 40px;
    }

    .password-wrapper .toggle-password {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #555;
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

    button:hover {
      background-color: rgb(6, 210, 199);
    }

    .footer {
      margin-top: 20px;
      text-align: center;
      font-size: 14px;
      color: #666;
    }

    .forgot {
      text-align: center;
      margin-top: 10px;
    }

    .forgot a {
      color: #008080;
      text-decoration: underline;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <?php if (isset($error)) : ?>
    <div class="alert-box"><?= $error ?></div>
  <?php endif; ?>

  <div class="container">
    <h2>Login</h2>
    <form method="POST">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" required>

      <label for="password">Password</label>
      <div class="password-wrapper">
        <input type="password" name="password" id="password" required>
        <i class="fa fa-eye toggle-password" id="togglePassword"></i>
      </div>

      <div class="forgot">
        <a href="forgot_password.php">Forgot password?</a>
      </div>

      <button type="submit" name="login">Log In</button>
    </form>

    <div class="footer">
      Don't have an account? <a href="register.php">Register</a>
    </div>

    <div class="footer">
      &copy; SESADUL 2025
    </div>
  </div>

  <script>
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");

    togglePassword.addEventListener("click", function () {
      const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);
      this.classList.toggle("fa-eye-slash");
    });
  </script>
</body>
</html>

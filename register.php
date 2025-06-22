<?php
require_once 'db.php';
session_start();

if (isset($_POST['register'])) {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $password_raw = $_POST['password'];

    if (!ctype_digit($phone)) {
        $error = "Phone numbers may only contain digits.";
    } else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password_raw)) {
        $error = "Password must be at least 6 characters long, contain uppercase and lowercase letters, a number, and a special character.";
    } else {
        $password = password_hash($password_raw, PASSWORD_BCRYPT);

        $check = $conn->prepare("SELECT 1 FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "The email is already registered. Please use another email.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $phone, $password);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['username'] = $username;
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
        $check->close();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
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

    .container {
      width: 400px;
      padding: 30px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background-color: white;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      text-align: left;
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
    input[type="password"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #aaa;
      border-radius: 8px;
      margin-top: 5px;
      font-size: 14px;
      height: 44px;
    }

    .password-wrapper {
      position: relative;
    }

    .password-wrapper input {
      padding-right: 40px;
    }

    .toggle-password {
      position: absolute;
      top: 50%;
      right: 12px;
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
      background-color: rgb(128, 106, 225);
    }

    .footer {
      margin-top: 20px;
      text-align: center;
      font-size: 14px;
      color: #666;
    }

    .footer a {
      color: rgb(128, 106, 225);
      text-decoration: underline;
    }

    .alert-box {
      background-color: #ffdddd;
      color: #a94442;
      padding: 15px;
      border: 1px solid #f5c6cb;
      border-radius: 5px;
      margin: 20px auto;
      width: 400px;
      text-align: center;
      font-size: 14px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <?php if (isset($error)) : ?>
    <div class="alert-box"><?= $error ?></div>
  <?php endif; ?>

  <div class="container">
    <h2>Register</h2>
    <form method="POST">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required />

      <label for="username">Username</label>
      <input type="text" id="username" name="username" required />

      <label for="phone">Phone Number</label>
      <input type="text" id="phone" name="phone" required />

      <label for="password">Password</label>
      <div class="password-wrapper">
        <input type="password" id="password" name="password" minlength="6" required />
        <i class="fa fa-eye toggle-password" id="togglePassword"></i>
      </div>

      <button type="submit" name="register">Sign Up</button>
    </form>

    <div class="footer">
      Already have an account? <a href="login.php">Log in here</a>
    </div>

    <div class="footer">
      © SESADUL 2025
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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AfroLove | African Dating & Connections</title>
  <link rel="stylesheet" href="./css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
  <div class="auth-wrapper">
    <div class="auth-container">
      <h2 class="auth-title">Afro Love</h2>

      <?php
        session_start();
        if (isset($_SESSION['message'])) {
          echo "<div class='alert-message'>" . $_SESSION['message'] . "</div>";
          unset($_SESSION['message']);
        }
      ?>

      <!-- Registration Form -->
      <div class="auth-form auth-register" id="register-form" style="display: none;">
        <h3>Register</h3>
        <form action="./pages/auth.php" method="POST" class="form">
          <input class="form-input" type="text" name="username" placeholder="Enter Username" required />
          <input class="form-input" type="email" name="email" placeholder="Enter Email" required />
          <input class="form-input" type="password" name="password" placeholder="Enter Password" required />
          <button class="form-button" type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="#" onclick="showLogin()">Login</a></p>
      </div>

      <!-- Login Form -->
      <div class="auth-form auth-login" id="login-form">
        <h3>Login</h3>
        <form action="./pages/auth.php" method="POST" class="form">
          <input class="form-input" type="text" name="identifier" placeholder="Enter Email or Username" required />
          <input class="form-input" type="password" name="password" placeholder="Enter Password" required />
          <button class="form-button" type="submit" name="login">Login</button>
        </form>
        <p><a href="#">Forgot Password?</a></p>
        <p>Don't have an account? <a href="#" onclick="showRegister()">Register</a></p>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
    // Show Login Form
    function showLogin() {
        document.getElementById('register-form').style.display = 'none';
        document.getElementById('login-form').style.display = 'block';
    }

    // Show Register Form
    function showRegister() {
        document.getElementById('login-form').style.display = 'none';
        document.getElementById('register-form').style.display = 'block';
    }

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // Email Verification
    const urlParams = new URLSearchParams(window.location.search);
    const email = urlParams.get("email");
    const userEmailInput = document.getElementById("userEmail");

    if (userEmailInput) {
        userEmailInput.value = email ? email : "";
    }
  </script>
</body>
</html>

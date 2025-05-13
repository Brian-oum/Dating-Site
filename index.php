<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nutrition System | Login & Register</title>
  <link rel="stylesheet" href="./assets/css/style.css" />
</head>
<body>
  <div class="auth-wrapper">
    <div class="auth-container">
      <h2 class="auth-title">Nutrition Recommender System</h2>

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

  <script src="./assets/js/login.js"></script>
</body>
</html>

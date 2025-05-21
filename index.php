<?php
session_start();

// Set default values to avoid undefined variable errors
$id = $id ?? '';
$profile_photo = $profile_photo ?? '';
$username = $username ?? '';
$email = $email ?? '';
$password = $password ?? '';
$first_name = $first_name ?? '';
$last_name = $last_name ?? '';
$show_last_name = $show_last_name ?? '';
$age = $age ?? '';
$location = $location ?? '';
$gender = $gender ?? '';
$bio = $bio ?? '';

// Handle selected option for gender
$gender_options = [
    'Male' => ($gender === 'Male') ? 'selected' : '',
    'Female' => ($gender === 'Female') ? 'selected' : '',
    'Other' => ($gender === 'Other') ? 'selected' : '',
    'Prefer not to say' => ($gender === 'Prefer not to say') ? 'selected' : ''
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AfroLove | African Dating & Connections</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #ff6b6b;
      --primary-dark: #ff5252;
      --secondary: #4ecdc4;
      --dark: #2b2d42;
      --light: #f8f9fa;
      --gray: #6c757d;
      --success: #28a745;
      --danger: #dc3545;
      --warning: #fd7e14;
      --info: #17a2b8;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f5f5;
      color: var(--dark);
      line-height: 1.6;
    }

    .auth-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .auth-container {
      width: 100%;
      max-width: 500px;
      background: white;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .auth-title {
      text-align: center;
      padding: 2rem;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      font-size: 2rem;
      font-weight: 700;
      margin: 0;
    }

    .auth-form {
      padding: 2rem;
    }

    .auth-form h3 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: var(--dark);
      font-size: 1.5rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--dark);
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-family: inherit;
      font-size: 1rem;
      transition: all 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.2);
    }

    textarea {
      min-height: 120px;
      resize: vertical;
    }

    button[type="submit"],
    .form-button {
      width: 100%;
      padding: 0.75rem;
      border: none;
      border-radius: 8px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      margin-top: 1rem;
    }

    button[type="submit"]:hover,
    .form-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
    }

    .auth-form p {
      text-align: center;
      margin-top: 1.5rem;
      color: var(--gray);
    }

    .auth-form p a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }

    .auth-form p a:hover {
      text-decoration: underline;
    }

    .profile-photo-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 2rem;
    }

    .profile-photo-preview {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid white;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      margin-bottom: 1rem;
      background-color: #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .profile-photo-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .photo-upload-label {
      display: inline-block;
      padding: 0.5rem 1rem;
      background: var(--primary);
      color: white;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s;
      text-align: center;
      font-weight: 500;
    }

    .photo-upload-label:hover {
      background: var(--primary-dark);
    }

    .photo-upload-input {
      display: none;
    }

    .toggle-switch {
      display: flex;
      align-items: center;
      margin-top: 0.5rem;
    }

    .toggle-switch input[type="checkbox"] {
      height: 0;
      width: 0;
      visibility: hidden;
    }

    .toggle-label {
      cursor: pointer;
      width: 50px;
      height: 25px;
      background: #ddd;
      display: block;
      border-radius: 25px;
      position: relative;
      margin-right: 10px;
    }

    .toggle-label:after {
      content: '';
      position: absolute;
      top: 2px;
      left: 2px;
      width: 21px;
      height: 21px;
      background: white;
      border-radius: 50%;
      transition: 0.3s;
    }

    .toggle-switch input:checked + .toggle-label {
      background: var(--primary);
    }

    .toggle-switch input:checked + .toggle-label:after {
      left: calc(100% - 2px);
      transform: translateX(-100%);
    }

    .toggle-switch span {
      font-size: 0.9rem;
      color: var(--gray);
    }

    .alert-message {
      padding: 0.75rem 1rem;
      margin-bottom: 1.5rem;
      border-radius: 8px;
      background-color: var(--success);
      color: white;
      text-align: center;
      font-weight: 500;
      animation: fadeIn 0.3s ease;
    }

    /* Multi-step form styling */
    .form-steps {
      display: flex;
      justify-content: space-between;
      margin-bottom: 2rem;
      position: relative;
    }

    .form-steps::before {
      content: '';
      position: absolute;
      top: 15px;
      left: 0;
      right: 0;
      height: 2px;
      background: #e0e0e0;
      z-index: 1;
    }

    .step {
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      z-index: 2;
    }

    .step-number {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: #e0e0e0;
      color: #999;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      margin-bottom: 0.5rem;
    }

    .step.active .step-number {
      background: var(--primary);
      color: white;
    }

    .step.completed .step-number {
      background: var(--success);
      color: white;
    }

    .step-label {
      font-size: 0.8rem;
      color: #999;
    }

    .step.active .step-label {
      color: var(--dark);
      font-weight: 500;
    }

    /* Interest selection */
    .interests-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
      gap: 1rem;
      margin-top: 1rem;
    }

    .interest-option {
      display: none;
    }

    .interest-label {
      display: block;
      padding: 0.75rem;
      background: #f5f5f5;
      border-radius: 8px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s;
      border: 2px solid transparent;
    }

    .interest-option:checked + .interest-label {
      background: var(--primary);
      color: white;
      border-color: var(--primary-dark);
    }

    /* Form step transitions */
    .form-step {
      display: none;
      animation: fadeIn 0.5s ease;
    }

    .form-step.active {
      display: block;
    }

    /* Navigation buttons */
    .form-navigation {
      display: flex;
      justify-content: space-between;
      margin-top: 2rem;
    }

    .btn {
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
    }

    .btn-primary {
      background: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }

    .btn-outline {
      background: transparent;
      border: 2px solid var(--primary);
      color: var(--primary);
    }

    .btn-outline:hover {
      background: rgba(255, 107, 107, 0.1);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
      .auth-wrapper {
        padding: 1rem;
      }
      
      .auth-container {
        border-radius: 12px;
      }
      
      .auth-title {
        font-size: 1.75rem;
        padding: 1.5rem;
      }
      
      .auth-form {
        padding: 1.5rem;
      }
      
      .interests-container {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
      }
    }
     /* Password strength indicator */
    .password-strength {
      margin-top: 0.5rem;
      height: 5px;
      background: #e0e0e0;
      border-radius: 3px;
      overflow: hidden;
    }

    .strength-meter {
      height: 100%;
      width: 0;
      background: var(--danger);
      transition: all 0.3s;
    }

    .password-requirements {
      margin-top: 0.5rem;
      font-size: 0.8rem;
      color: var(--gray);
    }

    .requirement {
      display: flex;
      align-items: center;
      margin-bottom: 0.25rem;
    }

    .requirement i {
      margin-right: 0.5rem;
      font-size: 0.7rem;
    }

    .requirement.valid {
      color: var(--success);
    }

    /* Show/hide password toggle */
    .password-toggle {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: var(--gray);
    }

    .password-input-container {
      position: relative;
    }
  </style>
</head>
<body>
   <div class="auth-wrapper">
    <div class="auth-container">
      <h2 class="auth-title">AfroLove</h2>

      <?php
        if (isset($_SESSION['message'])) {
          echo "<div class='alert-message'>" . $_SESSION['message'] . "</div>";
          unset($_SESSION['message']);
        }
      ?>

      <!-- Registration Form -->
      <div class="auth-form auth-register" id="register-form" style="display: none;">
        <h3>Create Your Profile</h3>
        <form id="profileForm" method="POST" action="./pages/auth.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

           

            <!-- Step 1: Account Information -->
            <div class="form-step active" id="step-1">
              <div class="form-group">
                  <label for="username">Username</label>
                  <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" placeholder="Choose a username" required>
              </div>

              <div class="form-group">
                  <label for="email">Email Address</label>
                  <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Enter your email" required>
              </div>

              <div class="form-group">
                  <label for="password">Password</label>
                  <div class="password-input-container">
                      <input type="password" id="password" name="password" value="<?= htmlspecialchars($password) ?>" placeholder="Create a password" required>
                      <span class="password-toggle" id="passwordToggle">
                          <i class="far fa-eye"></i>
                      </span>
                  </div>
                  <div class="password-strength">
                      <div class="strength-meter" id="strengthMeter"></div>
                  </div>
                  <div class="password-requirements">
                      <div class="requirement" id="lengthReq">
                          <i class="far fa-circle"></i>
                          <span>At least 8 characters</span>
                      </div>
                      <div class="requirement" id="numberReq">
                          <i class="far fa-circle"></i>
                          <span>Contains a number</span>
                      </div>
                      <div class="requirement" id="specialReq">
                          <i class="far fa-circle"></i>
                          <span>Contains a special character</span>
                      </div>
                  </div>
              </div>

              <div class="form-navigation">
                <button type="button" class="btn btn-primary next-step" data-next="2">Continue</button>
              </div>
            </div>

            <!-- Step 2: Profile Information -->
            <div class="form-step" id="step-2">
              <div class="form-group profile-photo-container">
                  <?php if (!empty($profile_photo) && file_exists("../$profile_photo")): ?>
                      <img src="../<?= htmlspecialchars($profile_photo) ?>" class="profile-photo-preview" id="photoPreview">
                  <?php else: ?>
                      <div class="profile-photo-preview" id="photoPreview">
                          <svg width="40" height="40" viewBox="0 0 24 24" fill="#718096">
                              <path d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
                              <circle cx="12" cy="12" r="3"/>
                          </svg>
                      </div>
                  <?php endif; ?>
                  <label class="photo-upload-label">
                      <i class="fas fa-camera"></i> Upload Photo
                      <input type="file" name="profile_photo" class="photo-upload-input" id="photoUpload" accept="image/*">
                  </label>
              </div>

              <div class="form-group">
                  <label for="first_name">First Name</label>
                  <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>" placeholder="Enter your first name" required>
              </div>

              <div class="form-group">
                  <label for="last_name">Last Name</label>
                  <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>" placeholder="Enter your last name" required>
                  <div class="toggle-switch">
                      <input type="checkbox" id="show_last_name" name="show_last_name" <?= $show_last_name ? 'checked' : '' ?>>
                      <label for="show_last_name" class="toggle-label"></label>
                      <span>Show Last Name on Profile</span>
                  </div>
              </div>

              <div class="form-navigation">
                <button type="button" class="btn btn-outline prev-step" data-prev="1">Back</button>
                <button type="button" class="btn btn-primary next-step" data-next="3">Continue</button>
              </div>
            </div>

            <!-- Step 3: Personal Details -->
            <div class="form-step" id="step-3">
              <div class="form-group">
                  <label for="age">Age</label>
                  <input type="number" id="age" name="age" value="<?= htmlspecialchars($age) ?>" placeholder="Enter your age" min="18" max="120" required>
              </div>

              <div class="form-group">
                  <label for="location">Location</label>
                  <input type="text" id="location" name="location" value="<?= htmlspecialchars($location ?: '') ?>" placeholder="City, Country">
              </div>

              <div class="form-group">
                  <label for="gender">Gender</label>
                  <select id="gender" name="gender" required>
                      <option value="">Select your gender</option>
                      <option value="Male" <?= $gender_options['Male'] ?>>Male</option>
                      <option value="Female" <?= $gender_options['Female'] ?>>Female</option>
                      <option value="Other" <?= $gender_options['Other'] ?>>Other</option>
                      <option value="Prefer not to say" <?= $gender_options['Prefer not to say'] ?>>Prefer not to say</option>
                  </select>
              </div>

              <div class="form-group">
                  <label for="bio">About Me</label>
                  <textarea id="bio" name="bio" placeholder="Tell us about yourself, your interests, and what you're looking for"><?= htmlspecialchars($bio) ?></textarea>
              </div>

              <div class="form-navigation">
                <button type="button" class="btn btn-outline prev-step" data-prev="2">Back</button>
                <button type="submit" name = "register" class="btn btn-primary">Complete Registration</button>
              </div>
            </div>
        </form>
        <p>Already have an account? <a href="#" onclick="showLogin()">Sign In</a></p>
      </div>

      <!-- Login Form remains the same -->
      <div class="auth-form auth-login" id="login-form">
        <form action="./pages/auth.php" method="POST" class="form">
          <div class="form-group">
            <input class="form-input" type="text" name="identifier" placeholder="Email or Username" required />
          </div>
          <div class="form-group">
            <input class="form-input" type="password" name="password" placeholder="Password" required />
          </div>
          <button class="form-button" type="submit" name="login">Sign In</button>
        </form>
        <p><a href="#">Forgot Password?</a></p>
        <p>New to AfroLove? <a href="#" onclick="showRegister()">Create Account</a></p>
      </div>
    </div>
  </div>

  <script>
      function showLogin() {
        document.getElementById('register-form').style.display = 'none';
        document.getElementById('login-form').style.display = 'block';
    }

    function showRegister() {
        document.getElementById('login-form').style.display = 'none';
        document.getElementById('register-form').style.display = 'block';
    }

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // Preview uploaded photo
    document.getElementById('photoUpload')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('photoPreview');
                preview.innerHTML = '';
                preview.style.backgroundImage = 'none';
                const img = document.createElement('img');
                img.src = event.target.result;
                img.className = 'profile-photo-preview';
                preview.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    });

    // Password strength checker
    document.getElementById('password')?.addEventListener('input', function(e) {
        const password = e.target.value;
        const strengthMeter = document.getElementById('strengthMeter');
        const lengthReq = document.getElementById('lengthReq');
        const numberReq = document.getElementById('numberReq');
        const specialReq = document.getElementById('specialReq');
        
        // Reset all
        strengthMeter.style.width = '0%';
        strengthMeter.style.backgroundColor = 'var(--danger)';
        [lengthReq, numberReq, specialReq].forEach(el => {
            el.classList.remove('valid');
            el.querySelector('i').className = 'far fa-circle';
        });
        
        // Check requirements
        let strength = 0;
        
        // Length requirement
        if (password.length >= 8) {
            strength += 33;
            lengthReq.classList.add('valid');
            lengthReq.querySelector('i').className = 'fas fa-check-circle';
        }
        
        // Number requirement
        if (/\d/.test(password)) {
            strength += 33;
            numberReq.classList.add('valid');
            numberReq.querySelector('i').className = 'fas fa-check-circle';
        }
        
        // Special character requirement
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            strength += 34;
            specialReq.classList.add('valid');
            specialReq.querySelector('i').className = 'fas fa-check-circle';
        }
        
        // Update strength meter
        strengthMeter.style.width = strength + '%';
        
        // Change color based on strength
        if (strength > 66) {
            strengthMeter.style.backgroundColor = 'var(--success)';
        } else if (strength > 33) {
            strengthMeter.style.backgroundColor = 'var(--warning)';
        }
    });

    // Toggle password visibility
    document.getElementById('passwordToggle')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.className = 'far fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            icon.className = 'far fa-eye';
        }
    });

    // Multi-step form functionality
    document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', function() {
            const currentStep = this.closest('.form-step');
            const nextStepId = this.dataset.next;
            
            // Validate current step before proceeding
            let isValid = true;
            const inputs = currentStep.querySelectorAll('input[required], select[required], textarea[required]');
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = 'var(--danger)';
                    isValid = false;
                } else {
                    input.style.borderColor = '';
                }
            });
            
            // Special validation for email
            const emailInput = document.getElementById('email');
            if (emailInput && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
                emailInput.style.borderColor = 'var(--danger)';
                isValid = false;
                alert('Please enter a valid email address');
            }
     
            
            if (isValid) {
                currentStep.classList.remove('active');
                document.getElementById(`step-${nextStepId}`).classList.add('active');
                
                // Update step indicators
                document.querySelectorAll('.step').forEach(step => {
                    step.classList.remove('active');
                    if (parseInt(step.dataset.step) < parseInt(nextStepId)) {
                        step.classList.add('completed');
                    }
                });
                document.querySelector(`.step[data-step="${nextStepId}"]`).classList.add('active');
            }
        });
    });

    document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', function() {
            const currentStep = this.closest('.form-step');
            const prevStepId = this.dataset.prev;
            
            currentStep.classList.remove('active');
            document.getElementById(`step-${prevStepId}`).classList.add('active');
            
            // Update step indicators
            document.querySelectorAll('.step').forEach(step => {
                step.classList.remove('active');
                if (parseInt(step.dataset.step) <= parseInt(prevStepId)) {
                    step.classList.add('completed');
                }
            });
            document.querySelector(`.step[data-step="${prevStepId}"]`).classList.add('active');
        });
    });
  </script>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

include '../config/db.php';

$user_id = $_SESSION['user_id'];

// Get user data
$stmt = $pdo->prepare("SELECT * FROM account WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user settings
$stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
$stmt->execute([$user_id]);
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// If no settings exist, create default settings
if (!$settings) {
    $pdo->prepare("INSERT INTO user_settings (user_id) VALUES (?)")->execute([$user_id]);
    $stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Profile settings
    $show_age = isset($_POST['show_age']) ? 1 : 0;
    $show_location = isset($_POST['show_location']) ? 1 : 0;
    $profile_visibility = $_POST['profile_visibility'];
    $allow_messages_from = $_POST['allow_messages_from'];
    $distance_unit = $_POST['distance_unit'];
    
    // Notification settings
    $notification_email = isset($_POST['notification_email']) ? 1 : 0;
    $notification_push = isset($_POST['notification_push']) ? 1 : 0;
    $notification_sms = isset($_POST['notification_sms']) ? 1 : 0;
    
    // Appearance settings
    $theme_preference = $_POST['theme_preference'];
    $language = $_POST['language'];
    
    // Update settings in database
    $stmt = $pdo->prepare("UPDATE user_settings SET 
        show_age = ?, 
        show_location = ?, 
        profile_visibility = ?, 
        allow_messages_from = ?, 
        distance_unit = ?,
        theme_preference = ?,
        notification_email = ?,
        notification_push = ?,
        notification_sms = ?,
        language = ?
        WHERE user_id = ?");
    
    $success = $stmt->execute([
        $show_age,
        $show_location,
        $profile_visibility,
        $allow_messages_from,
        $distance_unit,
        $theme_preference,
        $notification_email,
        $notification_push,
        $notification_sms,
        $language,
        $user_id
    ]);
    
    if ($success) {
        $_SESSION['success_message'] = "Settings updated successfully!";
        // Refresh settings
        $stmt = $pdo->prepare("SELECT * FROM user_settings WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error_message'] = "Failed to update settings. Please try again.";
    }
    
    header("Location: settings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= htmlspecialchars($settings['theme_preference']) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings | Afro Love</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #6a3093;
      --primary-light: #a044ff;
      --primary-dark: #4a1d6b;
      --secondary: #ff7e5f;
      --dark: #2B2D42;
      --light: #F7F9FC;
      --gray: #E4E9F2;
      --text: #4A4A4A;
      --success: #4CAF50;
      --warning: #FFC107;
      --danger: #F44336;
    }

    /* Afro theme colors */
    [data-theme="afro"] {
      --primary: #8E44AD;
      --primary-light: #BB8FCE;
      --primary-dark: #5B2C6F;
      --secondary: #F39C12;
      --dark: #1B2631;
      --light: #F2F3F4;
      --text: #2C3E50;
    }

    /* Dark theme colors */
    [data-theme="dark"] {
      --primary: #6a3093;
      --primary-light: #9B59B6;
      --primary-dark: #4a1d6b;
      --secondary: #E67E22;
      --dark: #1A1A1A;
      --light: #2D2D2D;
      --gray: #3D3D3D;
      --text: #E0E0E0;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: var(--light);
      color: var(--text);
      line-height: 1.6;
    }

    .container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar styles */
    .sidebar {
      width: 250px;
      background-color: white;
      color: var(--text);
      display: flex;
      flex-direction: column;
      height: 100vh;
      position: sticky;
      top: 0;
      box-shadow: 2px 0 10px rgba(0,0,0,0.05);
    }

    .sidebar-header {
      padding: 1.5rem;
      border-bottom: 1px solid var(--gray);
    }

    .logo h2 {
      color: var(--primary);
      font-size: 1.5rem;
      font-weight: 700;
    }

    .sidebar-nav {
      flex: 1;
      padding: 1rem 0;
    }

    .sidebar-nav ul {
      list-style: none;
    }

    .nav-link {
      display: flex;
      align-items: center;
      padding: 0.75rem 1.5rem;
      color: var(--text);
      text-decoration: none;
      transition: all 0.2s;
    }

    .nav-link:hover {
      background-color: rgba(106, 48, 147, 0.1);
      color: var(--primary);
    }

    .nav-link.active {
      background-color: rgba(106, 48, 147, 0.1);
      color: var(--primary);
      border-left: 3px solid var(--primary);
    }

    .nav-link i {
      margin-right: 0.75rem;
      width: 20px;
      text-align: center;
    }

    .badge {
      margin-left: auto;
      background-color: var(--primary);
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
    }

    .sidebar-footer {
      border-top: 1px solid var(--gray);
      padding: 1rem;
    }

    .user-profile {
      display: flex;
      align-items: center;
      margin-bottom: 1rem;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      margin-right: 0.75rem;
      overflow: hidden;
    }

    .user-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .user-info {
      display: flex;
      flex-direction: column;
    }

    .username {
      font-weight: 500;
    }

    .user-status {
      font-size: 0.75rem;
      color: var(--success);
    }

    .logout-btn {
      display: flex;
      align-items: center;
      color: var(--text);
      text-decoration: none;
      padding: 0.5rem;
      border-radius: 4px;
      transition: background-color 0.2s;
    }

    .logout-btn:hover {
      background-color: rgba(244, 67, 54, 0.1);
      color: var(--danger);
    }

    .logout-btn i {
      margin-right: 0.5rem;
    }

    .main-content {
      flex: 1;
      padding: 2rem;
      overflow-y: auto;
    }

    .settings-container {
      max-width: 1000px;
      margin: 0 auto;
    }

    .settings-header {
      margin-bottom: 2rem;
    }

    .settings-header h1 {
      font-size: 2rem;
      color: var(--primary-dark);
      margin-bottom: 0.5rem;
    }

    .settings-tabs {
      display: flex;
      border-bottom: 1px solid var(--gray);
      margin-bottom: 2rem;
      overflow-x: auto;
    }

    .settings-tab {
      padding: 0.75rem 1.5rem;
      cursor: pointer;
      border-bottom: 3px solid transparent;
      font-weight: 500;
      white-space: nowrap;
    }

    .settings-tab.active {
      border-bottom-color: var(--primary);
      color: var(--primary);
    }

    .settings-content {
      display: none;
    }

    .settings-content.active {
      display: block;
    }

    .settings-section {
      background: white;
      border-radius: 8px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .settings-section h2 {
      font-size: 1.25rem;
      margin-bottom: 1rem;
      color: var(--primary-dark);
    }

    .form-group {
      margin-bottom: 1.25rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
    }

    .form-control {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--gray);
      border-radius: 6px;
      background-color: white;
      color: var(--text);
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary-light);
      box-shadow: 0 0 0 3px rgba(106, 48, 147, 0.1);
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      margin-bottom: 0.75rem;
    }

    .checkbox-group input {
      margin-right: 0.75rem;
    }

    .radio-group {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .radio-option {
      display: flex;
      align-items: center;
    }

    .radio-option input {
      margin-right: 0.75rem;
    }

    .btn {
      padding: 0.75rem 1.5rem;
      background-color: var(--primary);
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
      transition: background-color 0.2s;
    }

    .btn:hover {
      background-color: var(--primary-dark);
    }

    .btn-outline {
      background: transparent;
      border: 1px solid var(--primary);
      color: var(--primary);
    }

    .btn-outline:hover {
      background-color: rgba(106, 48, 147, 0.1);
    }

    .btn-danger {
      background-color: var(--danger);
    }

    .btn-danger:hover {
      background-color: #d32f2f;
    }

    .blocked-users-list {
      display: grid;
      gap: 1rem;
    }

    .blocked-user {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .blocked-user-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      background-color: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }

    .success-message {
      padding: 1rem;
      background-color: rgba(76, 175, 80, 0.2);
      color: var(--success);
      border-radius: 6px;
      margin-bottom: 1.5rem;
    }

    .error-message {
      padding: 1rem;
      background-color: rgba(244, 67, 54, 0.2);
      color: var(--danger);
      border-radius: 6px;
      margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }
      
      .sidebar {
        width: 100%;
        height: auto;
        position: relative;
      }
      
      .sidebar-nav ul {
        display: flex;
        overflow-x: auto;
        padding: 0.5rem;
      }
      
      .nav-link {
        flex-direction: column;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
      }
      
      .nav-link i {
        margin-right: 0;
        margin-bottom: 0.25rem;
      }
      
      .nav-link.active {
        border-left: none;
        border-bottom: 3px solid var(--primary);
      }
      
      .badge {
        margin-left: 0;
        margin-top: 0.25rem;
      }
      
      .main-content {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>
<div class="container">
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-header">
      <div class="logo">
        <h2>Afro Love</h2>
      </div>
    </div>
    
    <nav class="sidebar-nav">
      <ul>
        <li>
          <a href="dashboard.php" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li>
          <a href="profile.php" class="nav-link">
            <i class="fas fa-user"></i>
            <span>My Profile</span>
          </a>
        </li>
        <li>
          <a href="matches.php" class="nav-link">
            <i class="fas fa-heart"></i>
            <span>Matches</span>
          </a>
        </li>
        <li>
          <a href="messages.php" class="nav-link">
            <i class="fas fa-envelope"></i>
            <span>Messages</span>
            <span class="badge">3</span>
          </a>
        </li>
        <li>
          <a href="discover.php" class="nav-link">
            <i class="fas fa-search"></i>
            <span>Discover</span>
          </a>
        </li>
        <li>
          <a href="settings.php" class="nav-link active">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
          </a>
        </li>
      </ul>
    </nav>
    
    <div class="sidebar-footer">
      <div class="user-profile">
        <div class="user-avatar">
          <?php if (!empty($user['profile_pic'])): ?>
            <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="<?= htmlspecialchars($user['username']) ?>">
          <?php else: ?>
            <i class="fas fa-user"></i>
          <?php endif; ?>
        </div>
        <div class="user-info">
          <span class="username"><?= htmlspecialchars($user['username']) ?></span>
          <span class="user-status">Online</span>
        </div>
      </div>
      <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </aside>

  <div class="main-content">
    <div class="settings-container">
      <div class="settings-header">
        <h1>Settings</h1>
        <p>Manage your Afro Love account preferences</p>
      </div>

      <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message">
          <?= $_SESSION['success_message'] ?>
          <?php unset($_SESSION['success_message']); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message">
          <?= $_SESSION['error_message'] ?>
          <?php unset($_SESSION['error_message']); ?>
        </div>
      <?php endif; ?>

      <div class="settings-tabs">
        <div class="settings-tab active" data-tab="profile">Profile</div>
        <div class="settings-tab" data-tab="privacy">Privacy</div>
        <div class="settings-tab" data-tab="notifications">Notifications</div>
        <div class="settings-tab" data-tab="appearance">Appearance</div>
        <div class="settings-tab" data-tab="blocked">Blocked Users</div>
        <div class="settings-tab" data-tab="account">Account</div>
      </div>

      <!-- Main form for all settings -->
      <form method="POST" id="settings-form">
        <!-- Profile Settings -->
        <div class="settings-content active" id="profile-settings">
          <div class="settings-section">
            <h2>Profile Visibility</h2>
            
            <div class="form-group">
              <label>Show my age on profile</label>
              <div class="checkbox-group">
                <input type="checkbox" id="show_age" name="show_age" <?= $settings['show_age'] ? 'checked' : '' ?>>
                <label for="show_age">Display my age</label>
              </div>
            </div>
            
            <div class="form-group">
              <label>Show my location on profile</label>
              <div class="checkbox-group">
                <input type="checkbox" id="show_location" name="show_location" <?= $settings['show_location'] ? 'checked' : '' ?>>
                <label for="show_location">Display my general location (city)</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Privacy Settings -->
        <div class="settings-content" id="privacy-settings">
          <div class="settings-section">
            <h2>Privacy Settings</h2>
            
            <div class="form-group">
              <label>Profile Visibility</label>
              <div class="radio-group">
                <div class="radio-option">
                  <input type="radio" id="visibility-public" name="profile_visibility" value="public" <?= $settings['profile_visibility'] === 'public' ? 'checked' : '' ?>>
                  <label for="visibility-public">Public (visible to everyone)</label>
                </div>
                <div class="radio-option">
                  <input type="radio" id="visibility-members" name="profile_visibility" value="members" <?= $settings['profile_visibility'] === 'members' ? 'checked' : '' ?>>
                  <label for="visibility-members">Members only (visible to logged-in users)</label>
                </div>
                <div class="radio-option">
                  <input type="radio" id="visibility-matches" name="profile_visibility" value="matches" <?= $settings['profile_visibility'] === 'matches' ? 'checked' : '' ?>>
                  <label for="visibility-matches">Matches only (visible only to your matches)</label>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label>Who can message me</label>
              <div class="radio-group">
                <div class="radio-option">
                  <input type="radio" id="messages-everyone" name="allow_messages_from" value="everyone" <?= $settings['allow_messages_from'] === 'everyone' ? 'checked' : '' ?>>
                  <label for="messages-everyone">Everyone</label>
                </div>
                <div class="radio-option">
                  <input type="radio" id="messages-matches" name="allow_messages_from" value="matches" <?= $settings['allow_messages_from'] === 'matches' ? 'checked' : '' ?>>
                  <label for="messages-matches">Matches only</label>
                </div>
                <div class="radio-option">
                  <input type="radio" id="messages-none" name="allow_messages_from" value="none" <?= $settings['allow_messages_from'] === 'none' ? 'checked' : '' ?>>
                  <label for="messages-none">No one (disable messages)</label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Notification Settings -->
        <div class="settings-content" id="notification-settings">
          <div class="settings-section">
            <h2>Notification Preferences</h2>
            
            <div class="form-group">
              <label>Email Notifications</label>
              <div class="checkbox-group">
                <input type="checkbox" id="notification_email" name="notification_email" <?= $settings['notification_email'] ? 'checked' : '' ?>>
                <label for="notification_email">Receive email notifications</label>
              </div>
            </div>
            
            <div class="form-group">
              <label>Push Notifications</label>
              <div class="checkbox-group">
                <input type="checkbox" id="notification_push" name="notification_push" <?= $settings['notification_push'] ? 'checked' : '' ?>>
                <label for="notification_push">Receive push notifications</label>
              </div>
            </div>
            
            <div class="form-group">
              <label>SMS Notifications</label>
              <div class="checkbox-group">
                <input type="checkbox" id="notification_sms" name="notification_sms" <?= $settings['notification_sms'] ? 'checked' : '' ?>>
                <label for="notification_sms">Receive SMS notifications (standard rates may apply)</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Appearance Settings -->
        <div class="settings-content" id="appearance-settings">
          <div class="settings-section">
            <h2>Appearance</h2>
            
            <div class="form-group">
              <label for="theme_preference">Theme</label>
              <select id="theme_preference" name="theme_preference" class="form-control">
                <option value="light" <?= $settings['theme_preference'] === 'light' ? 'selected' : '' ?>>Light</option>
                <option value="dark" <?= $settings['theme_preference'] === 'dark' ? 'selected' : '' ?>>Dark</option>
                <option value="afro" <?= $settings['theme_preference'] === 'afro' ? 'selected' : '' ?>>Afro Theme</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="distance_unit">Distance Unit</label>
              <select id="distance_unit" name="distance_unit" class="form-control">
                <option value="miles" <?= $settings['distance_unit'] === 'miles' ? 'selected' : '' ?>>Miles</option>
                <option value="kilometers" <?= $settings['distance_unit'] === 'kilometers' ? 'selected' : '' ?>>Kilometers</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="language">Language</label>
              <select id="language" name="language" class="form-control">
                <option value="en" <?= $settings['language'] === 'en' ? 'selected' : '' ?>>English</option>
                <option value="fr" <?= $settings['language'] === 'fr' ? 'selected' : '' ?>>French</option>
                <option value="es" <?= $settings['language'] === 'es' ? 'selected' : '' ?>>Spanish</option>
                <option value="pt" <?= $settings['language'] === 'pt' ? 'selected' : '' ?>>Portuguese</option>
                <option value="sw" <?= $settings['language'] === 'sw' ? 'selected' : '' ?>>Swahili</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Save button for all settings -->
        <div class="settings-section">
          <button type="submit" class="btn">Save All Settings</button>
        </div>
      </form>

      <!-- Blocked Users Section -->
      <div class="settings-content" id="blocked-settings">
        <div class="settings-section">
          <h2>Blocked Users</h2>
          
          <?php if (count($blocked_users) > 0): ?>
            <div class="blocked-users-list">
              <?php foreach ($blocked_users as $blocked_user): ?>
                <div class="blocked-user">
                  <div class="blocked-user-info">
                    <?php if (!empty($blocked_user['profile_pic'])): ?>
                      <img src="<?= htmlspecialchars($blocked_user['profile_pic']) ?>" alt="<?= htmlspecialchars($blocked_user['username']) ?>" class="user-avatar">
                    <?php else: ?>
                      <div class="user-avatar">
                        <i class="fas fa-user"></i>
                      </div>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($blocked_user['username']) ?></span>
                  </div>
                  <button class="btn btn-outline unblock-btn" data-user-id="<?= $blocked_user['id'] ?>">Unblock</button>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p>You haven't blocked any users yet.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Account Settings Section -->
      <div class="settings-content" id="account-settings">
        <div class="settings-section">
          <h2>Account Management</h2>
          
          <div class="form-group">
            <label>Delete Account</label>
            <p>Permanently delete your Afro Love account and all associated data.</p>
            <button type="button" class="btn btn-danger" id="delete-account-btn">Delete My Account</button>
          </div>
          
          <div class="form-group">
            <label>Download Data</label>
            <p>Download a copy of all your data on Afro Love.</p>
            <button type="button" class="btn btn-outline">Request Data Download</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Tab switching
  document.querySelectorAll('.settings-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      // Remove active class from all tabs and content
      document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.settings-content').forEach(c => c.classList.remove('active'));
      
      // Add active class to clicked tab and corresponding content
      tab.classList.add('active');
      const tabId = tab.getAttribute('data-tab');
      document.getElementById(`${tabId}-settings`).classList.add('active');
    });
  });

  // Theme switcher preview
  const themeSelect = document.getElementById('theme_preference');
  if (themeSelect) {
    themeSelect.addEventListener('change', function() {
      document.documentElement.setAttribute('data-theme', this.value);
    });
  }

  // Unblock user
  document.querySelectorAll('.unblock-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const userId = this.getAttribute('data-user-id');
      if (confirm('Are you sure you want to unblock this user?')) {
        fetch('unblock_user.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `user_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            this.closest('.blocked-user').remove();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while unblocking the user.');
        });
      }
    });
  });

  // Delete account confirmation
  document.getElementById('delete-account-btn')?.addEventListener('click', function() {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
      fetch('delete_account.php', {
        method: 'POST'
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.href = '../index.php';
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting your account.');
      });
    }
  });
</script>
</body>
</html>
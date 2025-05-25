<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
include '../config/db.php';

// Simulate user login (use real session ID in production)
$user_id = $_SESSION['user_id'] ?? 1;

$stmt = $pdo->prepare("SELECT * FROM account WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard | Dating Site</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="navbar">
  <div class="navbar-brand">
    <i class="fas fa-heart"></i>
    <span>LoveConnect</span>
  </div>
  <div class="navbar-actions">
    <div class="user-menu">
      <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="User" class="user-avatar">
      <span><?= htmlspecialchars($user['username']) ?></span>
      <i class="fas fa-chevron-down"></i>
      <div class="dropdown-menu">
        <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
  </div>
</div>

<div class="dashboard-container">
  <div class="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-user">
        <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="User" class="sidebar-user-avatar">
        <div class="sidebar-user-info">
          <h4><?= htmlspecialchars($user['username']) ?></h4>
          <p>Premium Member</p>
        </div>
      </div>
    </div>
    <ul class="sidebar-menu">
      <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
      <li><a href="profile.php"><i class="fas fa-user-edit"></i> <span>Edit Profile</span></a></li>
      <li><a href="matches.php"><i class="fas fa-heart"></i> <span>Matches</span></a></li>
      <li><a href="messages.php"><i class="fas fa-envelope"></i> <span>Messages</span> <span class="badge badge-primary">3</span></a></li>
      <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
    </ul>
  </div>

  <div class="main-content">
    <div class="welcome-banner">
      <div class="welcome-text">
        <h2>Welcome back, <?= htmlspecialchars($user['username']) ?>!</h2>
        <p>You have 3 new matches and 5 unread messages</p>
      </div>
      <button class="btn btn-outline" style="background: rgba(255,255,255,0.2); color: white; border-color: white;">
        <i class="fas fa-bolt"></i> Boost Profile
      </button>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-header">
          <div class="stat-card-title">Total Matches</div>
          <div class="stat-card-icon" style="background-color: var(--primary);">
            <i class="fas fa-heart"></i>
          </div>
        </div>
        <div class="stat-card-value">24</div>
        <div class="stat-card-diff positive">
          <i class="fas fa-arrow-up"></i> 12% from last week
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-header">
          <div class="stat-card-title">Messages</div>
          <div class="stat-card-icon" style="background-color: var(--secondary);">
            <i class="fas fa-envelope"></i>
          </div>
        </div>
        <div class="stat-card-value">15</div>
        <div class="stat-card-diff positive">
          <i class="fas fa-arrow-up"></i> 5 new today
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-header">
          <div class="stat-card-title">Profile Views</div>
          <div class="stat-card-icon" style="background-color: #6A5ACD;">
            <i class="fas fa-eye"></i>
          </div>
        </div>
        <div class="stat-card-value">143</div>
        <div class="stat-card-diff negative">
          <i class="fas fa-arrow-down"></i> 3% from last week
        </div>
      </div>
    </div>
    <div class="matches-section">
      <div class="section-header">
        <h3 class="section-title">Recent Messages</h3>
        <a href="messages.php" class="view-all">View All</a>
      </div>
      <!-- Message list would go here -->
    </div>
  </div>
</div>

</body>
</html>

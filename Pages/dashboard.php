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
  <style>
    :root {
      --primary: #FF5A5F;
      --secondary: #00A699;
      --dark: #2B2D42;
      --light: #F7F9FC;
      --gray: #E4E9F2;
      --text: #4A4A4A;
      --success: #4CAF50;
      --warning: #FFC107;
      --danger: #F44336;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', 'Roboto', sans-serif;
      background-color: var(--light);
      color: var(--text);
      line-height: 1.6;
    }

    .navbar {
      background-color: var(--primary);
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .navbar-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .user-menu {
      position: relative;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      cursor: pointer;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid white;
    }

    .dropdown-menu {
      position: absolute;
      top: 100%;
      right: 0;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      width: 200px;
      padding: 0.5rem 0;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
      z-index: 100;
    }

    .user-menu:hover .dropdown-menu {
      opacity: 1;
      visibility: visible;
    }

    .dropdown-menu a {
      display: block;
      padding: 0.75rem 1.5rem;
      color: var(--text);
      text-decoration: none;
      transition: all 0.2s;
    }

    .dropdown-menu a:hover {
      background-color: var(--gray);
      color: var(--primary);
    }

    .dashboard-container {
      display: flex;
      min-height: calc(100vh - 68px);
    }

    .sidebar {
      width: 250px;
      background-color: white;
      padding: 1.5rem 0;
      box-shadow: 2px 0 10px rgba(0,0,0,0.05);
      position: relative;
      z-index: 10;
    }

    .sidebar-header {
      padding: 0 1.5rem 1.5rem;
      border-bottom: 1px solid var(--gray);
      margin-bottom: 1rem;
    }

    .sidebar-user {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .sidebar-user-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--primary);
    }

    .sidebar-user-info h4 {
      font-size: 1rem;
      margin-bottom: 0.25rem;
    }

    .sidebar-user-info p {
      font-size: 0.8rem;
      color: #777;
    }

    .sidebar-menu {
      list-style: none;
    }

    .sidebar-menu li a {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 0.9rem 1.5rem;
      color: var(--text);
      text-decoration: none;
      transition: all 0.2s;
      font-weight: 500;
    }

    .sidebar-menu li a:hover, 
    .sidebar-menu li a.active {
      background-color: var(--gray);
      color: var(--primary);
      border-left: 4px solid var(--primary);
    }

    .sidebar-menu li a i {
      width: 24px;
      text-align: center;
    }

    .main-content {
      flex: 1;
      padding: 2rem;
      background-color: var(--light);
    }

    .welcome-banner {
      background: linear-gradient(135deg, var(--primary), #FF7E82);
      color: white;
      padding: 2rem;
      border-radius: 12px;
      margin-bottom: 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .welcome-text h2 {
      font-size: 1.8rem;
      margin-bottom: 0.5rem;
    }

    .welcome-text p {
      opacity: 0.9;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: white;
      padding: 1.5rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      transition: transform 0.3s;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .stat-card-title {
      font-size: 0.9rem;
      color: #777;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .stat-card-icon {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }

    .stat-card-value {
      font-size: 2rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
    }

    .stat-card-diff {
      font-size: 0.8rem;
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }

    .stat-card-diff.positive {
      color: var(--success);
    }

    .stat-card-diff.negative {
      color: var(--danger);
    }

    .matches-section {
      background: white;
      padding: 1.5rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      margin-bottom: 2rem;
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .section-title {
      font-size: 1.3rem;
      font-weight: 600;
    }

    .view-all {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
    }

    .matches-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 1.5rem;
    }

    .match-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      transition: all 0.3s;
      position: relative;
    }

    .match-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }

    .match-card-img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .match-card-body {
      padding: 1rem;
    }

    .match-card-name {
      font-weight: 600;
      margin-bottom: 0.25rem;
    }

    .match-card-age {
      color: #777;
      font-size: 0.9rem;
    }

    .match-card-actions {
      display: flex;
      justify-content: space-between;
      margin-top: 0.5rem;
    }

    .btn {
      padding: 0.5rem 1rem;
      border-radius: 8px;
      border: none;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-primary {
      background-color: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background-color: #E04A4F;
    }

    .btn-outline {
      background: transparent;
      border: 1px solid var(--gray);
    }

    .btn-outline:hover {
      background-color: var(--gray);
    }

    .badge {
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .badge-primary {
      background-color: var(--primary);
      color: white;
    }

    .badge-online {
      position: absolute;
      top: 10px;
      right: 10px;
      background-color: var(--success);
      color: white;
      padding: 0.25rem 0.5rem;
      border-radius: 50px;
      font-size: 0.7rem;
    }

    @media (max-width: 992px) {
      .sidebar {
        width: 70px;
        overflow: hidden;
      }
      
      .sidebar-header, .sidebar-user-info, .sidebar-menu li a span {
        display: none;
      }
      
      .sidebar-menu li a {
        justify-content: center;
        padding: 1rem 0;
      }
    }

    @media (max-width: 768px) {
      .dashboard-container {
        flex-direction: column;
      }
      
      .sidebar {
        width: 100%;
        display: flex;
        padding: 0;
      }
      
      .sidebar-header {
        display: none;
      }
      
      .sidebar-menu {
        display: flex;
        width: 100%;
      }
      
      .sidebar-menu li {
        flex: 1;
        text-align: center;
      }
      
      .sidebar-menu li a {
        flex-direction: column;
        padding: 0.75rem 0.5rem;
        font-size: 0.8rem;
      }
      
      .sidebar-menu li a i {
        font-size: 1.2rem;
        margin-bottom: 0.25rem;
      }
      
      .welcome-banner {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
      }
    }
  </style>
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

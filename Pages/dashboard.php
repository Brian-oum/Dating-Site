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
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
    }

    .navbar {
      background-color: #4A90E2;
      color: white;
      padding: 1rem;
      text-align: center;
      font-size: 1.5rem;
      font-weight: bold;
    }

    .dashboard-container {
      display: flex;
      min-height: 100vh;
    }

    .sidebar {
      width: 220px;
      background-color: #2c3e50;
      padding-top: 2rem;
      color: white;
    }

    .sidebar a {
      display: block;
      padding: 1rem 2rem;
      color: white;
      text-decoration: none;
      transition: background 0.3s;
    }

    .sidebar a:hover {
      background-color: #34495e;
    }

    .main-content {
      flex: 1;
      padding: 2rem;
    }

    .card {
      background: white;
      padding: 1.5rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      max-width: 700px;
      margin: auto;
    }

    .card h2 {
      margin-top: 0;
    }
  </style>
</head>
<body>

<div class="navbar">Dating Site Dashboard</div>

<div class="dashboard-container">
  <div class="sidebar">
    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Edit Profile</a>
    <a href="matches.php">Matches</a>
    <a href="messages.php">Messages</a>
    <a href="logout.php">Logout</a>
  </div>

  <div class="main-content">
    <div class="card">
      <h2>Welcome, <?= htmlspecialchars($user['username']) ?>!</h2>
      <p>This is your dashboard where you can manage your dating profile, see your matches, and send messages.</p>
      <p><strong>Location:</strong> <?= htmlspecialchars($user['location'] ?? 'Not Specified') ?></p>
    </div>
  </div>
</div>

</body>
</html>

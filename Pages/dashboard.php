<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
include '../config/db.php';

// Get logged in user data
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM account WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user stats
$stats = [
    'total_matches' => 0,
    'unread_messages' => 0,
    'profile_views' => 0,
    'new_matches' => 0
];

// Get total matches
$stmt = $pdo->prepare("SELECT COUNT(*) FROM matches WHERE user1_id = ? OR user2_id = ?");
$stmt->execute([$user_id, $user_id]);
$stats['total_matches'] = $stmt->fetchColumn();

// Get unread messages
$stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ?");
$stmt->execute([$user_id]);
$stats['unread_messages'] = $stmt->fetchColumn();


// Get new matches (last 7 days)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM matches 
                      WHERE (user1_id = ? OR user2_id = ?) 
                      AND match_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stmt->execute([$user_id, $user_id]);
$stats['new_matches'] = $stmt->fetchColumn();

// Get recent messages (last 5)
$stmt = $pdo->prepare("
    SELECT 
        m.*, 
        a.username AS sender_name, 
        u.profile_photo AS sender_pic
    FROM messages m
    JOIN account a ON m.sender_id = a.id
    JOIN users u ON u.account_id = a.id
    WHERE m.receiver_id = ?
    LIMIT 5
");
$stmt->execute([$user_id]);
$recent_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get weekly stats for charts
$weekly_stats = [
    'matches' => [],
    'messages' => [],
    'views' => []
];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    
    // Matches
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM matches 
                          WHERE (user1_id = ? OR user2_id = ?) 
                          AND DATE(match_date) = ?");
    $stmt->execute([$user_id, $user_id, $date]);
    $weekly_stats['matches'][$date] = $stmt->fetchColumn();
    
    // Messages
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM messages 
                            WHERE receiver_id = ? 
                            AND timestamp > ?");
    $stmt->execute([$user_id, $date]);
    $weekly_stats['messages'][$date] = $stmt->fetchColumn();
    

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard | Dating Site</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .chart-container {
      background: white;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .message-item {
      display: flex;
      align-items: center;
      padding: 15px;
      background: white;
      border-radius: 8px;
      margin-bottom: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
      transition: transform 0.2s;
    }
    .message-item:hover {
      transform: translateY(-2px);
    }
    .message-avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
    }
    .message-content {
      flex: 1;
    }
    .message-sender {
      font-weight: 600;
      margin-bottom: 5px;
    }
    .message-preview {
      color: #666;
      font-size: 0.9em;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .message-time {
      color: #999;
      font-size: 0.8em;
    }
    .unread-badge {
      background: #4361ee;
      color: white;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.7em;
      margin-left: 10px;
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
      <img src="<?= htmlspecialchars($user['profile_photo'] ?? 'default.jpg') ?>" alt="User" class="sidebar-user-avatar">
      <span><?= htmlspecialchars($user['username']) ?></span>
      <i class="fas fa-chevron-down"></i>
      <div class="dropdown-menu">
        <a href="view_profile.php"><i class="fas fa-user"></i> My Profile</a>
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
        <img src="<?= htmlspecialchars($user['profile_photo'] ?? 'default.jpg') ?>" alt="User" class="sidebar-user-avatar">
      </div>
    </div>
    <ul class="sidebar-menu">
      <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
      <li><a href="profile.php"><i class="fas fa-user-edit"></i> <span>Edit Profile</span></a></li>
      <li><a href="matches.php"><i class="fas fa-heart"></i> <span>Matches</span></a></li>
      <li><a href="messages.php"><i class="fas fa-envelope"></i> <span>Messages</span> 
          <?php if ($stats['unread_messages'] > 0): ?>
            <span class="badge badge-primary"><?= $stats['unread_messages'] ?></span>
          <?php endif; ?>
          </a></li>
      <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
    </ul>
  </div>

  <div class="main-content">
    <div class="welcome-banner">
      <div class="welcome-text">
        <h2>Welcome back, <?= htmlspecialchars($user['username']) ?>!</h2>
        <p>You have <?= $stats['new_matches'] ?> new matches and <?= $stats['unread_messages'] ?> unread messages</p>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-header">
          <div class="stat-card-title">Total Matches</div>
          <div class="stat-card-icon" style="background-color: var(--primary);">
            <i class="fas fa-heart"></i>
          </div>
        </div>
        <div class="stat-card-value"><?= $stats['total_matches'] ?></div>
        <div class="stat-card-diff positive">
          <i class="fas fa-arrow-up"></i>
          <?= ($stats['total_matches'] != 0) ? round(($stats['new_matches'] / $stats['total_matches']) * 100) : 0 ?>% from last week
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-header">
          <div class="stat-card-title">Messages</div>
          <div class="stat-card-icon" style="background-color: var(--secondary);">
            <i class="fas fa-envelope"></i>
          </div>
        </div>
        <div class="stat-card-value"><?= $stats['unread_messages'] ?></div>
        <div class="stat-card-diff positive">
          <i class="fas fa-arrow-up"></i> <?= $stats['unread_messages'] > 0 ? $stats['unread_messages'].' new' : 'No new' ?> today
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Weekly activity chart
  const ctx = document.getElementById('weeklyChart').getContext('2d');
  const weeklyChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: <?= json_encode(array_keys($weekly_stats['matches'])) ?>,
      datasets: [
        {
          label: 'Matches',
          data: <?= json_encode(array_values($weekly_stats['matches'])) ?>,
          borderColor: '#4361ee',
          backgroundColor: 'rgba(67, 97, 238, 0.1)',
          tension: 0.3,
          fill: true
        },
        {
          label: 'Messages',
          data: <?= json_encode(array_values($weekly_stats['messages'])) ?>,
          borderColor: '#4895ef',
          backgroundColor: 'rgba(72, 149, 239, 0.1)',
          tension: 0.3,
          fill: true
        },
        {
          label: 'Profile Views',
          data: <?= json_encode(array_values($weekly_stats['views'])) ?>,
          borderColor: '#6A5ACD',
          backgroundColor: 'rgba(106, 90, 205, 0.1)',
          tension: 0.3,
          fill: true
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
        tooltip: {
          mode: 'index',
          intersect: false,
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      }
    }
  });
</script>

</body>
</html>
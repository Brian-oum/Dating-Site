<?php
session_start();
require_once './match_algorithm';
require_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$userId = $_SESSION['user_id'];
$matchSystem = new MatchSystem($pdo);
$matches = $matchSystem->getUserMatches($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AfroLove - Your Matches</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Header Styles */
        .header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            font-family: 'Poppins', sans-serif;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #e83e8c;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 10px;
        }
        
        /* User Menu with Dropdown */
        .user-menu {
            position: relative;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e83e8c;
        }
        
        .username {
            font-weight: 500;
        }
        
        .dropdown-arrow {
            transition: transform 0.3s ease;
        }
        
        .user-menu:hover .dropdown-arrow {
            transform: rotate(180deg);
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 1001;
        }
        
        .user-menu:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
        }
        
        .dropdown-menu a:hover {
            background-color: #f8f9fa;
            color: #e83e8c;
        }
        
        .dropdown-menu a i {
            width: 20px;
            text-align: center;
        }
        
        .dropdown-divider {
            border-top: 1px solid #eee;
            margin: 5px 0;
        }

        /* Main Content Styles */
        .matches-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-title {
            text-align: center;
            color: #e83e8c;
            margin-bottom: 30px;
            font-size: 2.2rem;
        }
        
        .no-matches {
            text-align: center;
            padding: 50px 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .no-matches h2 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .no-matches p {
            color: #666;
            margin-bottom: 25px;
            font-size: 1.1rem;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: #e83e8c;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #d2337d;
            transform: translateY(-2px);
        }
        
        .matches-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .match-card {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .match-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }
        
        .match-image-container {
            position: relative;
            height: 250px;
            overflow: hidden;
        }
        
        .match-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .match-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #e83e8c;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .match-info {
            padding: 20px;
        }
        
        .match-name {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1.3rem;
        }
        
        .match-age-location {
            color: #666;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .match-bio {
            color: #555;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .match-interests {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .interest-tag {
            background-color: #f8e1eb;
            color: #e83e8c;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .match-actions {
            display: flex;
            border-top: 1px solid #eee;
            padding: 15px;
            gap: 10px;
        }
        
        .match-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .message-btn {
            background-color: #e83e8c;
            color: white;
        }
        
        .message-btn:hover {
            background-color: #d2337d;
        }
        
        .view-btn {
            background-color: #f0f0f0;
            color: #333;
        }
        
        .view-btn:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
    <!-- Header Section with Dropdown Navigation -->
    <header class="header">
        <a href="./dashboard.php" class="logo">
            <i class="fas fa-heart"></i>
            AfroLove
        </a>
        
        <div class="user-menu">
            <img src="<?= htmlspecialchars($_SESSION['user_photo'] ?? 'images/default-profile.jpg') ?>" 
                 alt="User" 
                 class="user-avatar">
            <span class="username"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
            <i class="fas fa-chevron-down dropdown-arrow"></i>
            
            <div class="dropdown-menu">
                <a href="./discover.php"><i class="fas fa-search"></i> Discover</a>
                <a href="./matches.php"><i class="fas fa-heart"></i> Matches</a>
                <a href="./messages.php"><i class="fas fa-comments"></i> Messages</a>
                <a href="./profile.php"><i class="fas fa-user"></i> Profile</a>
                <div class="dropdown-divider"></div>
                <a href="./settings.php"><i class="fas fa-cog"></i> Settings</a>
                <a href="./logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </header>
    
    <div class="matches-container">
        <h1 class="page-title">Your Matches</h1>
        
        <?php if (empty($matches)): ?>
            <div class="no-matches">
                <h2>You don't have any matches yet</h2>
                <p>Start discovering people to find your perfect match!</p>
                <a href="./discover.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Discover People
                </a>
            </div>
        <?php else: ?>
            <div class="matches-grid">
                <?php foreach ($matches as $match): ?>
                    <div class="match-card">
                        <div class="match-image-container">
                            <img src="<?= htmlspecialchars($match['profile_photo'] ?? 'images/default-profile.jpg') ?>" 
                                 alt="<?= htmlspecialchars($match['username']) ?>" 
                                 class="match-image">
                            <span class="match-badge">New Match</span>
                        </div>
                        
                        <div class="match-info">
                            <h3 class="match-name"><?= htmlspecialchars($match['username']) ?></h3>
                            <div class="match-age-location">
                                <i class="fas fa-user"></i>
                                <?= htmlspecialchars($match['age'] ?? 'N/A') ?> • <?= htmlspecialchars($match['location'] ?? 'Unknown') ?>
                            </div>
                            
                            <?php if (!empty($match['bio'])): ?>
                                <p class="match-bio"><?= htmlspecialchars($match['bio']) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($match['interests'])): ?>
                                <div class="match-interests">
                                    <?php 
                                    $interests = explode(',', $match['interests']);
                                    foreach (array_slice($interests, 0, 3) as $interest): 
                                    ?>
                                        <span class="interest-tag"><?= htmlspecialchars(trim($interest)) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($interests) > 3): ?>
                                        <span class="interest-tag">+<?= count($interests) - 3 ?> more</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="match-actions">
                            <button class="match-btn message-btn">
                                <i class="fas fa-comment"></i> Message
                            </button>
                            <button class="match-btn view-btn">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
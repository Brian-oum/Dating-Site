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

// Handle like/dislike actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['target_user_id'])) {
    $action = $_POST['action'];
    $targetUserId = (int)$_POST['target_user_id'];
    
    if (in_array($action, ['like', 'dislike', 'superlike'])) {
        $success = $matchSystem->recordAction($userId, $targetUserId, $action);
        
        // Return JSON response for AJAX requests
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit();
        }
    }
}

// Get potential matches
$potentialMatches = $matchSystem->getPotentialMatches($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AfroLove - Discover</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #e83e8c;
            --primary-dark: #d2337d;
            --secondary-color: #6c757d;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --white: #ffffff;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        /* Header Styles */
        .header {
            background-color: var(--white);
            box-shadow: var(--shadow);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
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
            border: 2px solid var(--primary-color);
        }
        
        .username {
            font-weight: 500;
        }
        
        .dropdown-arrow {
            transition: var(--transition);
        }
        
        .user-menu:hover .dropdown-arrow {
            transform: rotate(180deg);
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--white);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 10px 0;
            min-width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: var(--transition);
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
            color: var(--dark-color);
            text-decoration: none;
            transition: var(--transition);
            font-size: 14px;
        }
        
        .dropdown-menu a:hover {
            background-color: var(--light-color);
            color: var(--primary-color);
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
        .discover-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .no-matches {
            text-align: center;
            padding: 50px 20px;
            background-color: var(--white);
            border-radius: 10px;
            box-shadow: var(--shadow);
        }
        
        .no-matches h2 {
            color: var(--dark-color);
            margin-bottom: 15px;
            font-size: 1.8rem;
        }
        
        .no-matches p {
            color: var(--secondary-color);
            margin-bottom: 25px;
            font-size: 1.1rem;
        }
        
        /* Profile Card Styles */
        .profile-card {
            background-color: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 25px;
            transition: var(--transition);
            position: relative;
        }
        
        .profile-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            display: block;
        }
        
        .profile-info {
            padding: 20px;
        }
        
        .profile-name {
            color: var(--dark-color);
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .profile-age-location {
            color: var(--secondary-color);
            margin-bottom: 15px;
            font-size: 1rem;
        }
        
        .profile-bio {
            color: #555;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: space-around;
            padding: 15px;
            background-color: var(--white);
            border-top: 1px solid #eee;
        }
        
        .action-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            font-size: 24px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .dislike-btn {
            background-color: #fff;
            color: #ff4757;
            border: 2px solid #ff4757;
        }
        
        .dislike-btn:hover {
            background-color: #ff4757;
            color: white;
            transform: scale(1.1);
        }
        
        .superlike-btn {
            background-color: #fff;
            color: #1e90ff;
            border: 2px solid #1e90ff;
        }
        
        .superlike-btn:hover {
            background-color: #1e90ff;
            color: white;
            transform: scale(1.1);
        }
        
        .like-btn {
            background-color: #fff;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }
        
        .like-btn:hover {
            background-color: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                padding: 12px 20px;
            }
            
            .logo {
                font-size: 20px;
            }
            
            .discover-container {
                margin: 20px auto;
                padding: 0 15px;
            }
            
            .profile-image {
                height: 350px;
            }
            
            .action-btn {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .profile-image {
                height: 300px;
            }
            
            .profile-name {
                font-size: 1.3rem;
            }
            
            .action-buttons {
                padding: 10px;
            }
            
            .action-btn {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
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
    
    <div class="discover-container">
        <?php if (empty($potentialMatches)): ?>
            <div class="no-matches">
                <h2>No more profiles to show right now</h2>
                <p>Check back later or adjust your preferences to see more matches.</p>
            </div>
        <?php else: ?>
            <?php foreach ($potentialMatches as $profile): ?>
                <div class="profile-card" data-user-id="<?= htmlspecialchars($profile['id']) ?>">
                    <img src="<?= htmlspecialchars($profile['profile_photo'] ?? 'images/default-profile.jpg') ?>" 
                         alt="<?= htmlspecialchars($profile['username']) ?>" 
                         class="profile-image">
                    
                    <div class="profile-info">
                        <h2 class="profile-name">
                            <?= htmlspecialchars($profile['username']) ?>
                        </h2>
                        <div class="profile-age-location">
                            <?= htmlspecialchars($profile['age'] ?? 'N/A') ?> • <?= htmlspecialchars($profile['location'] ?? 'Unknown') ?>
                        </div>
                        <p class="profile-bio"><?= htmlspecialchars($profile['bio'] ?? 'No bio yet') ?></p>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="action-btn dislike-btn" data-action="dislike">✖</button>
                        <button class="action-btn superlike-btn" data-action="superlike">★</button>
                        <button class="action-btn like-btn" data-action="like">❤</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileCards = document.querySelectorAll('.profile-card');
            
            profileCards.forEach(card => {
                const userId = card.dataset.userId;
                const actionButtons = card.querySelectorAll('.action-btn');
                
                actionButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const action = this.dataset.action;
                        
                        // Add animation class
                        card.style.transition = 'transform 0.3s ease, opacity 0.3s ease';
                        
                        // Send AJAX request
                        fetch('discover.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `action=${action}&target_user_id=${userId}&ajax=1`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Slide card out with direction based on action
                                card.style.transform = action === 'like' || action === 'superlike' 
                                    ? 'translateX(100%) rotate(15deg)' 
                                    : 'translateX(-100%) rotate(-15deg)';
                                card.style.opacity = '0';
                                
                                // Remove card after animation
                                setTimeout(() => {
                                    card.remove();
                                    
                                    // Check if no cards left
                                    if (document.querySelectorAll('.profile-card').length === 0) {
                                        document.querySelector('.discover-container').innerHTML = `
                                            <div class="no-matches">
                                                <h2>No more profiles to show right now</h2>
                                                <p>Check back later or adjust your preferences to see more matches.</p>
                                            </div>
                                        `;
                                    }
                                }, 300);
                            }
                        });
                    });
                });
            });
            
            // Add swipe functionality for mobile
            let touchStartX = 0;
            let touchEndX = 0;
            
            profileCards.forEach(card => {
                card.addEventListener('touchstart', e => {
                    touchStartX = e.changedTouches[0].screenX;
                }, false);
                
                card.addEventListener('touchend', e => {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe(card);
                }, false);
            });
            
            function handleSwipe(card) {
                const userId = card.dataset.userId;
                const difference = touchStartX - touchEndX;
                
                if (Math.abs(difference) > 50) { // Only if significant swipe
                    const action = difference > 0 ? 'like' : 'dislike';
                    
                    // Add animation class
                    card.style.transition = 'transform 0.3s ease, opacity 0.3s ease';
                    
                    fetch('discover.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=${action}&target_user_id=${userId}&ajax=1`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            card.style.transform = action === 'like' 
                                ? 'translateX(100%) rotate(15deg)' 
                                : 'translateX(-100%) rotate(-15deg)';
                            card.style.opacity = '0';
                            setTimeout(() => card.remove(), 300);
                        }
                    });
                }
            }
        });
    </script>
</body>
</html>
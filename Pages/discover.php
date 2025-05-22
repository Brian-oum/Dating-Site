<?php
session_start();
require_once './match_algorithm';
require_once '../config/db.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    <link rel="stylesheet" href="css/style.css">
    <style>
        .discover-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 20px;
            position: relative;
        }
        
        .profile-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        
        .profile-info {
            padding: 20px;
        }
        
        .profile-name {
            font-size: 24px;
            margin: 0;
            color: #333;
        }
        
        .profile-age-location {
            color: #666;
            margin: 5px 0 15px;
        }
        
        .profile-bio {
            color: #444;
            line-height: 1.5;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 15px;
        }
        
        .action-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            transition: all 0.3s;
        }
        
        .dislike-btn {
            background: #fff;
            color: #ff4757;
        }
        
        .like-btn {
            background: #fff;
            color: #1e90ff;
        }
        
        .superlike-btn {
            background: #fff;
            color: #7d5fff;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
        
        .no-matches {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .match-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #ff6b6b;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
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
                                // Slide card out
                                card.style.transform = 'translateX(' + 
                                    (action === 'like' || action === 'superlike' ? '100%' : '-100%') + ')';
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
                
                if (difference > 50) { // Swipe left
                    // Like action
                    fetch('discover.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=like&target_user_id=${userId}&ajax=1`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            card.style.transform = 'translateX(-100%)';
                            card.style.opacity = '0';
                            setTimeout(() => card.remove(), 300);
                        }
                    });
                } else if (difference < -50) { // Swipe right
                    // Dislike action
                    fetch('discover.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=dislike&target_user_id=${userId}&ajax=1`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            card.style.transform = 'translateX(100%)';
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
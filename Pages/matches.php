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
        :root {
            --primary: #ff6b6b;
            --primary-dark: #ff5252;
            --secondary: #4ecdc4;
            --dark: #2b2d42;
            --light: #f8f9fa;
            --gray: #6c757d;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f7;
            color: var(--dark);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .matches-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        .page-title {
            color: var(--primary);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.2rem;
            position: relative;
            padding-bottom: 15px;
        }

        .page-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--primary);
        }

        .matches-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            padding: 10px;
        }

        .match-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
        }

        .match-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.15);
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
            transition: transform 0.5s ease;
        }

        .match-card:hover .match-image {
            transform: scale(1.05);
        }

        .match-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .match-info {
            padding: 20px;
        }

        .match-name {
            margin: 0;
            font-size: 1.2rem;
            color: var(--dark);
            font-weight: 600;
        }

        .match-age-location {
            color: var(--gray);
            margin: 8px 0;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .match-age-location i {
            margin-right: 8px;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .match-bio {
            color: #555;
            font-size: 0.9rem;
            margin: 10px 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .match-actions {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .match-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .message-btn {
            background: var(--primary);
            color: white;
            flex: 1;
            margin-right: 10px;
        }

        .message-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .view-btn {
            background: #f0f0f0;
            color: var(--dark);
            flex: 1;
        }

        .view-btn:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        .no-matches {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 0 auto;
        }

        .no-matches h2 {
            color: var(--dark);
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        .no-matches p {
            color: var(--gray);
            margin-bottom: 25px;
            font-size: 1rem;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: var(--primary);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }

        .btn-primary {
            background: var(--primary);
        }

        .match-interests {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
        }

        .interest-tag {
            background: #f0f0f0;
            color: #555;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .matches-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }
            
            .match-image-container {
                height: 200px;
            }
            
            .match-info {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .matches-grid {
                grid-template-columns: 1fr;
            }
            
            .page-title {
                font-size: 1.8rem;
            }
            
            .match-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .message-btn, .view-btn {
                width: 100%;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <?php include './header.php'; ?>
    
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
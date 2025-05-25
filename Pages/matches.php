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
    <link rel="stylesheet" href="../css/style.css">
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
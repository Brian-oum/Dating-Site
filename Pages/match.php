<?php
session_start();
include('../config/db.php'); // Ensure this file sets $pdo correctly using PDO

// Simulated user login (replace with real session logic)
$user_id = $_SESSION['user_id'] ?? 1;

try {
    // Fetch user's gender and preferences
    $stmt = $pdo->prepare("SELECT gender, preference FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        header("Location: ./dashboard.php");
        exit;
    }

    $user_gender = $user['gender'];
    $user_preference = $user['preference'];

    // Fetch matches based on preferences
    $match_stmt = $pdo->prepare("SELECT * FROM users WHERE gender = ? AND preference = ? AND id != ?");
    $match_stmt->execute([$user_preference, $user_gender, $user_id]);
    $matches = $match_stmt->fetchAll();

} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Matches</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Optional external styles -->
</head>
<body>
    <h1>Your Matches</h1>

    <?php if (!empty($matches)): ?>
        <div class="match-list">
            <?php foreach ($matches as $match): ?>
                <div class="match-card">
                    <img src="<?= htmlspecialchars($match['profile_pic'] ?? 'default.jpg') ?>" alt="Profile Picture">
                    <h3><?= htmlspecialchars($match['fullname']) ?></h3>
                    <p>Location: <?= htmlspecialchars($match['location']) ?></p>
                    <p>Age: <?= htmlspecialchars($match['age']) ?></p>
                    <a href="message.php?to=<?= $match['id'] ?>">Message</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-matches">No matches found. Update your profile to improve visibility.</div>
    <?php endif; ?>
</body>
</html>

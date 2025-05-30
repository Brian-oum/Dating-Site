<?php
session_start();
require_once '../config/db.php'; // Your PDO connection should be in this file

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Join `account` and `users` tables
$stmt = $pdo->prepare("
    SELECT 
        a.username, a.email, 
        u.profile_photo, u.first_name, u.last_name, u.age, u.location, u.gender, u.bio
    FROM 
        account a
    JOIN 
        users u ON a.id = u.account_id
    WHERE 
        a.id = ?
");

$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Profile not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - AfroLove</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .profile-container {
            width: 60%;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
        }
        .profile-info h2 {
            margin: 0;
        }
        .profile-info p {
            margin: 0.3rem 0;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <center>
            <img src="<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Picture" class="profile-picture">
            <h2><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
        </center>
        <div class="profile-info">
            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Age:</strong> <?= htmlspecialchars($user['age']) ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($user['gender']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($user['location']) ?></p>
            <p><strong>Bio:</strong> <?= nl2br(htmlspecialchars($user['bio'])) ?></p>
        </div>
        <center>
            <a href="edit_profile.php">✏️ Edit Profile</a>
        </center>
    </div>
</body>
</html>

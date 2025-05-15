<?php
include('../config/db.php'); // correct path

$id = $_POST['id'] ?? null;
$photo = $_FILES['profile_photo']['name'] ?? '';
$photoPath = '';

if (!$id) {
    die("User ID is required.");
}

// Handle image upload
if ($photo) {
    $targetDir = __DIR__ . '/../uploads/';
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $photoPath = 'uploads/' . basename($photo);
    move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetDir . basename($photo));
}

$sql = "UPDATE users SET
    profile_photo = COALESCE(NULLIF(?, ''), profile_photo),
    first_name = ?, last_name = ?, show_last_name = ?,
    age = ?, location = ?, gender = ?, orientation = ?,
    interests = ?, bio = ?, relationship_goals = ?
    WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $photoPath,
    $_POST['first_name'] ?? '',
    $_POST['last_name'] ?? '',
    isset($_POST['show_last_name']) ? 1 : 0,
    $_POST['age'] ?? 0,
    $_POST['location'] ?? '',
    $_POST['gender'] ?? '',
    $_POST['orientation'] ?? '',
    $_POST['interests'] ?? '',
    $_POST['bio'] ?? '',
    $_POST['relationship_goals'] ?? '',
    $id
]);

header("Location: ../Pages/profile.php");
exit;

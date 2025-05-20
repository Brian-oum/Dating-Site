<?php
session_start();
include '../config/db.php';

// REGISTER
if (isset($_POST['register'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Additional profile fields
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $show_last_name = isset($_POST['show_last_name']) ? 1 : 0;
    $age = intval($_POST['age'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    // Handle profile photo upload
    $profile_photo = '';
    if (!empty($_FILES['profile_photo']['name'])) {
        $uploadDir = '../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $profile_photo_name = basename($_FILES['profile_photo']['name']);
        $profile_photo = 'uploads/' . $profile_photo_name;
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $uploadDir . $profile_photo_name);
    }

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['message'] = "Username, email, and password are required!";
        header("Location: ../index.php");
        exit;
    }

    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM account WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "User already exists!";
        header("Location: ../index.php");
        exit;
    }

    // Insert into `account`
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO account (username, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $hashedPassword])) {
        $account_id = $pdo->lastInsertId();

        // Insert into `users` table
        $stmt = $pdo->prepare("INSERT INTO users (account_id, profile_photo, first_name, last_name, show_last_name, age, location, gender, bio)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $account_id,
            $profile_photo,
            $first_name,
            $last_name,
            $show_last_name,
            $age,
            $location,
            $gender,
            $bio
        ]);

        $_SESSION['message'] = "Registration successful! Please login.";
    } else {
        $_SESSION['message'] = "Registration failed!";
    }

    header("Location: ../index.php");
    exit;
}

// LOGIN
if (isset($_POST['login'])) {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        $_SESSION['message'] = "Please enter both identifier and password!";
        header("Location: ../index.php");
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM account WHERE email = ? OR username = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['message'] = "Welcome, " . htmlspecialchars($user['username']) . "!";
        header("Location: ../pages/dashboard.php");
        exit;
    } else {
        $_SESSION['message'] = "Invalid credentials!";
        header("Location: ../index.php");
        exit;
    }
}
?>

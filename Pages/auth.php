<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Step 1: DB connection
$db_path = '../config/db.php';
if (!file_exists($db_path)) {
    die("Error: DB config file not found at $db_path");
}
include $db_path;

// Step 2: Check if register
if (isset($_POST['register'])) {
    echo "Step: Registering<br>";

    // Fetch form data
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $show_last_name = isset($_POST['show_last_name']) ? 1 : 0;
    $age = intval($_POST['age'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    $profile_photo = '';
    if (!empty($_FILES['profile_photo']['name'])) {
        $uploadDir = '../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $profile_photo_name = basename($_FILES['profile_photo']['name']);
        $target_path = $uploadDir . $profile_photo_name;
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_path)) {
            $profile_photo = 'uploads/' . $profile_photo_name;
        } else {
            die("Error uploading profile photo.");
        }
    }

    // Validate
    if (empty($username) || empty($email) || empty($password)) {
        die("Missing required fields.");
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM account WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->rowCount() > 0) {
        die("User already exists.");
    }

    // Create account
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO account (username, email, password) VALUES (?, ?, ?)");
    if (!$stmt->execute([$username, $email, $hashedPassword])) {
        die("Account creation failed.");
    }

    $account_id = $pdo->lastInsertId();

    // Insert profile
    $stmt = $pdo->prepare("INSERT INTO users (account_id, profile_photo, first_name, last_name, show_last_name, age, location, gender, bio)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt->execute([$account_id, $profile_photo, $first_name, $last_name, $show_last_name, $age, $location, $gender, $bio])) {
        die("User profile creation failed.");
    }

    $_SESSION['message'] = "Registration successful! Please login.";
    header("Location: ../index.php");
    exit;
}

// LOGIN
if (isset($_POST['login'])) {
    echo "Step: Logging in<br>";

    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        die("Please enter both identifier and password.");
    }

    $stmt = $pdo->prepare("SELECT * FROM account WHERE email = ? OR username = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: ../pages/dashboard.php");
        exit;
    } else {
        die("Invalid credentials.");
    }
}

// Fallback
echo "No action performed.";
?>

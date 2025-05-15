<?php
session_start();
include '../config/db.php';

// REGISTER
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if person already exists
    $stmt = $pdo->prepare("SELECT id FROM account WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['message'] = "User already exists!";
        header("Location: ../index.php");
        exit;
    }

    // Insert new person
    $stmt = $pdo->prepare("INSERT INTO account (username, email, password) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $email, $password])) {
        $_SESSION['message'] = "Registration successful! Please login.";
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['message'] = "Registration failed!";
        header("Location: ../index.php");
        exit;
    }
}

// LOGIN
if (isset($_POST['login'])) {
    $identifier = trim($_POST['identifier']); // username or email
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM account WHERE email = ? OR username = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['message'] = "Welcome, " . $user['username'];
        header("Location: ../pages/dashboard.php");
        exit;
    } else {
        $_SESSION['message'] = "Invalid credentials!";
        header("Location: ../index.php");
        exit;
    }
}

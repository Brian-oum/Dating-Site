<?php
header('Content-Type: application/json');
require 'vendor/autoload.php';

// Simple authentication check
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Database connection
require '../config/db.php'

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get chat history
    $stmt = $pdo->prepare("SELECT * FROM messages ORDER BY timestamp DESC LIMIT 50");
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($messages);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle other API requests if needed
    echo json_encode(['status' => 'success']);
}
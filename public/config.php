<?php
// public/config.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO(
        'mysql:host=localhost;port=8889;dbname=nft_db;charset=utf8mb4',
        'root',
        'root',
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('DB Connection failed: ' . $e->getMessage());
}

function isUserLoggedIn() {
    return !empty($_SESSION['user_id']);
}
function isAdminLoggedIn() {
    return isUserLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: ../login.php');
        exit;
    }
}

// повертаємо PDO для підключення в інших скриптах
return $pdo;
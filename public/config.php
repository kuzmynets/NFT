<?php

session_start([

]);

// PDO
try {
    $pdo = new PDO(
        'mysql:host=localhost;port=8889;dbname=nft_db;charset=utf8mb4',
        'root',
        'root',
        [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("DB error: " . $e->getMessage());
}

// Функції-утиліти
function isAdminLoggedIn() {
    return !empty($_SESSION['admin_id']);
}
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: admin/login.php');
        exit;
    }
}
function isUserLoggedIn() {
    return !empty($_SESSION['user_id']);
}
function requireUser() {
    if (!isUserLoggedIn()) {
        header('Location: user/login.php');
        exit;
    }
}

return $pdo;
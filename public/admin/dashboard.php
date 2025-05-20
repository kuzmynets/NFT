<?php
// public/admin/dashboard.php

// Вмикаємо відображення помилок для розробки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// Підключаємо конфіг (повертає PDO та утиліту requireAdmin)
$pdo = require __DIR__ . '/../config.php';
// Перевіряємо, що користувач є адміністратором
requireAdmin();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Адмін-панель</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<h1>Адмін-панель</h1>

<!-- Основна навігація для адміністратора -->
<nav>
    <a href="../index.php">Галерея NFT</a>
    <a href="posts.php">Управління постами</a>
    <a href="post_create.php">Додати новий пост</a>
    <a href="../logout.php">Вийти</a>
</nav>

<!-- Можна додати розділ-статистику чи привітання -->
<p>Вітаємо, адміністраторе! Оберіть дію в навігації вище.</p>
</body>
</html>
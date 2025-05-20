<?php
// public/admin/dashboard.php

session_start();
// Якщо адміністратор не в сесії — перекидаємо на сторінку входу
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Підключаємо PDO (необов’язково використовувати тут, але на випадок статистики)
// $pdo = require __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Адмін-панель</title>
</head>
<body>
<h1>Адмін-панель</h1>
<ul>
    <li><a href="posts.php">Управління постами</a></li>
    <li><a href="post_create.php">Додати новий пост</a></li>
    <li><a href="logout.php">Вийти</a></li>
</ul>
</body>
</html>

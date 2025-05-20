<?php
// public/admin/dashboard.php

session_start();

// Підключаємо конфіг, щоб визначити requireAdmin()
$pdo = require __DIR__ . '/../config.php';

// Перевіряємо, що це саме адмін
requireAdmin();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Адмін-панель</title>
</head>
<body>
<h1>Ласкаво просимо, адміністраторе!</h1>
<ul>
    <li><a href="posts.php">Управління постами</a></li>
    <li><a href="../logout.php">Вийти</a></li>
</ul>
</body>
</html>
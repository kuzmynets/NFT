<?php
// public/admin/dashboard.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$pdo = require __DIR__ . '/../config.php';
requireAdmin();

// Параметри шаблону
$pageTitle = 'Адмін-панель';
ob_start();
?>
    <div class="container mt-4">
        <h1 class="mb-4"><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></h1>
        <div class="list-group mb-4">
            <a href="../index.php" class="list-group-item list-group-item-action">Галерея NFT</a>
            <a href="posts.php" class="list-group-item list-group-item-action">Управління постами</a>
            <a href="post_create.php" class="list-group-item list-group-item-action">Додати новий пост</a>
            <a href="../logout.php" class="list-group-item list-group-item-action text-danger">Вийти</a>
        </div>
        <div class="alert alert-info">Вітаємо, адміністраторе! Оберіть дію зі списку вище.</div>
    </div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../templates/layout.php';
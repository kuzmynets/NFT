<?php
// public/index.php

// 1) Дебаг помилок (для розробки)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Стартуємо сесію
session_start();

// 3) Підключаємо config (повертає PDO і утиліти isUserLoggedIn(), isAdminLoggedIn())
$pdo = require __DIR__ . '/config.php';

// 4) Отримуємо список NFT
$stmt = $pdo->query('SELECT id, title, description, image FROM nfts ORDER BY id DESC');
$items = $stmt->fetchAll();

function navLink($href, $label) {
    echo '<a href="'.htmlspecialchars($href,ENT_QUOTES).'">'.htmlspecialchars($label,ENT_QUOTES).'</a> ';
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Головна — NFT Галерея</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<!-- Навігація -->
<nav>
    <?php if (isUserLoggedIn()): ?>
        <?php navLink('user/profile.php', 'Мій профіль'); ?>
        <?php if (isAdminLoggedIn()): ?>
            <?php navLink('admin/dashboard.php', 'Адмін-панель'); ?>
        <?php endif; ?>
        <?php navLink('logout.php', 'Вийти'); ?>
    <?php else: ?>
        <?php navLink('login.php', 'Увійти'); ?>
        <?php navLink('register.php', 'Реєстрація'); ?>
    <?php endif; ?>
</nav>

<h1>Моя NFT-галерея</h1>
<div class="gallery">
    <?php foreach ($items as $n): ?>
        <div class="item">
            <a href="post.php?id=<?= $n['id'] ?>">
                <?php if ($n['image']): ?>
                    <img src="storage/uploads/<?= htmlspecialchars($n['image'],ENT_QUOTES) ?>"
                         alt="<?= htmlspecialchars($n['title'],ENT_QUOTES) ?>">
                <?php else: ?>
                    <div style="width:100%;height:100px;background:#f0f0f0;display:flex;
                        align-items:center;justify-content:center;color:#888;">
                        Без зображення
                    </div>
                <?php endif; ?>
                <p><?= htmlspecialchars($n['title'],ENT_QUOTES) ?></p>
            </a>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
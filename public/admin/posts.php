<?php
// public/admin/posts.php

// 1) Стартуємо сесію
session_start();

// 2) Підключаємо config — він повертає PDO і містить requireAdmin()
$pdo = require __DIR__ . '/../config.php';

// 3) Перевіряємо, що це саме адмін
requireAdmin();

// 4) Отримуємо всі записи з таблиці nfts
$items = $pdo
    ->query('SELECT id, title, description, image FROM nfts ORDER BY id DESC')
    ->fetchAll();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Управління NFT</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<h1>Управління NFT</h1>
<p>
    <a href="post_create.php">+ Додати новий</a> |
    <a href="dashboard.php">← Панель</a>
</p>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Заголовок</th>
        <th>Опис</th>
        <th>Зображення</th>
        <th>Дії</th>
    </tr>
    <?php foreach ($items as $n): ?>
        <tr>
            <td><?= $n['id'] ?></td>
            <td><?= htmlspecialchars($n['title'], ENT_QUOTES) ?></td>
            <td><?= nl2br(htmlspecialchars($n['description'], ENT_QUOTES)) ?></td>
            <td>
                <?php if ($n['image']): ?>
                    <img src="../storage/uploads/<?= htmlspecialchars($n['image'], ENT_QUOTES) ?>"
                         width="80" alt="">
                <?php endif; ?>
            </td>
            <td>
                <a href="post_edit.php?id=<?= $n['id'] ?>">Редагувати</a> |
                <a href="post_delete.php?id=<?= $n['id'] ?>">Видалити</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>

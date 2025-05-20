<?php
session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
$pdo = require __DIR__ . '/../config.php';

// Отримуємо всі пости
$posts = $pdo
    ->query('SELECT * FROM posts ORDER BY created_at DESC')
    ->fetchAll();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Управління постами</title>
</head>
<body>
<h1>Управління постами</h1>
<p><a href="post_create.php">+ Додати новий пост</a> | <a href="dashboard.php">← Повернутись</a></p>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th><th>Категорія</th><th>Заголовок</th><th>Дата</th><th>Дії</th>
    </tr>
    <?php foreach ($posts as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['category'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($p['title'],    ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= $p['created_at'] ?></td>
            <td>
                <a href="post_edit.php?id=<?= $p['id'] ?>">Редагувати</a> |
                <a href="post_delete.php?id=<?= $p['id'] ?>">Видалити</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>

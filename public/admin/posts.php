<?php
// public/admin/posts.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$pdo = require __DIR__ . '/../config.php';
requireAdmin();

// Отримуємо всі записи
$items = $pdo
    ->query('SELECT id, title, description, image FROM nfts ORDER BY id DESC')
    ->fetchAll();

// Параметри шаблону
$pageTitle = 'Управління NFT';
ob_start();
?>
    <div class="container mt-4">
        <h1 class="mb-4"><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></h1>
        <div class="mb-3">
            <a href="post_create.php" class="btn btn-success me-2">+ Додати новий</a>
            <a href="dashboard.php" class="btn btn-secondary">← Панель</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Заголовок</th>
                    <th>Опис</th>
                    <th>Зображення</th>
                    <th>Дії</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $n): ?>
                    <tr>
                        <td><?= $n['id'] ?></td>
                        <td><?= htmlspecialchars($n['title'], ENT_QUOTES) ?></td>
                        <td><?= nl2br(htmlspecialchars($n['description'], ENT_QUOTES)) ?></td>
                        <td>
                            <?php if ($n['image']): ?>
                                <img src="../storage/uploads/<?= htmlspecialchars($n['image'], ENT_QUOTES) ?>"
                                     alt="" width="80" class="img-thumbnail">
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="post_edit.php?id=<?= $n['id'] ?>" class="btn btn-sm btn-primary me-1">Редагувати</a>
                            <a href="post_delete.php?id=<?= $n['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Видалити цей пост?');">Видалити</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../templates/layout.php';
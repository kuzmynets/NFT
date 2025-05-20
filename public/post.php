<?php
// public/post.php

// Вмикаємо дебаг помилок (для розробки)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// Підключаємо конфіг (повертає PDO та утиліти isUserLoggedIn(), isAdminLoggedIn())
$pdo = require __DIR__ . '/config.php';

// Отримуємо ID поста
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM nfts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) {
    http_response_code(404);
    exit('Пост не знайдено');
}

// Підрахунок лайків
$countStmt = $pdo->prepare('SELECT COUNT(*) FROM likes WHERE post_id = ?');
$countStmt->execute([$id]);
$likesCount = $countStmt->fetchColumn();

// Перевірка, чи користувач уже лайкнув
$userLiked = false;
if (!empty($_SESSION['user_id'])) {
    $check = $pdo->prepare(
        'SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?'
    );
    $check->execute([$id, $_SESSION['user_id']]);
    $userLiked = (bool)$check->fetch();
}

// Збір коментарів
$commStmt = $pdo->prepare(
    'SELECT c.id, c.text, c.created_at, u.name
     FROM comments c
     JOIN users u ON u.id = c.user_id
     WHERE c.post_id = ?
     ORDER BY c.created_at'
);
$commStmt->execute([$id]);
$comments = $commStmt->fetchAll();

// Збір вподобань (для адміна)
$likeStmt = $pdo->prepare(
    'SELECT u.name, u.email, l.created_at
     FROM likes l
     JOIN users u ON u.id = l.user_id
     WHERE l.post_id = ?
     ORDER BY l.created_at DESC'
);
$likeStmt->execute([$id]);
$likers = $likeStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($post['title'], ENT_QUOTES) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php if (isAdminLoggedIn()): ?>
    <nav>
        <a href="admin/dashboard.php">Адмін-панель</a>
        <a href="admin/posts.php">Управління постами</a>
        <a href="admin/post_edit.php?id=<?= $post['id'] ?>">Редагувати пост</a>
        <a href="index.php">Галерея NFT</a>
    </nav>
<?php else: ?>
    <p><a href="index.php">← Повернутись до галереї</a></p>
<?php endif; ?>

<h1><?= htmlspecialchars($post['title'], ENT_QUOTES) ?></h1>

<?php if ($post['image']): ?>
    <img src="storage/uploads/<?= htmlspecialchars($post['image'], ENT_QUOTES) ?>" alt="">
<?php endif; ?>

<p><?= nl2br(htmlspecialchars($post['description'], ENT_QUOTES)) ?></p>

<hr>
<!-- Likes -->
<p>Вподобань: <?= $likesCount ?></p>
<?php if (!empty($_SESSION['user_id'])): ?>
    <form action="like.php" method="post" style="display:inline">
        <input type="hidden" name="post_id" value="<?= $id ?>">
        <button type="submit"><?= $userLiked ? 'Вподобано' : 'Вподобати' ?></button>
    </form>
<?php else: ?>
    <p><a href="login.php">Увійдіть, щоб вподобати</a></p>
<?php endif; ?>

<hr>
<!-- Comments -->
<h2>Коментарі</h2>
<?php foreach ($comments as $c): ?>
    <div class="comment">
        <p><strong><?= htmlspecialchars($c['name'], ENT_QUOTES) ?></strong> <small>(<?= $c['created_at'] ?>)</small></p>
        <p><?= nl2br(htmlspecialchars($c['text'], ENT_QUOTES)) ?></p>
        <?php if (isAdminLoggedIn()): ?>
            <a href="admin/comment_delete.php?comment_id=<?= $c['id'] ?>&post_id=<?= $post['id'] ?>"
               onclick="return confirm('Видалити цей коментар?');" style="color:red;">Видалити</a>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php if (!empty($_SESSION['user_id'])): ?>
    <form action="comment.php" method="post">
        <textarea name="text" rows="4" required placeholder="Ваш коментар..."></textarea><br>
        <input type="hidden" name="post_id" value="<?= $id ?>">
        <button type="submit">Додати коментар</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Увійдіть, щоб коментувати</a></p>
<?php endif; ?>
</body>
</html>
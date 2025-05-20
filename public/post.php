<?php
session_start();
$pdo = require __DIR__ . '/config.php';

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

// Перевірка, чи поточний користувач вже лайкнув
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
    'SELECT c.text, c.created_at, u.name 
     FROM comments c 
     JOIN users u ON u.id = c.user_id
     WHERE c.post_id = ?
     ORDER BY c.created_at'
);
$commStmt->execute([$id]);
$comments = $commStmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($post['title'], ENT_QUOTES) ?></title>
</head>
<body>
<!-- 1. Відображення заголовка -->
<h1><?= htmlspecialchars($post['title'], ENT_QUOTES) ?></h1>

<!-- 2. Відображення зображення -->
<?php if ($post['image']): ?>
    <img src="storage/uploads/<?= htmlspecialchars($post['image'], ENT_QUOTES) ?>" alt="">
<?php endif; ?>

<!-- 3. Відображення опису -->
<p><?= nl2br(htmlspecialchars($post['description'], ENT_QUOTES)) ?></p>

<!-- 4. Ось сюди, після опису, вставляєш блок лайків та коментарів: -->

<hr>
<!-- Likes -->
<p>Вподобань: <?= $likesCount ?></p>
<?php if (!empty($_SESSION['user_id'])): ?>
    <form action="like.php" method="post">
        <input type="hidden" name="post_id" value="<?= $id ?>">
        <button type="submit">
            <?= $userLiked ? 'Вподобано' : 'Вподобати' ?>
        </button>
    </form>
<?php else: ?>
    <p><a href="login.php">Увійдіть, щоб вподобати</a></p>
<?php endif; ?>

<!-- Comments -->
<h2>Коментарі</h2>
<?php foreach ($comments as $c): ?>
    <p>
        <strong><?= htmlspecialchars($c['name'], ENT_QUOTES) ?></strong>
        (<?= $c['created_at'] ?>):<br>
        <?= nl2br(htmlspecialchars($c['text'], ENT_QUOTES)) ?>
    </p>
<?php endforeach; ?>

<?php if (!empty($_SESSION['user_id'])): ?>
    <form action="comment.php" method="post">
        <textarea name="text" rows="4" required></textarea><br>
        <input type="hidden" name="post_id" value="<?= $id ?>">
        <button type="submit">Додати коментар</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Увійдіть, щоб коментувати</a></p>
<?php endif; ?>

<!-- 5. Тут може бути футер або закриття тіла сторінки -->
</body>
</html>
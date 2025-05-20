<?php
// public/profile.php

session_start();
$pdo = require __DIR__ . '/config.php';

// Захищаємося
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Інформація про користувача
$stmt = $pdo->prepare('SELECT name,email,created_at FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Лайкнуті пости
$likesStmt = $pdo->prepare(
    'SELECT p.id, p.title 
     FROM likes l 
     JOIN nfts p ON p.id = l.post_id
     WHERE l.user_id = ?'
);
$likesStmt->execute([$user_id]);
$likes = $likesStmt->fetchAll();

// Коментарі користувача
$commsStmt = $pdo->prepare(
    'SELECT c.text, c.created_at, p.id AS post_id, p.title
     FROM comments c
     JOIN nfts p ON p.id = c.post_id
     WHERE c.user_id = ?
     ORDER BY c.created_at DESC'
);
$commsStmt->execute([$user_id]);
$comments = $commsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Профіль</title>
</head>
<body>
<h1>Профіль <?= htmlspecialchars($user['name'], ENT_QUOTES) ?></h1>
<p>Email: <?= htmlspecialchars($user['email'], ENT_QUOTES) ?></p>
<p>Зареєстрований: <?= $user['created_at'] ?></p>

<h2>Ваші вподобання</h2>
<?php if ($likes): ?>
    <ul>
        <?php foreach ($likes as $l): ?>
            <li><a href="post.php?id=<?= $l['id'] ?>">
                    <?= htmlspecialchars($l['title'], ENT_QUOTES) ?>
                </a></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Поки немає вподобань.</p>
<?php endif; ?>

<h2>Ваші коментарі</h2>
<?php if ($comments): ?>
    <?php foreach ($comments as $c): ?>
        <p>
            <a href="post.php?id=<?= $c['post_id'] ?>">
                <?= htmlspecialchars($c['title'], ENT_QUOTES) ?>
            </a>
            (<?= $c['created_at'] ?>):<br>
            <?= nl2br(htmlspecialchars($c['text'], ENT_QUOTES)) ?>
        </p>
    <?php endforeach; ?>
<?php else: ?>
    <p>Поки немає коментарів.</p>
<?php endif; ?>
</body>
</html>
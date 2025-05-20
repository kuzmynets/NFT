<?php
// public/profile.php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

session_start();
$pdo = require __DIR__ . '/../config.php';

// Доступ тільки для залогінених користувачів
requireUser();
$user_id = $_SESSION['user_id'];

// Інформація про користувача
$stmt = $pdo->prepare('SELECT name, email, created_at FROM users WHERE id = ?');
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

// Параметри шаблону
$pageTitle = 'Профіль — ' . htmlspecialchars($user['name'], ENT_QUOTES);
ob_start();
?>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="mb-4"><?= htmlspecialchars($user['name'], ENT_QUOTES) ?>'s Профіль</h1>
            <ul class="list-group mb-4">
                <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($user['email'], ENT_QUOTES) ?></li>
                <li class="list-group-item"><strong>Зареєстрований:</strong> <?= $user['created_at'] ?></li>
            </ul>

            <h2 class="mb-3">Ваші вподобання</h2>
            <?php if ($likes): ?>
                <div class="list-group mb-4">
                    <?php foreach ($likes as $l): ?>
                        <a href="../post.php?id=<?= $l['id'] ?>" class="list-group-item list-group-item-action">
                            <?= htmlspecialchars($l['title'], ENT_QUOTES) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>У вас ще немає вподобань.</p>
            <?php endif; ?>

            <h2 class="mb-3">Ваші коментарі</h2>
            <?php if ($comments): ?>
                <?php foreach ($comments as $c): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="../post.php?id=<?= $c['post_id'] ?>">
                                    <?= htmlspecialchars($c['title'], ENT_QUOTES) ?>
                                </a>
                            </h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?= $c['created_at'] ?></h6>
                            <p class="card-text"><?= nl2br(htmlspecialchars($c['text'], ENT_QUOTES)) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>У вас ще немає коментарів.</p>
            <?php endif; ?>
        </div>
    </div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../templates/layout.php';
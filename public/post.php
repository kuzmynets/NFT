<?php
// public/post.php

// Вмикаємо дебаг помилок (для розробки)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
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
    $check = $pdo->prepare('SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?');
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

// Встановлюємо заголовок і починаємо буфер
$pageTitle = $post['title'];
ob_start();
?>
    <div class="row mb-4">
        <?php if (isAdminLoggedIn()): ?>
        <?php else: ?>
            <div class="col-12 mb-3">
                <a href="index.php" class="btn btn-primary mt-auto">← Повернутись до галереї</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="card mb-4">
        <?php if ($post['image']): ?>
            <img src="storage/uploads/<?= htmlspecialchars($post['image'], ENT_QUOTES) ?>" class="card-img-top" alt="">
        <?php endif; ?>
        <div class="card-body">
            <h1 class="card-title"><?= htmlspecialchars($post['title'], ENT_QUOTES) ?></h1>
            <p class="card-text"><?= nl2br(htmlspecialchars($post['description'], ENT_QUOTES)) ?></p>
        </div>
    </div>

    <!-- Likes -->
    <div class="mb-4">
        <h4>Вподобань: <?= $likesCount ?></h4>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <form action="like.php" method="post" class="d-inline">
                <input type="hidden" name="post_id" value="<?= $id ?>">
                <button type="submit" class="btn btn-<?= $userLiked ? 'secondary' : 'primary' ?>">
                    <?= $userLiked ? 'Вподобано' : 'Вподобати' ?>
                </button>
            </form>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline-primary">Увійдіть, щоб вподобати</a>
        <?php endif; ?>
    </div>

    <!-- Comments -->
    <div class="mb-4">
        <h2>Коментарі</h2>
        <?php foreach ($comments as $c): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($c['name'], ENT_QUOTES) ?> <small>(<?= $c['created_at'] ?>)</small></h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($c['text'], ENT_QUOTES)) ?></p>
                    <?php if (isAdminLoggedIn()): ?>
                        <a href="admin/comment_delete.php?comment_id=<?= $c['id'] ?>&post_id=<?= $post['id'] ?>"
                           class="card-link text-danger" onclick="return confirm('Видалити цей коментар?');">Видалити</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php if (!empty($_SESSION['user_id'])): ?>
    <div class="mb-4">
        <h4>Додати коментар</h4>
        <form action="comment.php" method="post">
            <div class="mb-3">
                <textarea name="text" rows="4" class="form-control" required placeholder="Ваш коментар..."></textarea>
            </div>
            <input type="hidden" name="post_id" value="<?= $id ?>">
            <button type="submit" class="btn btn-primary">Додати коментар</button>
        </form>
    </div>
<?php else: ?>
    <p><a href="login.php" class="btn btn-outline-primary">Увійдіть, щоб коментувати</a></p>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
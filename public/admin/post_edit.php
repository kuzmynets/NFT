<?php
// public/admin/post_edit.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$pdo = require __DIR__ . '/../config.php';
requireAdmin();

// Отримуємо ID поста
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM nfts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) {
    die('Пост не знайдено');
}

// Обробка форми редагування поста
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim(isset($_POST['title']) ? $_POST['title'] : '');
    $description = trim(isset($_POST['description']) ? $_POST['description'] : '');
    if ($title === '')       $errors[] = 'Вкажіть заголовок';
    if ($description === '') $errors[] = 'Вкажіть опис';

    // Обробка зображення
    $filename = $post['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('nft_') . '.' . $ext;
        $target   = __DIR__ . '/../storage/uploads/' . $filename;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $errors[] = 'Не вдалося зберегти картинку';
        }
    }

    if (empty($errors)) {
        $update = $pdo->prepare(
            'UPDATE nfts SET title = ?, description = ?, image = ? WHERE id = ?'
        );
        $update->execute([$title, $description, $filename, $id]);
        header('Location: posts.php');
        exit;
    }
}

// Отримуємо коментарі для цього поста
$commStmt = $pdo->prepare(
    'SELECT c.id, c.text, c.created_at, u.name
     FROM comments c
     JOIN users u ON u.id = c.user_id
     WHERE c.post_id = ?
     ORDER BY c.created_at DESC'
);
$commStmt->execute([$id]);
$comments = $commStmt->fetchAll();

// Отримуємо вподобання для цього поста
$likeStmt = $pdo->prepare(
    'SELECT u.name, u.email, l.created_at
     FROM likes l
     JOIN users u ON u.id = l.user_id
     WHERE l.post_id = ?
     ORDER BY l.created_at DESC'
);
$likeStmt->execute([$id]);
$likers = $likeStmt->fetchAll();

// Параметри шаблону
$pageTitle = 'Редагувати NFT #' . $post['id'];
ob_start();
?>
    <div class="container mt-4">
        <h1 class="mb-4"><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></h1>

        <div class="mb-4">
            <a href="dashboard.php" class="btn btn-secondary me-2">Адмін-панель</a>
            <a href="posts.php" class="btn btn-secondary me-2">Управління постами</a>
            <a href="post_create.php" class="btn btn-success me-2">Додати новий пост</a>
            <a href="../logout.php" class="btn btn-danger">Вийти</a>
        </div>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card mb-5">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Заголовок</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title'], ENT_QUOTES) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Опис</label>
                        <textarea id="description" name="description" rows="5" class="form-control" required><?= htmlspecialchars($post['description'], ENT_QUOTES) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Поточне зображення</label><br>
                        <?php if ($post['image']): ?>
                            <img src="../storage/uploads/<?= htmlspecialchars($post['image'], ENT_QUOTES) ?>" alt="" class="img-thumbnail mb-2" style="max-width:200px;"><br>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/*" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Зберегти</button>
                    <a href="posts.php" class="btn btn-secondary ms-2">Скасувати</a>
                </form>
            </div>
        </div>

        <h2>Коментарі</h2>
        <?php foreach ($comments as $c): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($c['name'], ENT_QUOTES) ?> <small>(<?= $c['created_at'] ?>)</small></h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($c['text'], ENT_QUOTES)) ?></p>
                    <a href="comment_delete.php?comment_id=<?= $c['id'] ?>&post_id=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Видалити цей коментар?');">Видалити</a>
                </div>
            </div>
        <?php endforeach; ?>

        <h2>Уподобання</h2>
        <?php if ($likers): ?>
            <ul class="list-group mb-3">
                <?php foreach ($likers as $l): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($l['name'], ENT_QUOTES) ?> (<?= htmlspecialchars($l['email'], ENT_QUOTES) ?>) — <?= $l['created_at'] ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Ще немає вподобань.</p>
        <?php endif; ?>
    </div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../templates/layout.php';
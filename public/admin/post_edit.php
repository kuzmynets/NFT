<?php
// public/admin/post_edit.php

// Вмикаємо показ помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
// Підключаємо конфіг і перевірку ролі
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
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
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
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Редагувати NFT #<?= $post['id'] ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<h1>Редагувати NFT #<?= $post['id'] ?></h1>

<!-- Навігація -->
<nav>
    <a href="dashboard.php">Адмін-панель</a>
    <a href="posts.php">Управління постами</a>
    <a href="post_create.php">Додати новий пост</a>
    <a href="../logout.php">Вийти</a>
</nav>

<!-- Форма редагування поста -->
<?php foreach ($errors as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e, ENT_QUOTES) ?></p>
<?php endforeach; ?>

<form method="post" enctype="multipart/form-data">
    <label>Заголовок:<br>
        <input name="title" value="<?= htmlspecialchars($post['title'], ENT_QUOTES) ?>">
    </label><br><br>

    <label>Опис:<br>
        <textarea name="description" rows="5"><?= htmlspecialchars($post['description'], ENT_QUOTES) ?></textarea>
    </label><br><br>

    <label>Поточне зображення:<br>
        <?php if ($post['image']): ?>
            <img src="../storage/uploads/<?= htmlspecialchars($post['image'], ENT_QUOTES) ?>" width="150"><br>
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">
    </label><br><br>

    <button type="submit">Зберегти</button>
    <a href="posts.php">Скасувати</a>
</form>

<hr>
<!-- Коментарі -->
<h2>Коментарі</h2>
<?php foreach ($comments as $c): ?>
    <div class="comment">
        <p>
            <strong><?= htmlspecialchars($c['name'], ENT_QUOTES) ?></strong>
            (<?= $c['created_at'] ?>)
            <a href="comment_delete.php?comment_id=<?= $c['id'] ?>&post_id=<?= $post['id'] ?>"
               onclick="return confirm('Видалити цей коментар?')"
               style="color:red; margin-left:10px;">[Видалити]</a>
        </p>
        <p><?= nl2br(htmlspecialchars($c['text'], ENT_QUOTES)) ?></p>
    </div>
<?php endforeach; ?>

<hr>
<!-- Вподобання -->
<h2>Уподобання</h2>
<?php if ($likers): ?>
    <ul>
        <?php foreach ($likers as $l): ?>
            <li>
                <?= htmlspecialchars($l['name'], ENT_QUOTES) ?>
                (<?= htmlspecialchars($l['email'], ENT_QUOTES) ?>)
                — <?= $l['created_at'] ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Ще немає вподобань.</p>
<?php endif; ?>
</body>
</html>

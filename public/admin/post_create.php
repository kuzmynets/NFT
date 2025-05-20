<?php
// public/admin/post_create.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$pdo       = require __DIR__ . '/../config.php';

requireAdmin();

$errors    = [];
$title     = '';
$description = '';
$uploadDir = __DIR__ . '/../storage/uploads/';
$maxSize   = 5 * 1024 * 1024; // 5 MB
$allowed   = ['image/jpeg', 'image/png', 'image/gif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Валідація заголовка та опису
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    if ($title === '')       $errors[] = 'Вкажіть заголовок';
    if ($description === '') $errors[] = 'Вкажіть опис';

    // 2) Перевірка файлу
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Будь ласка, завантажте зображення';
    } else {
        // перевірка розміру
        if ($_FILES['image']['size'] > $maxSize) {
            $errors[] = 'Зображення занадто велике (макс. 5 МБ)';
        }
        // використовуємо MIME із $_FILES
        $mime = $_FILES['image']['type'];
        if (!in_array($mime, $allowed, true)) {
            $errors[] = 'Непідтримуваний формат зображення: ' . htmlspecialchars($mime);
        }
        // перевіряємо папку
        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            $errors[] = 'Проблема з папкою для завантажень';
        }
    }

    // 3) Зберігання файлу
    if (empty($errors)) {
        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('nft_') . '.' . $ext;
        $target   = $uploadDir . $filename;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $errors[] = 'Не вдалося зберегти картинку на сервері';
        }
    }

    // 4) Вставка в БД
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO nfts (title, description, image) VALUES (?, ?, ?)');
        $stmt->execute([$title, $description, $filename]);
        header('Location: posts.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Додати NFT</title>
</head>
<body>
<h1>Додати новий NFT</h1>

<!-- Вивід помилок -->
<?php foreach ($errors as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e, ENT_QUOTES) ?></p>
<?php endforeach; ?>

<form method="post" enctype="multipart/form-data">
    <label>Заголовок:<br>
        <input name="title" value="<?= htmlspecialchars($title, ENT_QUOTES) ?>">
    </label><br><br>

    <label>Опис:<br>
        <textarea name="description" rows="5"><?= htmlspecialchars($description, ENT_QUOTES) ?></textarea>
    </label><br><br>

    <label>Зображення:<br>
        <input type="file" name="image" accept="image/*" required>
    </label><br><br>

    <button type="submit">Опублікувати</button>
    <a href="posts.php">Скасувати</a>
</form>
</body>
</html>
<?php
// public/admin/post_create.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$pdo = require __DIR__ . '/../config.php';
requireAdmin();

$errors      = [];
$title       = '';
$description = '';
$uploadDir   = __DIR__ . '/../storage/uploads/';
$maxSize     = 5 * 1024 * 1024; // 5 MB
$allowed     = ['image/jpeg','image/png','image/gif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim(isset($_POST['title']) ? $_POST['title'] : '');
    $description = trim(isset($_POST['description']) ? $_POST['description'] : '');
    if ($title === '')       $errors[] = 'Вкажіть заголовок';
    if ($description === '') $errors[] = 'Вкажіть опис';

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Будь ласка, завантажте зображення';
    } else {
        if ($_FILES['image']['size'] > $maxSize) {
            $errors[] = 'Зображення занадто велике (макс. 5 МБ)';
        }
        $mime = $_FILES['image']['type'];
        if (!in_array($mime, $allowed, true)) {
            $errors[] = 'Непідтримуваний формат зображення: '.htmlspecialchars($mime, ENT_QUOTES);
        }
    }

    if (empty($errors)) {
        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('nft_') . '.' . $ext;
        $target   = $uploadDir . $filename;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $errors[] = 'Не вдалося зберегти картинку на сервері';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO nfts (title, description, image) VALUES (?, ?, ?)');
        $stmt->execute([$title, $description, $filename]);
        header('Location: posts.php');
        exit;
    }
}

// Параметри шаблону
$pageTitle = 'Додати новий NFT';
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <h1 class="mb-4 text-center"><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></h1>

        <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Заголовок</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($title, ENT_QUOTES) ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Опис</label>
                <textarea id="description" name="description" rows="5" class="form-control" required><?= htmlspecialchars($description, ENT_QUOTES) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Зображення</label>
                <input type="file" id="image" name="image" accept="image/*" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Опублікувати</button>
            <a href="posts.php" class="btn btn-secondary ms-2">Скасувати</a>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../templates/layout.php';
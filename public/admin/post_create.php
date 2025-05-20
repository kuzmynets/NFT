<?php
session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
$pdo = require __DIR__ . '/../config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Валідація
    $category = trim($_POST['category']);
    $title    = trim($_POST['title']);
    $content  = trim($_POST['content']);
    if ($category === '') $errors[] = 'Вкажіть категорію';
    if ($title    === '') $errors[] = 'Вкажіть заголовок';
    if ($content  === '') $errors[] = 'Вкажіть текст поста';

    // Обробка зображення
    $filename = null;
    if (!empty($_FILES['image']['tmp_name'])) {
        $fn = uniqid().'_'.basename($_FILES['image']['name']);
        $target = __DIR__ . '/../storage/uploads/' . $fn;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $filename = $fn;
        } else {
            $errors[] = 'Не вдалося завантажити зображення';
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare('
          INSERT INTO posts (category, image, title, content)
          VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([$category, $filename, $title, $content]);
        header('Location: posts.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head><meta charset="utf-8"><title>Додати пост</title></head>
<body>
<h1>Додати новий пост</h1>
<?php foreach ($errors as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></p>
<?php endforeach; ?>
<form method="post" enctype="multipart/form-data">
    <label>Категорія:<br>
        <input name="category" value="<?= isset($category) ? htmlspecialchars($category, ENT_QUOTES, 'UTF-8') : '' ?>">
    </label><br><br>
    <label>Зображення:<br>
        <input type="file" name="image">
    </label><br><br>
    <label>Заголовок:<br>
        <input name="title" value="<?= isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : '' ?>">
    </label><br><br>
    <label>Текст:<br>
        <textarea name="content" rows="5" cols="50"><?= isset($content) ? htmlspecialchars($content, ENT_QUOTES, 'UTF-8') : '' ?></textarea>
    </label><br><br>
    <button type="submit">Опублікувати</button>
    <a href="posts.php">Скасувати</a>
</form>
</body>
</html>

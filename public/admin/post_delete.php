<?php
session_start();
if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
$pdo = require __DIR__ . '/../config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Підтверджено видалення
    $pdo->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
    header('Location: posts.php');
    exit;
}

// Перед показом перевіримо, що пост існує
$stmt = $pdo->prepare('SELECT title FROM posts WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) {
    die('Пост не знайдено');
}
?>
<!DOCTYPE html>
<html lang="uk">
<head><meta charset="utf-8"><title>Видалити пост</title></head>
<body>
<h1>Видалити пост #<?= $id ?></h1>
<p>Ви впевнені, що хочете видалити пост “<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>”?</p>
<form method="post">
    <button type="submit">Так, видалити</button>
    <a href="posts.php">Ні, повернутися назад</a>
</form>
</body>
</html>

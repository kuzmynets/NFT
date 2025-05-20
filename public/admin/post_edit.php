<?php
session_start();

$pdo = require __DIR__ . '/../config.php';

requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM nfts WHERE id = ?');
$stmt->execute([$id]);
$nft = $stmt->fetch();
if (!$nft) {
    die('Запис не знайдено');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    if ($title === '')       $errors[] = 'Вкажіть заголовок';
    if ($description === '') $errors[] = 'Вкажіть опис';

    // Якщо завантажили нове зображення
    $filename = $nft['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('nft_') . '.' . $ext;
        $target   = __DIR__ . '/../storage/uploads/' . $filename;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $errors[] = 'Не вдалося зберегти картинку';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('
          UPDATE nfts
          SET title = ?, description = ?, image = ?
          WHERE id = ?
        ');
        $stmt->execute([$title, $description, $filename, $id]);
        header('Location: posts.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk"><head>
    <meta charset="utf-8"><title>Редагувати NFT</title>
</head><body>
<h1>Редагувати NFT #<?= $nft['id'] ?></h1>
<?php foreach ($errors as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e, ENT_QUOTES) ?></p>
<?php endforeach; ?>
<form method="post" enctype="multipart/form-data">
    <label>Заголовок:<br>
        <input name="title" value="<?= htmlspecialchars($nft['title'], ENT_QUOTES) ?>">
    </label><br><br>
    <label>Опис:<br>
        <textarea name="description" rows="5"><?= htmlspecialchars($nft['description'], ENT_QUOTES) ?></textarea>
    </label><br><br>
    <label>Поточне зображення:<br>
        <?php if ($nft['image']): ?>
            <img src="../storage/uploads/<?= htmlspecialchars($nft['image'], ENT_QUOTES) ?>" width="150"><br>
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">
    </label><br><br>
    <button>Зберегти</button>
    <a href="posts.php">Скасувати</a>
</form>
</body></html>

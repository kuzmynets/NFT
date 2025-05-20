<?php
// index.php — точка входу

ini_set('display_errors', 1);
error_reporting(E_ALL);

$pdo = require __DIR__ . '/config.php';

// Якщо в URL є ?id=… — показуємо деталі одного запису
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM nfts WHERE id = ?');
    $stmt->execute([$id]);
    $nft = $stmt->fetch();
    if (!$nft) {
        http_response_code(404);
        echo '<h1>Запис не знайдено</h1>';
        exit;
    }
    ?>
    <!DOCTYPE html>
    <html><head>
        <meta charset="utf-8">
        <title><?= htmlspecialchars($nft['title'], ENT_QUOTES) ?></title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head><body>
    <p><a href="index.php">← Назад</a></p>
    <h1><?= htmlspecialchars($nft['title'], ENT_QUOTES) ?></h1>
    <?php if ($nft['image']): ?>
        <img src="storage/uploads/<?= htmlspecialchars($nft['image'], ENT_QUOTES) ?>" alt="">
    <?php endif; ?>
    <p><?= nl2br(htmlspecialchars($nft['description'], ENT_QUOTES)) ?></p>
    </body></html>
    <?php
    exit;
}

// Інакше — виводимо список усіх записів
$stmt = $pdo->query('SELECT id, title, image FROM nfts ORDER BY id DESC');
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html><head>
    <meta charset="utf-8">
    <title>Галерея NFT</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head><body>
<header>
    <a href="login.php">Логін</a>
    <a href="register.php">Реєстрація</a>
</header>
<h1>Моя NFT-галерея</h1>
<div class="gallery">
    <?php foreach ($items as $n): ?>
        <div class="item">
            <a href="?id=<?= $n['id'] ?>">
                <?php if ($n['image']): ?>
                    <img src="storage/uploads/<?= htmlspecialchars($n['image'], ENT_QUOTES) ?>" alt="">
                <?php endif; ?>
                <p><?= htmlspecialchars($n['title'], ENT_QUOTES) ?></p>
            </a>
        </div>
    <?php endforeach; ?>
</div>
</body></html>
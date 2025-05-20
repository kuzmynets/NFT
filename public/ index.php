<?php
// index.php

// 1) Увімкнути вивід помилок (тільки для розробки!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Підключаємо конфіг, який повертає PDO
$pdo = require __DIR__ . '/config.php';

// 3) Роутинг: якщо є GET-параметр id — показуємо деталі NFT,
//    інакше — галерею
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM nfts WHERE id = ?');
    $stmt->execute([$id]);
    $nft = $stmt->fetch();

    if (!$nft) {
        http_response_code(404);
        echo '<h1>NFT не знайдено</h1>';
        exit;
    }

    // Детальна сторінка
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?= htmlspecialchars($nft['title']) ?></title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
    <p><a href="index.php">← Назад до галереї</a></p>
    <h1><?= htmlspecialchars($nft['title']) ?></h1>
    <img src="https://ipfs.io/ipfs/<?= htmlspecialchars($nft['ipfs_cid']) ?>/image.png" alt="">
    <p><?= nl2br(htmlspecialchars($nft['description'])) ?></p>
    </body>
    </html>
    <?php
    exit;
}

// 4) Галерея
$stmt = $pdo->query('SELECT id, title, ipfs_cid FROM nfts');
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Галерея NFT</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<h1>Моя NFT-галерея</h1>
<div class="gallery">
    <?php foreach ($items as $nft): ?>
        <a href="?id=<?= $nft['id'] ?>">
            <img src="https://ipfs.io/ipfs/<?= htmlspecialchars($nft['ipfs_cid']) ?>/image.png"
                 alt="<?= htmlspecialchars($nft['title']) ?>" />
            <p><?= htmlspecialchars($nft['title']) ?></p>
        </a>
    <?php endforeach; ?>
</div>
</body>
</html>
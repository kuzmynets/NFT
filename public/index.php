<?php
// index.php — точка входу для користувачів

// 1) Показувати помилки (для розробки)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Підключити конфіг (повертає PDO і стартує сесію)
$pdo = require __DIR__ . '/config.php';

// 3) ROUTING: якщо в URL є ?id=... — показуємо деталі, інакше — галерею
if (isset($_GET['id'])) {
    // 3.1) Деталі одного NFT
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM nfts WHERE id = ?');
    $stmt->execute([$id]);
    $nft = $stmt->fetch();

    if (!$nft) {
        http_response_code(404);
        echo '<h1>NFT не знайдено</h1>';
        exit;
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?= htmlspecialchars($nft['title'], ENT_QUOTES, 'UTF-8') ?></title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
    <p><a href="index.php">← Назад до галереї</a></p>
    <h1><?= htmlspecialchars($nft['title'], ENT_QUOTES, 'UTF-8') ?></h1>
    <img src="https://ipfs.io/ipfs/<?= htmlspecialchars($nft['ipfs_cid'], ENT_QUOTES, 'UTF-8') ?>/image.png" alt="">
    <p><?= nl2br(htmlspecialchars($nft['description'], ENT_QUOTES, 'UTF-8')) ?></p>
    </body>
    </html>
    <?php
    exit;
}

// 4) Якщо id не передано — ГАЛЕРЕЯ
$stmt = $pdo->query('SELECT id, title, ipfs_cid FROM nfts ORDER BY id DESC');
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
        <div class="item">
            <a href="index.php?id=<?= $nft['id'] ?>">
                <img src="https://ipfs.io/ipfs/<?= htmlspecialchars($nft['ipfs_cid'], ENT_QUOTES, 'UTF-8') ?>/thumbnail.png"
                     alt="<?= htmlspecialchars($nft['title'], ENT_QUOTES, 'UTF-8') ?>">
                <p><?= htmlspecialchars($nft['title'], ENT_QUOTES, 'UTF-8') ?></p>
            </a>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
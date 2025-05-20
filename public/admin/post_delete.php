<?php
session_start();

$pdo = require __DIR__ . '/../config.php';

requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare('DELETE FROM nfts WHERE id = ?')->execute([$id]);
    header('Location: posts.php');
    exit;
}

// Показуємо підтвердження
$stmt = $pdo->prepare('SELECT title FROM nfts WHERE id = ?');
$stmt->execute([$id]);
$nft = $stmt->fetch();
if (!$nft) die('Запис не знайдено');
?>
<!DOCTYPE html>
<html lang="uk"><head>
    <meta charset="utf-8"><title>Видалити NFT</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head><body>
<h1>Видалити NFT “<?= htmlspecialchars($nft['title'], ENT_QUOTES) ?>”?</h1>
<form method="post">
    <button type="submit">Так, видалити</button>
    <a href="posts.php">Ні, повернутися</a>
</form>
</body></html>

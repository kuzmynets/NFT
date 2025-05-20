<?php
// public/admin/post_delete.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$pageTitle = 'Видалити NFT';
ob_start();
?>
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="card-title mb-4">Видалити NFT «<?= htmlspecialchars($nft['title'], ENT_QUOTES) ?>»?</h3>
                    <form method="post" class="d-inline">
                        <button type="submit" class="btn btn-danger">Так, видалити</button>
                    </form>
                    <a href="posts.php" class="btn btn-secondary ms-2">Ні, повернутися</a>
                </div>
            </div>
        </div>
    </div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../templates/layout.php';
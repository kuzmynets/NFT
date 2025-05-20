<?php
// public/index.php

// 1) Дебаг помилок (для розробки)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Стартуємо сесію
session_start();

// 3) Підключаємо config (повертає PDO і утиліти isUserLoggedIn(), isAdminLoggedIn())
$pdo = require __DIR__ . '/config.php';

// 4) Отримуємо список NFT
$stmt = $pdo->query('SELECT id, title, description, image FROM nfts ORDER BY id DESC');
$items = $stmt->fetchAll();

// 5) Встановлюємо заголовок сторінки
$pageTitle = 'Головна — NFT Галерея';

// 6) Формуємо контент
ob_start();
?>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($items as $n): ?>
            <div class="col">
                <div class="card h-100">
                    <?php if ($n['image']): ?>
                        <img src="storage/uploads/<?= htmlspecialchars($n['image'], ENT_QUOTES) ?>" class="card-img-top" alt="">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($n['title'], ENT_QUOTES) ?></h5>
                        <p class="card-text text-truncate"><?= htmlspecialchars($n['description'], ENT_QUOTES) ?></p>
                        <a href="post.php?id=<?= $n['id'] ?>" class="btn btn-primary mt-auto">Детальніше</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php
// 7) Підключаємо шаблон
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
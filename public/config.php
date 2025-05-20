<?php
// config.php
// Повертає PDO-з’єднання з вашою БД

try {
    $pdo = new PDO(
        'mysql:host=localhost;port=8889;dbname=nft_db;charset=utf8mb4',
        'root',
        'root',
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    // Якщо не вдалося підключитися — зупиняємо скрипт і виводимо помилку
    die('DB Connection failed: ' . $e->getMessage());
}

return $pdo;

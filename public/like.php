<?php
// public/like.php

// 1) Показ помилок для дебагу (забрати в продакшені)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Старт сесії та підключення БД
session_start();
$pdo = require __DIR__ . '/config.php';

// 3) Якщо не залогінений — на логін
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 4) Читаємо post_id
$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$user_id = (int)$_SESSION['user_id'];

// 5) Toggle лайка
if ($post_id > 0) {
    // Перевіряємо, чи лайк вже є
    $stmt = $pdo->prepare('SELECT id FROM likes WHERE user_id = ? AND post_id = ?');
    $stmt->execute([$user_id, $post_id]);
    if ($like = $stmt->fetch()) {
        // Якщо є — видаляємо
        $pdo->prepare('DELETE FROM likes WHERE id = ?')
            ->execute([$like['id']]);
    } else {
        // Якщо нема — додаємо
        $pdo->prepare('INSERT INTO likes (user_id, post_id) VALUES (?, ?)')
            ->execute([$user_id, $post_id]);
    }
}

// 6) Повернення на сторінку поста
header('Location: post.php?id=' . $post_id);
exit;
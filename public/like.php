<?php
// public/comment.php

session_start();
$pdo = require __DIR__ . '/config.php';

// Захищаємося від неавторизованих
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$text    = trim(isset($_POST['text']) ? $_POST['text'] : '');

if ($post_id && $text !== '') {
    $stmt = $pdo->prepare(
        'INSERT INTO comments (user_id, post_id, text) VALUES (?,?,?)'
    );
    $stmt->execute([$_SESSION['user_id'], $post_id, $text]);
}

header('Location: post.php?id=' . $post_id);
exit;

<?php
session_start();

// Очищаємо всі дані сесії
$_SESSION = [];

// Якщо використовуєте кукі для сесії — видаляємо її
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Знищуємо саму сесію
session_destroy();

// Переадресуємо на сторінку входу
header('Location: index.php');
exit;
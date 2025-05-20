<?php
// public/register.php

// 1) Вивід помилок для дебагу (видаліть у продакшені)
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

// 2) Старт сесії
session_start();

// 3) Підключення PDO
$pdo = require __DIR__ . '/config.php';

$errors = [];
$name    = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 4) Зчитування полів
    $name            = trim(isset($_POST['name']) ? $_POST['name'] : '');
    $email           = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password        = isset($_POST['password']) ? $_POST['password'] : '';
    $passwordConfirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

    // 5) Валідація
    if ($name === '') {
        $errors[] = 'Вкажіть ім’я';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Вкажіть коректний email';
    }
    if ($password === '') {
        $errors[] = 'Вкажіть пароль';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Пароль повинен бути щонайменше 6 символів';
    }
    if ($password !== $passwordConfirm) {
        $errors[] = 'Паролі не співпадають';
    }

    // 6) Перевірка унікальності email
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Цей email вже зареєстровано';
        }
    }

    // 7) Створення нового користувача
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO users (name,email,password_hash,role,created_at)
             VALUES (?,      ?,    ?,             ?,    NOW())'
        );
        $stmt->execute([$name, $email, $hash, 'user']);
        // 8) Редірект на логін
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Реєстрація</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<h1>Реєстрація</h1>

<!-- Вивід помилок -->
<?php if ($errors): ?>
    <ul style="color:red">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" action="register.php">
    <label>Ім’я:<br>
        <input type="text" name="name"
               value="<?= htmlspecialchars($name, ENT_QUOTES) ?>" required>
    </label><br><br>

    <label>Email:<br>
        <input type="email" name="email"
               value="<?= htmlspecialchars($email, ENT_QUOTES) ?>" required>
    </label><br><br>

    <label>Пароль:<br>
        <input type="password" name="password" required>
    </label><br><br>

    <label>Повторіть пароль:<br>
        <input type="password" name="password_confirm" required>
    </label><br><br>

    <button type="submit">Зареєструватися</button>
</form>

<p>Вже є акаунт? <a href="login.php">Увійти</a></p>
</body>
</html>

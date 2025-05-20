<?php
// public/register.php

// Вивід помилок для дебагу (видаліть у продакшені)
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

// Старт сесії
session_start();

// Підключення PDO
$pdo = require __DIR__ . '/config.php';

$errors = [];
$name    = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Зчитування полів
    $name            = trim(isset($_POST['name']) ? $_POST['name'] : '');
    $email           = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password        = isset($_POST['password']) ? $_POST['password'] : '';
    $passwordConfirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

    // Валідація
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

    // Перевірка унікальності email
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Цей email вже зареєстровано';
        }
    }

    // Створення нового користувача
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO users (name, email, password_hash, role, created_at)
             VALUES (?, ?, ?, ?, NOW())'
        );
        $stmt->execute([$name, $email, $hash, 'user']);
        header('Location: login.php');
        exit;
    }
}

// Параметри шаблону
$pageTitle = 'Реєстрація';
ob_start();
?>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4 text-center"><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></h1>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e, ENT_QUOTES) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="register.php">
                <div class="mb-3">
                    <label for="name" class="form-label">Ім’я</label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="<?= htmlspecialchars($name, ENT_QUOTES) ?>"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="<?= htmlspecialchars($email, ENT_QUOTES) ?>"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control"
                           required>
                </div>

                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Повторіть пароль</label>
                    <input type="password"
                           id="password_confirm"
                           name="password_confirm"
                           class="form-control"
                           required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Зареєструватися</button>
            </form>

            <p class="mt-3 text-center">
                Вже є акаунт? <a href="login.php">Увійти</a>
            </p>
        </div>
    </div>

<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
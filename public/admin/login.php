<?php
// public/admin/login.php

// 1) Підключення БД + сесії
$pdo = require __DIR__ . '/../config.php';
session_start();

// 2) Якщо вже в сесії — редірект на панель
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $pass  = isset($_POST['password']) ? $_POST['password'] : '';

    // 3) Перевірка в БД
    $stmt = $pdo->prepare('SELECT id, password_hash FROM admins WHERE login = ?');
    $stmt->execute([$login]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($pass, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Невірний логін або пароль';
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Admin Login</title>
</head>
<body>
<h1>Вхід для адміністратора</h1>
<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
<form method="post">
    <label>Login:
        <input type="text" name="login" value="<?= isset($_POST['login']) ? htmlspecialchars($_POST['login'], ENT_QUOTES, 'UTF-8') : '' ?>" required>
    </label><br>
    <label>Password:
        <input type="password" name="password" required>
    </label><br>
    <button type="submit">Увійти</button>
</form>
</body>
</html>
<?php
// public/login.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$pdo   = require __DIR__ . '/config.php';
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $error = 'Вкажіть email та пароль.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password_hash, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password_hash'])) {
            // авторизація пройшла
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];

            // редірект
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }
        $error = 'Невірний email або пароль.';
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Вхід</title>
</head>
<body>
<h1>Увійти</h1>
<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES) ?></p>
<?php endif; ?>
<form method="post" action="login.php">
    <label>Email:<br>
        <input type="email" name="email"
               value="<?= htmlspecialchars($email, ENT_QUOTES) ?>"
               required>
    </label><br><br>
    <label>Пароль:<br>
        <input type="password" name="password" required>
    </label><br><br>
    <button type="submit">Увійти</button>
</form>
</body>
</html>

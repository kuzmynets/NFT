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
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $pass  = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $pass === '') {
        $error = 'Вкажіть email та пароль.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password_hash, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];

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

$pageTitle = 'Увійти';
ob_start();
?>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h1 class="mb-4 text-center"><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></h1>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
            <?php endif; ?>
            <form method="post" action="login.php">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES) ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Увійти</button>
            </form>
            <p class="mt-3 text-center">Немає акаунту? <a href="register.php">Реєстрація</a></p>
        </div>
    </div>
<?php
$content = ob_get_clean();
require __DIR__ . '/templates/layout.php';
<?php
// public/templates/layout.php
// Base layout using Bootstrap 5

if (!isset($pageTitle)) {
    $pageTitle = 'NFT Галерея';
}

function isActive($path) {
    return basename($_SERVER['PHP_SELF']) === $path ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-..." crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">NFT Галерея</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isUserLoggedIn()): ?>
                    <?php if (isAdminLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= isActive('admin/dashboard.php') ?>" href="/admin/dashboard.php">Адмін-панель</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('profile.php') ?>" href="/profile.php">Профіль</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout.php">Вийти</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('login.php') ?>" href="/login.php">Увійти</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('register.php') ?>" href="/register.php">Реєстрація</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4">
    <?php
    if (function_exists('the_content')) {
        the_content();
    } elseif (isset($content)) {
        echo $content;
    }
    ?>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-..." crossorigin="anonymous"></script>
</body>
</html>
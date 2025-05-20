<?php
// public/admin/logout.php

session_start();
// Видаляємо всі дані сесії
$_SESSION = [];
session_destroy();

// Переадресація на сторінку входу
header('Location: login.php');
exit;
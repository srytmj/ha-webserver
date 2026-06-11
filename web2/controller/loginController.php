<?php
// controller/loginController.php — Web1 & Web2
session_start();

define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'password123');

function handleLogin(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === ADMIN_USER && $password === ADMIN_PASS) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username']  = $username;
            header('Location: /index.php?action=main');
            exit;
        }
        $_SESSION['login_error'] = 'Username atau password salah.';
        header('Location: /index.php?action=login');
        exit;
    }
    require_once __DIR__ . '/../view/login.php';
}

function handleLogout(): void {
    session_destroy();
    header('Location: /index.php?action=login');
    exit;
}

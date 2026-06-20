<?php
// public/index.php — Front Controller (Web1)
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controller/loginController.php';
require_once __DIR__ . '/../controller/mainController.php';
require_once __DIR__ . '/../controller/userController.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':   handleLogin();   break;
    case 'logout':  handleLogout();  break;
    case 'main':    handleMain();    break;
    case 'read':    handleRead();    break;
    case 'create':  handleCreate();  break;
    case 'update':  handleUpdate();  break;
    case 'delete':  handleDelete();  break;
    default:
        http_response_code(404);
        echo '<h2>404 — Halaman tidak ditemukan.</h2>';
}

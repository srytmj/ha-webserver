<?php
// public/index.php — Front Controller / Router (Web2 — Read Only)
// File create.php, update.php, delete TIDAK tersedia di Web2

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controller/loginController.php';
require_once __DIR__ . '/../controller/mainController.php';

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':  handleLogin();  break;
    case 'logout': handleLogout(); break;
    case 'main':   handleMain();   break;
    case 'read':   handleRead();   break;

    // Operasi tulis ditolak di Web2
    case 'create':
    case 'update':
    case 'delete':
        http_response_code(403);
        echo '<h2 style="font-family:sans-serif; color:#C0392B; padding:32px;">403 — Operasi ini tidak tersedia di server ini (Web2 / Read-Only).</h2>';
        break;

    default:
        http_response_code(404);
        echo '404 — Halaman tidak ditemukan.';
}

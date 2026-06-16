<?php
// controller/mainController.php — Web1 & Web2
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../config/database.php';

function requireLogin(): void {
    if (!isset($_SESSION['logged_in'])) {
        header('Location: /index.php?action=login');
        exit;
    }
}

function handleMain(): void {
    requireLogin();
    $userModel   = new User();
    $users       = $userModel->getAll();
    $serverId    = SERVER_ID;
    $serverLabel = SERVER_LABEL;
    require_once __DIR__ . '/../view/main.php';
}

function handleRead(): void {
    requireLogin();
    $userModel   = new User();
    $users       = $userModel->getAll();
    $serverId    = SERVER_ID;
    $serverLabel = SERVER_LABEL;
    require_once __DIR__ . '/../view/read.php';
}

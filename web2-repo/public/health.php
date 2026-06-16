<?php
// public/health.php — ALB Health Check Endpoint
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');

try {
    $pdo  = getDBConnection();
    $pdo->query('SELECT 1');
    http_response_code(200);
    echo json_encode([
        'status'    => 'healthy',
        'server_id' => SERVER_ID,
        'db'        => 'connected',
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'    => 'unhealthy',
        'server_id' => SERVER_ID,
        'error'     => $e->getMessage(),
    ]);
}

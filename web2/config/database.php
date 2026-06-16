<?php
// config/database.php — Web Instance 2 (Replica Node)
// GANTI nilai di bawah sesuai konfigurasi AWS kamu

define('DB_HOST',   'ha-rds-mysql.c85ckooeqpsp.us-east-1.rds.amazonaws.com'); // Writer Endpoint
define('DB_NAME',   'ha_webserver');
define('DB_USER',   'admin');
define('DB_PASS',   '12341234');
define('DB_CHARSET','utf8mb4');

define('SERVER_ID',    '1');
define('SERVER_LABEL', 'Web Server 1 — Master Node');

define('S3_BUCKET',   'demo-webserver-kelompok4');
define('S3_REGION',   'us-east-1');
define('S3_BASE_URL', 'https://demo-webserver-kelompok4.s3.amazonaws.com/');

function getDBConnection(): PDO {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        return new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        die(json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]));
    }
}


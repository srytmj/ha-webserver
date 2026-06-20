<?php
// config/database.php — Web Instance 2 (Replica Node / Read-Only)
// GANTI nilai di bawah sesuai konfigurasi AWS kamu

define('DB_HOST',          '[DB_PRIMARY_PRIVATE_IP]'); // MySQL on EC2 = PRIMARY (sumber data live, sama dengan Web1)
define('DB_HOST_FALLBACK', '[RDS_ENDPOINT]');          // RDS = salinan exact-copy, dipakai saat primary mati (read-only)
define('FAILOVER_ENABLED', true);                      // set false untuk matikan fallback saat demo bermasalah

define('DB_NAME',   'ha_webserver');
define('DB_USER',   'admin');
define('DB_PASS',   '[PASSWORD_RDS]');                 // password admin db-primary & RDS harus SAMA agar fallback mulus
define('DB_CHARSET','utf8mb4');

define('SERVER_ID',    '2');
define('SERVER_LABEL', 'Web Server 2 — Replica Node');

define('S3_BUCKET',   '[NAMA_BUCKET_S3]');
define('S3_REGION',   'us-east-1');
define('S3_BASE_URL', 'https://[NAMA_BUCKET_S3].s3.us-east-1.amazonaws.com/');

// Membuka koneksi DB dengan failover berurutan: db-primary → RDS (salinan).
// Node yang aktif dicatat di DB_ACTIVE_NODE; fallback RDS ditandai read-only via DB_READONLY.
// Format host boleh 'ip' atau 'ip:port' (default port 3306).
function getDBConnection(): PDO {
    static $conn = null;
    if ($conn instanceof PDO) {
        return $conn; // pakai ulang satu koneksi per request (hemat round-trip)
    }

    $nodes = FAILOVER_ENABLED ? [DB_HOST, DB_HOST_FALLBACK] : [DB_HOST];
    $last  = null;

    foreach ($nodes as $i => $node) {
        [$host, $port] = array_pad(explode(':', $node, 2), 2, '3306');
        try {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, DB_NAME, DB_CHARSET);
            $conn = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 3, // jangan menggantung lama saat primary mati
            ]);
            if (!defined('DB_ACTIVE_NODE')) {
                define('DB_ACTIVE_NODE', $i === 0 ? 'primary' : 'fallback-rds');
                define('DB_READONLY',    $i !== 0); // fallback RDS = read-only
            }
            return $conn;
        } catch (PDOException $e) {
            $last = $e;
        }
    }

    // Semua node down: catat detail teknis ke log server, jangan bocorkan ke pengguna.
    if (!defined('DB_ACTIVE_NODE')) {
        define('DB_ACTIVE_NODE', 'none');
        define('DB_READONLY', true);
    }
    error_log('getDBConnection: semua node database tidak dapat dihubungi. ' . ($last ? $last->getMessage() : ''));
    http_response_code(503);
    header('Content-Type: application/json');
    die(json_encode(['status' => 'unavailable', 'error' => 'Semua node database tidak tersedia. Silakan coba lagi nanti.']));
}

// Web2 read-only: semua operasi tulis sudah ditolak di front controller (index.php → 403).
// Guard ini disediakan agar config kedua instance identik (parity) dan aman bila dipakai ulang.
function assertWritable(): void {
    if (!defined('DB_READONLY')) {
        getDBConnection();
    }
    if (defined('DB_READONLY') && DB_READONLY) {
        http_response_code(503);
        die('<h2 style="font-family:sans-serif;color:#C0392B;padding:32px;">'
          . '503 — Database sedang dalam mode read-only (failover ke salinan RDS).</h2>');
    }
}

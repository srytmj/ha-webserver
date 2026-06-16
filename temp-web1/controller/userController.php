<?php
// controller/userController.php — EKSKLUSIF WEB1 ONLY
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../config/database.php';

function uploadToS3(array $file): string {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedTypes)) {
        throw new RuntimeException('Tipe file tidak diizinkan: ' . $mime);
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Ukuran file melebihi 2MB.');
    }

    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'foto_' . uniqid('', true) . '.' . $ext;
    $tmpPath  = escapeshellarg($file['tmp_name']);
    $s3Path   = escapeshellarg('s3://' . S3_BUCKET . '/photos/' . $filename);

    $cmd    = "aws s3 cp {$tmpPath} {$s3Path} --acl public-read 2>&1";
    $output = shell_exec($cmd);

    if (!$output || strpos($output, 'upload:') === false) {
        throw new RuntimeException('Upload S3 gagal: ' . $output);
    }

    return S3_BASE_URL . 'photos/' . $filename;
}

function handleCreate(): void {
    if (!isset($_SESSION['logged_in'])) {
        header('Location: /index.php?action=login'); exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = trim($_POST['nama'] ?? '');
        $nim  = trim($_POST['nim']  ?? '');

        if (!$nama || !$nim) {
            $_SESSION['error'] = 'Nama dan NIM wajib diisi.';
            header('Location: /index.php?action=create'); exit;
        }

        $fotoUrl = '';
        try {
            if (!empty($_FILES['foto']['name'])) {
                $fotoUrl = uploadToS3($_FILES['foto']);
            }
            $model = new User();
            $model->create($nama, $nim, $fotoUrl);
            $_SESSION['success'] = 'Data berhasil ditambahkan.';
            header('Location: /index.php?action=read'); exit;
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /index.php?action=create'); exit;
        }
    }

    require_once __DIR__ . '/../view/create.php';
}

function handleUpdate(): void {
    if (!isset($_SESSION['logged_in'])) {
        header('Location: /index.php?action=login'); exit;
    }

    $id    = (int) ($_GET['id'] ?? 0);
    $model = new User();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama    = trim($_POST['nama'] ?? '');
        $nim     = trim($_POST['nim']  ?? '');
        $fotoUrl = null;

        try {
            if (!empty($_FILES['foto']['name'])) {
                $fotoUrl = uploadToS3($_FILES['foto']);
            }
            $model->update($id, $nama, $nim, $fotoUrl);
            $_SESSION['success'] = 'Data berhasil diperbarui.';
            header('Location: /index.php?action=read'); exit;
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /index.php?action=update&id=' . $id); exit;
        }
    }

    $user = $model->getById($id);
    if (!$user) {
        http_response_code(404);
        echo '404 — Data tidak ditemukan.'; exit;
    }

    require_once __DIR__ . '/../view/update.php';
}

function handleDelete(): void {
    if (!isset($_SESSION['logged_in'])) {
        header('Location: /index.php?action=login'); exit;
    }
    $id    = (int) ($_GET['id'] ?? 0);
    $model = new User();
    $model->delete($id);
    $_SESSION['success'] = 'Data berhasil dihapus.';
    header('Location: /index.php?action=read');
    exit;
}

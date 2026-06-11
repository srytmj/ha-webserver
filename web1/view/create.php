<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah User — Server 1</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; padding: 32px; }
        .form-card { background: #fff; padding: 32px; max-width: 500px;
                     border-radius: 10px; box-shadow: 0 2px 16px rgba(0,0,0,.08); }
        h2 { color: #1A5276; margin-bottom: 20px; }
        label { font-size: 13px; color: #555; display: block; margin-bottom: 4px; }
        input[type=text], input[type=number], input[type=file] {
            width: 100%; padding: 10px 14px; border: 1px solid #ddd;
            border-radius: 6px; font-size: 14px; margin-bottom: 16px;
        }
        input:focus { outline: none; border-color: #1A5276; }
        .alert-error { background: #fde8e8; color: #c0392b; padding: 10px 14px;
                       border-radius: 6px; font-size: 13px; margin-bottom: 16px; }
        .btn-submit { background: #27AE60; color: #fff; padding: 11px 28px;
                      border: none; border-radius: 6px; font-size: 14px; cursor: pointer; }
        .btn-submit:hover { background: #1E8449; }
        a.back { color: #2E86C1; text-decoration: none; font-size: 13px;
                 display: inline-block; margin-bottom: 16px; }
        .hint { font-size: 11px; color: #aaa; margin-top: -12px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <a class="back" href="/index.php?action=read">← Kembali ke Daftar</a>
    <div class="form-card">
        <h2>➕ Tambah Data User</h2>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="/index.php?action=create" enctype="multipart/form-data">
            <label>Nama Lengkap <span style="color:red">*</span></label>
            <input type="text" name="nama" required placeholder="Masukkan nama lengkap"
                   value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">

            <label>NIM <span style="color:red">*</span></label>
            <input type="number" name="nim" required placeholder="Nomor Induk Mahasiswa"
                   value="<?= htmlspecialchars($_POST['nim'] ?? '') ?>">

            <label>Foto Profil</label>
            <input type="file" name="foto" accept="image/jpeg,image/png,image/gif,image/webp">
            <p class="hint">Format: JPG, PNG, GIF, WEBP. Maks 2MB.</p>

            <button type="submit" class="btn-submit">💾 Simpan</button>
        </form>
    </div>
</body>
</html>

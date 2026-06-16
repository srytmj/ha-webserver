<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data User — Server <?= htmlspecialchars($serverId) ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; padding: 24px 32px; }
        .top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        h2 { color: #1A5276; }
        .badge {
            display: inline-block; padding: 5px 14px; border-radius: 14px;
            font-size: 12px; font-weight: bold;
            background: <?= $serverId == '1' ? '#27AE60' : '#E67E22' ?>;
            color: #fff;
        }
        a.back { color: #2E86C1; text-decoration: none; font-size: 13px; }
        .alert-success { background: #EAFAF1; color: #1E8449; padding: 10px 14px;
                         border-radius: 6px; margin-bottom: 16px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; background: #fff;
                border-radius: 10px; overflow: hidden;
                box-shadow: 0 2px 12px rgba(0,0,0,.07); }
        th { background: #1A5276; color: #fff; padding: 13px 16px; text-align: left; font-size: 13px; }
        td { padding: 12px 16px; border-bottom: 1px solid #eee; font-size: 14px; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #EAF2FF; }
        .foto-thumb { width: 52px; height: 52px; object-fit: cover;
                      border-radius: 50%; border: 2px solid #AED6F1; }
        .no-foto { width: 52px; height: 52px; background: #ECF0F1; border-radius: 50%;
                   display: flex; align-items: center; justify-content: center;
                   color: #BDC3C7; font-size: 10px; text-align: center; }
        .btn { padding: 5px 11px; border-radius: 4px; font-size: 12px;
               text-decoration: none; color: #fff; margin-right: 4px; display: inline-block; }
        .btn-edit  { background: #2E86C1; }
        .btn-del   { background: #C0392B; }
        .empty { text-align: center; padding: 32px; color: #aaa; }
    </style>
</head>
<body>
    <div class="top">
        <div>
            <a class="back" href="/index.php?action=main">← Dashboard</a>
            <h2 style="margin-top:6px;">Daftar Data User</h2>
        </div>
        <span class="badge">SERVER <?= htmlspecialchars($serverId) ?> — <?= $serverId == '1' ? 'Master R/W' : 'Replica R/O' ?></span>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (SERVER_ID === '1'): ?>
        <p style="margin-bottom:12px;">
            <a href="/index.php?action=create" class="btn btn-edit" style="font-size:13px; padding:8px 16px;">
                ➕ Tambah User
            </a>
        </p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Nama</th>
                <th>NIM</th>
                <?php if (SERVER_ID === '1'): ?><th>Aksi</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="<?= SERVER_ID === '1' ? 5 : 4 ?>" class="empty">Belum ada data.</td></tr>
        <?php else: ?>
        <?php foreach ($users as $i => $u): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td>
                    <?php if (!empty($u['foto'])): ?>
                        <img src="<?= htmlspecialchars($u['foto']) ?>" class="foto-thumb" alt="foto">
                    <?php else: ?>
                        <div class="no-foto">No<br>Foto</div>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($u['nama']) ?></td>
                <td><?= htmlspecialchars($u['nim']) ?></td>
                <?php if (SERVER_ID === '1'): ?>
                <td>
                    <a href="/index.php?action=update&id=<?= $u['id'] ?>" class="btn btn-edit">✏️ Edit</a>
                    <a href="/index.php?action=delete&id=<?= $u['id'] ?>" class="btn btn-del"
                       onclick="return confirm('Hapus data <?= htmlspecialchars($u['nama']) ?>?')">🗑️ Hapus</a>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

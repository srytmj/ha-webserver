<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — HA Web Server</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; }
        header {
            background: #1A5276; color: #fff; padding: 14px 32px;
            display: flex; justify-content: space-between; align-items: center;
        }
        header h1 { font-size: 20px; }
        .server-badge {
            background: <?= $serverId == '1' ? '#27AE60' : '#E67E22' ?>;
            color: #fff; padding: 6px 16px; border-radius: 20px;
            font-size: 13px; font-weight: bold; letter-spacing: .5px;
        }
        .server-info {
            text-align: center; padding: 16px 32px;
            background: <?= $serverId == '1' ? '#EAFAF1' : '#FEF9E7' ?>;
            border-left: 4px solid <?= $serverId == '1' ? '#27AE60' : '#E67E22' ?>;
            margin: 24px 32px; border-radius: 8px;
        }
        .server-info p { font-size: 14px; color: #555; }
        .server-info strong { font-size: 22px; color: #1A5276; display: block; margin: 4px 0; }
        .server-info .meta { font-size: 12px; color: #888; }
        nav { padding: 0 32px 24px; }
        nav a {
            display: inline-block; margin: 8px 8px 0 0; padding: 10px 20px;
            background: #2E86C1; color: #fff; border-radius: 6px;
            text-decoration: none; font-size: 14px;
        }
        nav a:hover { background: #1A5276; }
        .btn-danger { background: #C0392B !important; }
        .stats { padding: 0 32px; display: flex; gap: 16px; flex-wrap: wrap; }
        .stat-card {
            background: #fff; padding: 20px 24px; border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,.06); flex: 1; min-width: 150px;
        }
        .stat-card .label { font-size: 12px; color: #888; }
        .stat-card .value { font-size: 28px; font-weight: bold; color: #1A5276; }
    </style>
</head>
<body>
    <header>
        <h1>HA Web Server — Dashboard</h1>
        <span class="server-badge">SERVER <?= htmlspecialchars($serverId) ?></span>
    </header>

    <div class="server-info">
        <p>Request ini diproses oleh:</p>
        <strong><?= htmlspecialchars($serverLabel) ?></strong>
        <p class="meta">
            Instance: <?= htmlspecialchars($serverId) ?> &nbsp;|&nbsp;
            DB Mode: <?= $serverId == '1' ? 'Read &amp; Write (Master)' : 'Read Only (Replica)' ?>
        </p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="label">Total User</div>
            <div class="value"><?= count($users) ?></div>
        </div>
        <div class="stat-card">
            <div class="label">Server</div>
            <div class="value"><?= htmlspecialchars($serverId) ?></div>
        </div>
        <div class="stat-card">
            <div class="label">DB Mode</div>
            <div class="value" style="font-size:16px; padding-top:6px;">
                <?= $serverId == '1' ? 'R/W' : 'R/O' ?>
            </div>
        </div>
    </div>

    <nav>
        <a href="/index.php?action=read">📋 Lihat Data</a>
        <?php if (SERVER_ID === '1'): ?>
        <a href="/index.php?action=create">➕ Tambah Data</a>
        <?php endif; ?>
        <a href="/index.php?action=logout" class="btn-danger">🔓 Logout</a>
    </nav>
</body>
</html>

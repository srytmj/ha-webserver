<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — HA Web Server</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8;
               display: flex; justify-content: center; align-items: center;
               min-height: 100vh; }
        .card { background: #fff; padding: 40px; border-radius: 12px;
                box-shadow: 0 4px 24px rgba(0,0,0,.1); width: 100%; max-width: 380px; }
        h1 { text-align: center; color: #1A5276; margin-bottom: 6px; }
        .subtitle { text-align: center; color: #888; font-size: 13px; margin-bottom: 24px; }
        label { font-size: 13px; color: #555; display: block; margin-bottom: 4px; }
        input { width: 100%; padding: 10px 14px; border: 1px solid #ddd;
                border-radius: 6px; font-size: 14px; margin-bottom: 16px;
                transition: border-color .2s; }
        input:focus { outline: none; border-color: #1A5276; }
        button { width: 100%; padding: 12px; background: #1A5276; color: #fff;
                 border: none; border-radius: 6px; font-size: 15px; cursor: pointer; }
        button:hover { background: #21618C; }
        .alert { background: #fde8e8; color: #c0392b; padding: 10px 14px;
                 border-radius: 6px; font-size: 13px; margin-bottom: 16px; }
        .hint { text-align: center; font-size: 11px; color: #aaa; margin-top: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>HA Web Server</h1>
        <p class="subtitle">Sistem Manajemen Data — AWS Demo</p>

        <?php if (!empty($_SESSION['login_error'])): ?>
            <div class="alert"><?= htmlspecialchars($_SESSION['login_error']) ?></div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>

        <form method="POST" action="/index.php?action=login">
            <label>Username</label>
            <input type="text" name="username" required autofocus placeholder="admin">
            <label>Password</label>
            <input type="password" name="password" required placeholder="••••••••">
            <button type="submit">Masuk</button>
        </form>
        <p class="hint">Demo: admin / password123</p>
    </div>
</body>
</html>

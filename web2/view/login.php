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
        .subtitle { text-align: center; color: #888; font-size: 13px; margin-bottom: 16px; }
        .server-indicator {
            display: flex; align-items: center; justify-content: center;
            gap: 8px; margin-bottom: 20px; padding: 10px 16px;
            background: #FEF9E7; border: 1px solid #FAD7A0;
            border-radius: 8px;
        }
        .server-dot { width: 10px; height: 10px; border-radius: 50%;
                      background: #E67E22; flex-shrink: 0;
                      animation: pulse 1.5s ease-in-out infinite; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: .4; }
        }
        .server-indicator span { font-size: 13px; font-weight: 600; color: #B7770D; }
        .server-indicator small { font-size: 11px; color: #888; }
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

        <div class="server-indicator">
            <div class="server-dot"></div>
            <div>
                <span>SERVER 2</span><br>
                <small>Read Only</small>
            </div>
        </div>

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

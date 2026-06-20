<?php
// public/stress.php — CPU Stress Test untuk CloudWatch Alarm Testing
require_once __DIR__ . '/../config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$duration = min((int)($_POST['duration'] ?? 10), 60); // maks 60 detik
$result   = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stress'])) {
    $start   = microtime(true);
    $end     = $start + $duration;
    $ops     = 0;

    // CPU burn loop — kalkulasi matematika berat
    while (microtime(true) < $end) {
        $x = sqrt(rand(1, 999999)) * log(rand(1, 999999)) / tan(rand(1, 99) * M_PI / 180);
        $ops++;
    }

    $elapsed = round(microtime(true) - $start, 2);
    $result  = ['ops' => number_format($ops), 'elapsed' => $elapsed];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CPU Stress Test — Server <?= SERVER_ID ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; padding: 32px; }
        .card { background: #fff; padding: 32px; max-width: 520px;
                border-radius: 10px; box-shadow: 0 2px 16px rgba(0,0,0,.08); }
        h2 { color: #C0392B; margin-bottom: 6px; }
        .server-badge {
            display: inline-block; margin-bottom: 20px; padding: 4px 14px;
            border-radius: 14px; font-size: 12px; font-weight: bold; color: #fff;
            background: <?= SERVER_ID == '1' ? '#27AE60' : '#E67E22' ?>;
        }
        .warning { background: #FEF9E7; border-left: 4px solid #F39C12;
                   padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;
                   font-size: 13px; color: #7D6608; }
        label { font-size: 13px; color: #555; display: block; margin-bottom: 6px; }
        .slider-row { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        input[type=range] { flex: 1; accent-color: #C0392B; }
        .duration-val { font-size: 20px; font-weight: bold; color: #C0392B;
                        min-width: 60px; text-align: center; }
        .btn-stress {
            width: 100%; padding: 14px; background: #C0392B; color: #fff;
            border: none; border-radius: 8px; font-size: 16px; font-weight: bold;
            cursor: pointer; letter-spacing: .5px; transition: background .2s;
        }
        .btn-stress:hover { background: #A93226; }
        .btn-stress:disabled { background: #aaa; cursor: not-allowed; }
        .result {
            margin-top: 20px; background: #EAFAF1; border-left: 4px solid #27AE60;
            padding: 14px 16px; border-radius: 6px; font-size: 14px;
        }
        .result strong { font-size: 18px; color: #1E8449; }
        .progress { display: none; margin-top: 16px; text-align: center;
                    font-size: 13px; color: #888; }
        .spinner { display: inline-block; width: 18px; height: 18px;
                   border: 3px solid #ddd; border-top-color: #C0392B;
                   border-radius: 50%; animation: spin .7s linear infinite;
                   margin-right: 8px; vertical-align: middle; }
        @keyframes spin { to { transform: rotate(360deg); } }
        a.back { color: #2E86C1; text-decoration: none; font-size: 13px;
                 display: inline-block; margin-bottom: 16px; }
        .tips { margin-top: 20px; font-size: 12px; color: #999; line-height: 1.7; }
    </style>
</head>
<body>
    <a class="back" href="/index.php?action=main">← Dashboard</a>
    <div class="card">
        <h2>🔥 CPU Stress Test</h2>
        <div class="server-badge">SERVER <?= SERVER_ID ?> — <?= SERVER_LABEL ?></div>

        <div class="warning">
            Tombol ini memaksakan CPU tinggi untuk menguji CloudWatch Alarm dan Load Balancer.
            Jangan gunakan di production!
        </div>

        <?php if ($result): ?>
        <div class="result">
            Selesai dalam <strong><?= $result['elapsed'] ?>s</strong> —
            <?= $result['ops'] ?> operasi matematika dijalankan.<br>
            <small style="color:#888">Cek CloudWatch Metrics &gt; EC2 &gt; CPUUtilization</small>
        </div>
        <?php endif; ?>

        <form method="POST" id="stressForm">
            <label>Durasi stress (detik): <span id="durLabel"><?= $duration ?></span>s</label>
            <div class="slider-row">
                <input type="range" name="duration" id="durSlider"
                       min="5" max="60" step="5" value="<?= $duration ?>"
                       oninput="document.getElementById('durLabel').textContent=this.value">
                <div class="duration-val"><span id="durSlider2"><?= $duration ?></span>s</div>
            </div>
            <button type="submit" name="stress" value="1" class="btn-stress" id="stressBtn">
                ⚡ MULAI STRESS TEST
            </button>
            <div class="progress" id="progress">
                <span class="spinner"></span>
                Menjalankan CPU stress... jangan tutup halaman ini.
                (<span id="countdown"><?= $duration ?></span>s tersisa)
            </div>
        </form>

        <div class="tips">
            Tips pengujian CloudWatch:<br>
            1. Buka CloudWatch &gt; Alarms sebelum klik tombol ini.<br>
            2. Klik stress di <strong>kedua</strong> instance bersamaan untuk beban optimal.<br>
            3. Alarm biasanya trigger dalam 1-2 menit setelah CPU spike.<br>
            4. Durasi 30-60 detik memberikan spike yang cukup untuk trigger alarm.
        </div>
    </div>

    <script>
        const slider   = document.getElementById('durSlider');
        const label    = document.getElementById('durLabel');
        const label2   = document.getElementById('durSlider2');
        const countdown= document.getElementById('countdown');

        slider.addEventListener('input', () => {
            label.textContent  = slider.value;
            label2.textContent = slider.value;
            countdown.textContent = slider.value;
        });

        document.getElementById('stressForm').addEventListener('submit', function() {
            const btn      = document.getElementById('stressBtn');
            const progress = document.getElementById('progress');
            const secs     = parseInt(slider.value);

            btn.disabled = true;
            btn.textContent = '⏳ Running...';
            progress.style.display = 'block';
            countdown.textContent  = secs;

            let remaining = secs;
            const timer = setInterval(() => {
                remaining--;
                countdown.textContent = remaining;
                if (remaining <= 0) clearInterval(timer);
            }, 1000);
        });
    </script>
</body>
</html>

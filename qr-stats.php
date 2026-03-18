<?php
/**
 * QR Scan Statistics — mobile-friendly dashboard
 *
 * View at: https://giustogusto.ru/welcome/qr-stats.php?key=giusto2026
 */

$secret = 'giusto2026';

if (($_GET['key'] ?? '') !== $secret) {
    http_response_code(403);
    die('Доступ запрещён');
}

$logFile = __DIR__ . '/qr-scans.log';
$scans = [];

if (file_exists($logFile)) {
    $lines = array_filter(array_map('trim', file($logFile)));
    foreach (array_reverse($lines) as $line) {
        $data = json_decode($line, true);
        if ($data) $scans[] = $data;
    }
}

$total = count($scans);
$today = date('Y-m-d');
$todayCount = count(array_filter($scans, fn($s) => str_starts_with($s['time'], $today)));

// Parse device from User-Agent
function getDevice(string $ua): string {
    if (stripos($ua, 'iPhone') !== false) return '📱 iPhone';
    if (stripos($ua, 'iPad') !== false) return '📱 iPad';
    if (stripos($ua, 'Android') !== false) return '📱 Android';
    if (stripos($ua, 'Windows') !== false) return '💻 Windows';
    if (stripos($ua, 'Mac') !== false) return '💻 Mac';
    return '🔹 Другое';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="robots" content="noindex, nofollow" />
<title>QR Статистика — Giusto Gusto</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    background: #1a1a2e;
    color: #e0e0e0;
    padding: 16px;
    min-height: 100dvh;
  }
  h1 {
    font-size: 20px;
    font-weight: 600;
    color: #E6D48D;
    margin-bottom: 16px;
    text-align: center;
  }
  .cards {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
  }
  .card {
    flex: 1;
    background: rgba(255,255,255,.06);
    border-radius: 12px;
    padding: 16px;
    text-align: center;
  }
  .card__num {
    font-size: 32px;
    font-weight: 700;
    color: #E6D48D;
  }
  .card__label {
    font-size: 12px;
    color: rgba(255,255,255,.5);
    margin-top: 4px;
  }
  .scan {
    background: rgba(255,255,255,.04);
    border-radius: 10px;
    padding: 12px;
    margin-bottom: 8px;
  }
  .scan__time {
    font-size: 14px;
    font-weight: 600;
    color: #E6D48D;
  }
  .scan__meta {
    font-size: 12px;
    color: rgba(255,255,255,.45);
    margin-top: 4px;
    word-break: break-all;
  }
  .empty {
    text-align: center;
    color: rgba(255,255,255,.3);
    padding: 40px 0;
  }
  .refresh {
    display: block;
    margin: 0 auto 20px;
    background: rgba(230,212,141,.15);
    color: #E6D48D;
    border: 1px solid rgba(230,212,141,.25);
    border-radius: 8px;
    padding: 10px 24px;
    font-size: 14px;
    cursor: pointer;
  }
</style>
</head>
<body>
  <h1>📊 QR Сканирования</h1>

  <div class="cards">
    <div class="card">
      <div class="card__num"><?= $total ?></div>
      <div class="card__label">Всего</div>
    </div>
    <div class="card">
      <div class="card__num"><?= $todayCount ?></div>
      <div class="card__label">Сегодня</div>
    </div>
  </div>

  <button class="refresh" onclick="location.reload()">🔄 Обновить</button>

  <?php if (empty($scans)): ?>
    <div class="empty">Сканирований пока нет</div>
  <?php else: ?>
    <?php foreach ($scans as $s): ?>
      <div class="scan">
        <div class="scan__time"><?= htmlspecialchars($s['time']) ?></div>
        <div class="scan__meta"><?= getDevice($s['ua']) ?> · <?= htmlspecialchars($s['ip']) ?></div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</body>
</html>

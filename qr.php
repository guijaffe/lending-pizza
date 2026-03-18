<?php
/**
 * QR Code redirect tracker
 *
 * Logs every scan, then redirects to the clean landing page.
 * Tracking is done via Metrika goal (triggered by #qr hash).
 *
 * QR code should point to: https://giustogusto.ru/welcome/qr.php
 */

// ── Config ──────────────────────────────────────────────────
$logFile     = __DIR__ . '/qr-scans.log';
$destination = 'https://giustogusto.ru/welcome/#qr';

// ── Log the scan ────────────────────────────────────────────
$entry = json_encode([
    'time'  => date('Y-m-d H:i:s'),
    'ip'    => $_SERVER['REMOTE_ADDR'] ?? '-',
    'ua'    => $_SERVER['HTTP_USER_AGENT'] ?? '-',
], JSON_UNESCAPED_UNICODE) . PHP_EOL;

file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);

// ── Redirect (clean URL, no UTM) ────────────────────────────
header('HTTP/1.1 302 Found');
header('Location: ' . $destination);
header('Cache-Control: no-store, no-cache');
exit;

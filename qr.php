<?php
/**
 * QR Code redirect tracker
 *
 * Logs every scan with timestamp, IP, User-Agent and referer,
 * then redirects to the main landing page with UTM tags.
 *
 * QR code should point to:  https://giustogusto.ru/welcome/qr.php
 *   or via .htaccess:       https://giustogusto.ru/qr
 */

// ── Config ──────────────────────────────────────────────────
$logFile     = __DIR__ . '/qr-scans.log';
$destination = 'https://giustogusto.ru/welcome/?utm_source=qr&utm_medium=offline&utm_campaign=flyer';

// ── Log the scan ────────────────────────────────────────────
$entry = implode("\t", [
    date('Y-m-d H:i:s'),
    $_SERVER['REMOTE_ADDR'] ?? '-',
    $_SERVER['HTTP_USER_AGENT'] ?? '-',
    $_SERVER['HTTP_REFERER'] ?? '-',
]) . PHP_EOL;

file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);

// ── Redirect ────────────────────────────────────────────────
header('HTTP/1.1 302 Found');
header('Location: ' . $destination);
header('Cache-Control: no-store, no-cache');
exit;

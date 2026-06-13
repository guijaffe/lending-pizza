<?php
$secret = 'giusto2026';
$sig = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
$body = file_get_contents('php://input');

if (!hash_equals('sha256=' . hash_hmac('sha256', $body, $secret), $sig)) {
    http_response_code(403);
    die('Forbidden');
}

// Pull latest changes
$output = shell_exec('cd /var/www/giustogusto.ru/welcome && git pull 2>&1');

// No file copying needed anymore, Nginx handles it directly

// Log
file_put_contents('/tmp/deploy.log', date('Y-m-d H:i:s') . "\n" . $output . "\n\n", FILE_APPEND);
echo $output;

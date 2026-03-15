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

// Sync coming-soon to root domain
shell_exec('cp /var/www/giustogusto.ru/welcome/coming-soon.html /var/www/giustogusto.ru/index.html 2>&1');

// Log
file_put_contents('/tmp/deploy.log', date('Y-m-d H:i:s') . "\n" . $output . "\n\n", FILE_APPEND);
echo $output;

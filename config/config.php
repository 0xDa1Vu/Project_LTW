<?php
/**
 * Cấu hình ứng dụng. Đọc biến môi trường (Docker truyền vào, hoặc file .env).
 */

// Nạp .env nếu tồn tại (đơn giản, không dùng thư viện ngoài)
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if ($line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }
        [$k, $v] = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        if (getenv($k) === false) {
            putenv("$k=$v");
            $_ENV[$k] = $v;
        }
    }
}

function env(string $key, $default = null) {
    $v = getenv($key);
    return $v === false ? $default : $v;
}

return [
    'app_url' => env('APP_URL', 'http://localhost:8000'),
    'app_env' => env('APP_ENV', 'local'),
    'db' => [
        'host' => env('DB_HOST', 'db'),
        'port' => env('DB_PORT', '5432'),
        'name' => env('DB_NAME', 'fashion_shop'),
        'user' => env('DB_USER', 'shop'),
        'pass' => env('DB_PASS', 'shop_secret'),
    ],
    'vnpay' => [
        'tmn_code'    => env('VNP_TMN_CODE', ''),
        'hash_secret' => env('VNP_HASH_SECRET', ''),
        'url'         => env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url'  => env('VNP_RETURN_URL', 'http://localhost:8000/payment/vnpay/return'),
    ],
];

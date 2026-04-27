<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'zzy_rental');
define('DB_USER', 'root');
define('DB_PASS', '');

// Load APP_URL from admin-managed generated config, or fall back to default
$_generatedUrlFile = __DIR__ . '/generated_url.php';
if (file_exists($_generatedUrlFile)) {
    $_appUrl = include $_generatedUrlFile;
    if ($_appUrl && is_string($_appUrl)) {
        define('APP_URL', rtrim($_appUrl, '/'));
    } else {
        define('APP_URL', 'http://localhost:8080/zzy_rental');
    }
} else {
    define('APP_URL', 'http://localhost:8080/zzy_rental');
}

// Derive the base path from APP_URL (e.g. "/zzy_rental" or "" for root domains)
define('APP_BASE_PATH', parse_url(APP_URL, PHP_URL_PATH) ?: '');

define('GOOGLE_MAPS_API_KEY', 'AIzaSyClaCBu0X-e_kdvTEPUtVsfPmwFD1iCYiI');

// Paystack Configuration
define('PAYSTACK_PUBLIC_KEY', 'pk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('PAYSTACK_SECRET_KEY', 'sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('PLATFORM_FEE_PERCENT', 5); // 5% platform fee

session_start();

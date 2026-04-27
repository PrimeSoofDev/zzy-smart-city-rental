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

// Derive the base path from the actual script location to ensure routing works 
// even if APP_URL is configured for a different domain (e.g. production vs local).
// SCRIPT_NAME is typically something like /zzy_rental/public/index.php
$_scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
$_basePath = dirname(dirname($_scriptPath)); // Go up two levels from /public/index.php
if ($_basePath === DIRECTORY_SEPARATOR || $_basePath === '.') {
    $_basePath = '';
}
define('APP_BASE_PATH', $_basePath);

define('GOOGLE_MAPS_API_KEY', 'AIzaSyClaCBu0X-e_kdvTEPUtVsfPmwFD1iCYiI');

// Paystack Configuration
define('PAYSTACK_PUBLIC_KEY', 'pk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('PAYSTACK_SECRET_KEY', 'sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('PLATFORM_FEE_PERCENT', 5); // 5% platform fee

session_start();

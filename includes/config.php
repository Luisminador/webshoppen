<?php
// Session-inställningar måste sättas innan session_start()
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // 0 för lokal utveckling
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
}

// Databaskonfiguration
define('DB_HOST', 'localhost');
define('DB_NAME', 'webshoppen');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_PORT', 8889);

// Webbplatsens bas-URL
define('BASE_URL', '/webshoppen/public');

// Felrapportering
error_reporting(E_ALL);
ini_set('display_errors', 1); // Ändrad till 1 för utveckling
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Tidzon
date_default_timezone_set('Europe/Stockholm');

// Konstanter för filsökvägar
define('UPLOAD_PATH', __DIR__ . '/../public/images/uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

return [
    'db_host' => 'localhost',
    'db_name' => 'webshoppen',
    'db_user' => 'root',
    'db_pass' => 'root',
    'db_port' => 8889,
    'db_charset' => 'utf8mb4',
    
    'app_name' => 'Webshoppen',
    'app_url' => 'http://localhost:8888/webshoppen',
    'base_url' => '/webshoppen',
    'debug_mode' => true,
    
    'session_lifetime' => 7200,
    'session_secure' => false,
    'session_httponly' => true,
]; 
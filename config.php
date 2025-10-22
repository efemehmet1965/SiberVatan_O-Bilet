<?php

// Composer autoload
require_once __DIR__ . '/vendor/autoload.php';

// Proje ana dizini
define('ROOT_PATH', __DIR__);

// Veritabanı dosyası yolu
define('DB_PATH', ROOT_PATH . '/database/bilet_platformu.sqlite');

// Site ana URL'i
if (getenv('DOCKER_ENV') === 'true') {
    // Docker ortamı için BASE_URL
    define('BASE_URL', 'http://localhost');
} else {
    // XAMPP veya diğer ortamlar için dinamik BASE_URL
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script_name = str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME']);
    define('BASE_URL', $protocol . $host . $script_name . '/public');
}

?>
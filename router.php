<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basit bir URL yönlendirici

// Get the full request URI from the 'url' parameter passed by Nginx
$request_uri = isset($_GET['url']) ? $_GET['url'] : '';

// Strip the project base path if it's present (common in some server configurations)
$base_path_to_strip = 'sibervatanbilet/public/';
if (strpos($request_uri, $base_path_to_strip) === 0) {
    $request_uri = substr($request_uri, strlen($base_path_to_strip));
}

// Parse the URI to remove any potential query string and get just the path
$path = parse_url($request_uri, PHP_URL_PATH);

// Trim leading/trailing slashes from the path
$path = trim($path, '/');

// If the path is empty after trimming (i.e., it was '/' or empty), default to the home page.
// Otherwise, use the cleaned path.
$url = !empty($path) ? $path : 'home/index';
$urlParts = explode('/', $url);

// Controller, Method ve Parametreleri belirle
$controllerName = !empty($urlParts[0]) ? ucfirst($urlParts[0]) . 'Controller' : 'HomeController';
$methodName = isset($urlParts[1]) ? $urlParts[1] : 'index';
$params = array_slice($urlParts, 2);

// Controller dosyasını dahil et
$controllerFile = ROOT_PATH . '/src/Controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
        // Veritabanı bağlantısını oluştur
        try {
            $pdo = new PDO('sqlite:' . DB_PATH);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Veritabanı bağlantı hatası: " . $e->getMessage());
        }

        $controller = new $controllerName($pdo);
        call_user_func_array([$controller, $methodName], $params);
    } else {
        http_response_code(404);
        echo "<h1>404 - Sayfa Bulunamadı</h1><p>Metot bulunamadı: {$methodName}</p>";
    }
} else {
    http_response_code(404);
    echo "<h1>404 - Sayfa Bulunamadı</h1><p>Denetleyici bulunamadı: {$controllerName}</p>";
}
?>
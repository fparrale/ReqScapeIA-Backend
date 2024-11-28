<?php
// Configuración de la API
require_once 'config/ApiConfig.php';
ApiConfig::cors();
ApiConfig::json();

// Cargar variables de entorno
require_once 'config/Environment.php';
Environment::loadEnv(__DIR__ . '/.env');

$uri = trim($_SERVER['REQUEST_URI'], '/');
$method = $_SERVER['REQUEST_METHOD'];

// Define route patterns and corresponding file paths
$routes = [
    // 'rooms/(\d+)' => 'api/rooms.php', // Example pattern for /rooms/:id
];

$matched = false;

foreach ($routes as $pattern => $filePath) {
    if (preg_match("#^$pattern$#", $uri, $matches)) {
        $matched = true;
        array_shift($matches); // Remove the full match
        $_GET['params'] = $matches; // Store parameters in $_GET['params']
        require $filePath;
        break;
    }
}

if (!$matched) {
    $basePath = "api/$uri";
    $methodFilePath = "$basePath.php";
    $indexFilePath = "$basePath/index.php";

    if (file_exists($methodFilePath)) {
        require $methodFilePath;
    } elseif (file_exists($indexFilePath)) {
        require $indexFilePath;
    } else {
        header("HTTP/1.0 404 Not Found");
        echo json_encode(['error' => 'Endpoint not found']);
    }
}

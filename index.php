<?php
require_once 'config/ApiConfig.php';
ApiConfig::cors();
ApiConfig::json();

require_once 'config/Environment.php';
Environment::loadEnv(__DIR__ . '/.env');

$uri = trim($_SERVER['REQUEST_URI'], '/');
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    'rooms/(\d+)' => 'api/rooms/index.php',
    'admin/course-content/(\d+)' => 'api/admin/course-content.php',
    'admin/course-stats/(\d+)' => 'api/admin/course-stats.php',
    'attempts/(\d+)' => 'api/attempts/index.php',
    'game/content/(\d+)' => 'api/game/content.php',
];

$matched = false;

foreach ($routes as $pattern => $filePath) {
    if (preg_match("#^$pattern$#", $uri, $matches)) {
        $matched = true;
        array_shift($matches);
        $_GET['params'] = $matches;
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
        header("HTTP/1.0 404 No encontrado");
        echo json_encode(['error' => 'Endpoint no encontrado']);
    }
}

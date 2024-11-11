<?php
// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
// JSON
header('Content-Type: application/json');

// Load environment variables
require_once 'config/Environment.php';
Environment::loadEnv(__DIR__ . '/.env');


$uri = trim($_SERVER['REQUEST_URI'], '/');
$method = $_SERVER['REQUEST_METHOD'];


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

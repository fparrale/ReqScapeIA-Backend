<?php
// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
// JSON
header('Content-Type: application/json');

// Load environment variables
require_once 'config/Environment.php';
Environment::loadEnv(__DIR__ . '/.env');

require_once 'routes.php';

$route = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

handleRoute($route, $method);
?>

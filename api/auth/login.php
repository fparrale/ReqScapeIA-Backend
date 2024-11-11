<?php
require_once 'controllers/AuthController.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    AuthController::login();
} else {
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode(['error' => 'Method not allowed']);
}

<?php

require_once 'controllers/RoomController.php';
require_once 'middleware/AuthMiddleware.php';

$payload = AuthMiddleware::validateToken();
$email = $payload['email'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    RoomController::deleteRoom($email);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}

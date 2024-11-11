<?php
require_once 'middleware/AuthMiddleware.php';
require_once 'controllers/RoomController.php';

$method = $_SERVER['REQUEST_METHOD'];

$payload = AuthMiddleware::validateToken();

$user_id = $payload['id'];
$email = $payload['email'];

switch ($method) {
    case 'GET': 
        RoomController::getAllRooms($user_id, $email);
        break;
    case 'POST':
        RoomController::createRoom($user_id, $email);
        break;
}

<?php
require_once 'middleware/AuthMiddleware.php';
require_once 'controllers/RoomController.php';

$payload = AuthMiddleware::validateToken();

RoomController::enroll($payload['id'], $payload['email']);
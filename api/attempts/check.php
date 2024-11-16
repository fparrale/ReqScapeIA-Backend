<?php
require_once 'controllers/AttemptController.php';
require_once 'middleware/AuthMiddleware.php';

$payload = AuthMiddleware::validateToken();
$user_id = $payload['id'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    AttemptController::checkAttemptsRemaining($user_id);
}

?>
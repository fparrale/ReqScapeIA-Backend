<?php
require_once 'controllers/AuthController.php';
require_once 'middleware/AuthMiddleware.php';

$payload = AuthMiddleware::validateToken();

$user_id = $payload['id'];
$email = $payload['email'];

AuthController::refreshToken($user_id, $email);

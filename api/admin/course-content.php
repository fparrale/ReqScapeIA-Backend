<?php
require_once 'controllers/AdminStatsController.php';
require_once 'middleware/AuthMiddleware.php';

$payload = AuthMiddleware::validateToken();
$email = $payload['email'];

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    AdminStatsController::getGeneratedRequirementsByCourse($email);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}

<?php
require_once 'middleware/AuthMiddleware.php';
require_once 'controllers/CourseController.php';

$payload = AuthMiddleware::validateToken();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    CourseController::existsAttempts($payload['id'], $payload['email']);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido.']);
}
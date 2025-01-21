<?php
require_once 'controllers/AdminStatsController.php';
require_once 'middleware/AuthMiddleware.php';

$payload = AuthMiddleware::validateToken();
$email = $payload['email'];
$userId = $payload['id'];

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        AdminStatsController::getGeneratedRequirementsByCourse($email);
        break;
    case 'DELETE':
        AdminStatsController::deleteCourseRequirement($userId);
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Método no permitido']);
}

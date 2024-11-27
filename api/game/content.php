<?php

require_once 'controllers/GameController.php';
require_once 'middleware/AuthMiddleware.php';

AuthMiddleware::validateToken();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    GameController::prepareGameContent();
}

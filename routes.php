<?php
require_once 'controllers/AuthController.php';
require_once 'controllers/UserController.php';

require_once 'middleware/AuthMiddleware.php';

function handleRoute($route, $method) {

    $authController = new AuthController();
    $userController = new UserController();


    // Public routes
    if ($method === 'POST' && $route === '/login') {
        $authController->login();
    }

    if ($method === 'POST' && $route === '/register') {
        $authController->register();
    }

    if ($method === 'GET' && $route === '/refresh-token') {
        $payload = AuthMiddleware::validateToken();
        $authController->refreshToken($payload['id'], $payload['email']);
    }

    
    // Protected routes
    if ($method === 'GET' && $route === '/users') {
        AuthMiddleware::validateToken();
        $userController->getAllUsers();
    }
}
?>




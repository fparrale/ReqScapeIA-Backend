<?php
require_once 'config/Database.php';
require_once 'entities/RoomEntity.php';
require_once 'services/RoomService.php';
require_once 'services/UserService.php';

class RoomController
{
    public static function createRoom($id, $email)
    {
        $isAdmin = UserService::isAdmin($email);

        if (!$isAdmin) {
            http_response_code(400);
            echo json_encode(['message' => 'Acceso denegado. Solo los administradores pueden crear salas.']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $room_code = $data['room_code'] ?? null;
        $room_name = $data['room_name'] ?? null;
        $max_attempts = $data['max_attempts'] ?? null;

        if (empty($room_name)) {
            http_response_code(400);
            echo json_encode(['message' => 'Nombre de sala es requerido.']);
            return;
        }

        if (empty($room_code)) {
            http_response_code(400);
            echo json_encode(['message' => 'Código de sala es requerido.']);
            return;
        }

        $room_exists = RoomService::getByCode($room_code);

        if ($room_exists) {
            http_response_code(400);
            echo json_encode(['message' => 'El código de sala ya existe.']);
            return;
        }

        $roomEntity = new RoomEntity(null, $room_name, $room_code, $max_attempts);
        $createdRoom = RoomService::create($roomEntity, $id);

        if (!$createdRoom) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al crear la sala.']);
            return;
        }

        http_response_code(201);
        echo json_encode($createdRoom);
        return;
    }

    public static function getAllRooms($id, $email)
    {
        $isAdmin = UserService::isAdmin($email);

        if (!$isAdmin) {
            http_response_code(400);
            echo json_encode(['message' => 'Acceso denegado. Solo los administradores pueden crear salas.']);
            return;
        }

        $rooms = RoomService::getAllByUserId($id);
        http_response_code(200);
        echo json_encode($rooms);
    }

    public static function joinRoom($id, $email)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $room_code = $data['room_code'] ?? null;

        if (!$room_code) {
            http_response_code(400);
            echo json_encode(['message' => 'Código de sala no proporcionado.']);
            return;
        }

        $room = RoomService::getByCode($room_code);

        if (!$room) {
            http_response_code(400);
            echo json_encode(['message' => 'La sala con el código proporcionado no existe.']);
            return;
        }

        $room_name = $room['room_name'];

        // Contar los intentos realizados por el usuario en esta sala
        $queryAttempts = "SELECT COUNT(*) AS total_attempts FROM tried WHERE user_id = :user_id AND room_id = :room_id";
        $stmtAttempts = Database::getConn()->prepare($queryAttempts);
        $stmtAttempts->bindParam(':user_id', $id);
        $stmtAttempts->bindParam(':room_id', $room['id']);
        $stmtAttempts->execute();
        $attempts = $stmtAttempts->fetch(PDO::FETCH_ASSOC);
        $totalAttempts = $attempts['total_attempts'];

        // Verificar si los intentos son ilimitados
        if ($room['max_attempts'] == -1) {
            http_response_code(200);
            echo json_encode([
                'message' => 'Intentos ilimitados en esta sala.',
                'attempts' => "$totalAttempts / ∞",
                'room_name' => "$room_name",
                'room_code' => "$room_code"
            ]);
            return;
        }

        // Verificar si el usuario ha alcanzado el límite de intentos
        if ($totalAttempts >= $room['max_attempts']) {
            http_response_code(403); // Código de estado HTTP 403: Forbidden
            echo json_encode([
                'message' => 'Acceso denegado: Has alcanzado el número máximo de intentos en esta sala.',
                'attempts' => "$totalAttempts / {$room['max_attempts']}",
                'room_name' => "$room_name",
                'room_code' => "$room_code" // TODO: Change to (0 or -1) or consider adding a boolean field.
            ]);
            return;
        }

        // Permitir el ingreso si le quedan intentos
        $remainingAttempts = $room['max_attempts'] - $totalAttempts;
        http_response_code(200);
        echo json_encode([
            'message' => 'Acceso permitido',
            'remaining_attempts' => $remainingAttempts,
            'attempts' => "$totalAttempts / {$room['max_attempts']}",
            'room_name' => "$room_name",
            'room_code' => "$room_code"
        ]);
        return;
    }
}

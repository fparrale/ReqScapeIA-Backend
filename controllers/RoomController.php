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

        $roomEntity = new RoomEntity($room_name, $room_code, $max_attempts);
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
        $rooms = RoomService::getAllByUserId($id);
        http_response_code(200);
        echo json_encode($rooms);
    }

    public static function getEnrolledRooms($id, $email)
    {
        $rooms = RoomService::getAllEnrolledByUserId($id);
        http_response_code(200);
        echo json_encode($rooms);
    }

    public static function enroll($id, $email)
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

        $enrolled = RoomService::enroll($id, $room['id']);

        if (!$enrolled) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al inscribirse en la sala.']);
            return;
        }

        http_response_code(200);
        echo json_encode(['message' => 'Inscripción exitosa.']);
    }
}

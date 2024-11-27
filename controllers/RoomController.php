<?php
require_once 'config/Database.php';
require_once 'entities/RoomEntity.php';
require_once 'entities/GameConfigEntity.php';
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
        $items_per_attempt = $data['items_per_attempt'] ?? null;
        $max_attempts = $data['max_attempts'] ?? null;

        $language = $data['language'] ?? null;
        $additional_context = $data['additional_context'] ?? null;

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

        $roomEntity = new RoomEntity($room_name, $room_code, $items_per_attempt, $max_attempts);
        $gameConfigEntity = new GameConfigEntity($language, $additional_context);
        $createdRoom = RoomService::create($roomEntity, $gameConfigEntity, $id);

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

    public static function deleteRoom($email)
    {
        $isAdmin = UserService::isAdmin($email);

        if (!$isAdmin) {
            http_response_code(400);
            echo json_encode(['message' => 'Acceso denegado. Solo los administradores pueden crear salas.']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $room_id = $data['room_id'] ?? null;

        if (!$room_id) {
            http_response_code(400);
            echo json_encode(['message' => 'ID de sala no proporcionado.']);
            return;
        }

        $deleted = RoomService::remove($room_id);

        if (!$deleted) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al eliminar la sala.']);
            return;
        }

        http_response_code(200);
        echo json_encode(['message' => 'Sala eliminada exitosamente.']);
    }
}

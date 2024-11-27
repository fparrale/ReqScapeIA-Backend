<?php

require_once 'entities/RoomEntity.php';
require_once 'entities/GameConfigEntity.php';
require_once 'config/Database.php';
require_once 'services/GptService.php';

class RoomService
{

    public static function create(RoomEntity $room, GameConfigEntity $gameConfig, $user_id)
    {
        $query = "INSERT INTO rooms (room_name, room_code, items_per_attempt, max_attempts, user_id) VALUES (:room_name, :room_code, :items_per_attempt, :max_attempts, :user_id)";
        $stmt = Database::getConn()->prepare($query);
        $stmt->bindParam(':room_name', $room->room_name);
        $stmt->bindParam(':room_code', $room->room_code);
        $stmt->bindParam(':items_per_attempt', $room->items_per_attempt);
        $stmt->bindParam(':max_attempts', $room->max_attempts);
        $stmt->bindParam(':user_id', $user_id);

        $roomId = null;
        if ($stmt->execute()) {
            $roomId = Database::getConn()->lastInsertId();
            $createdRoom = self::getById($roomId);
        } else {
            return null;
        }

        $response = GptService::generateRequirements($gameConfig);

        self::saveRequirements($roomId, $response['requirements']);

        return $createdRoom;
    }

    public static function getAllByUserId($user_id)
    {
        $query = "SELECT * FROM rooms WHERE user_id = :user_id";
        $stmt = Database::getConn()->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function getAllEnrolledByUserId($user_id)
    {
        $query = "SELECT rooms.id, rooms.room_name, rooms.room_code, rooms.created_at, rooms.max_attempts,
        rooms.user_id AS teacher_id,
        users.email AS teacher_email, 
        CONCAT(users.first_name, ' ', users.last_name) AS teacher_name
                 FROM enrolled_rooms 
                 JOIN rooms ON enrolled_rooms.room_id = rooms.id
                 JOIN users ON rooms.user_id = users.id
                 WHERE enrolled_rooms.user_id = :user_id";
        $stmt = Database::getConn()->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public static function enroll($user_id, $room_id)
    {
        $query = "INSERT INTO enrolled_rooms (user_id, room_id) VALUES (:user_id, :room_id)";
        $stmt = Database::getConn()->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':room_id', $room_id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public static function getByCode($room_code)
    {
        $query = "SELECT * FROM rooms WHERE room_code = :room_code";
        $stmt = Database::getConn()->prepare($query);
        $stmt->bindParam(':room_code', $room_code);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($result)) {
            return null;
        }
        return $result;
    }

    public static function getById($id)
    {
        $query = "SELECT * FROM rooms WHERE id = :id";
        $stmt = Database::getConn()->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($result)) {
            return null;
        }
        return $result;
    }

    public static function remove($id)
    {
        $query = "DELETE FROM rooms WHERE id = :id";
        $stmt = Database::getConn()->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public static function deleteAll()
    {
        $query = "DELETE FROM rooms";
        $stmt = Database::getConn()->prepare($query);
        $stmt->execute();
    }

    public static function saveRequirements($room_id, $requirements)
    {
        foreach ($requirements as $requirement) {
            $query = "INSERT INTO requirements (requirementText, isValid, feedbackText, room_id) VALUES (:requirementText, :isValid, :feedbackText, :room_id)";
            $stmt = Database::getConn()->prepare($query);
            $requirementText = htmlspecialchars($requirement['text']);
            $feedbackText = htmlspecialchars($requirement['feedback']);
            $stmt->bindParam(':requirementText', $requirementText);
            $stmt->bindParam(':isValid', $requirement['isValid'], PDO::PARAM_INT);
            $stmt->bindParam(':feedbackText', $feedbackText);
            $stmt->bindParam(':room_id', $room_id);

            if (!$stmt->execute()) {
                return false;
            }
        }
        return true;
    }
}

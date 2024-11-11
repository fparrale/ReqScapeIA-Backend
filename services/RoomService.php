<?php

require_once 'entities/RoomEntity.php';
require_once 'config/Database.php';

class RoomService
{
    public static function create(RoomEntity $room, $user_id)
    {
        $query = "INSERT INTO rooms (room_name, room_code, max_attempts, user_id) VALUES (:room_name, :room_code, :max_attempts, :user_id)";
        $stmt = Database::getConn()->prepare($query);
        $stmt->bindParam(':room_name', $room->room_name);
        $stmt->bindParam(':room_code', $room->room_code);
        $stmt->bindParam(':max_attempts', $room->max_attempts);
        $stmt->bindParam(':user_id', $user_id);
        if ($stmt->execute()) {
            $lastId = Database::getConn()->lastInsertId();
            return self::getById($lastId);
        } else {
            return null;
        }
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
}

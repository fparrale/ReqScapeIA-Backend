<?php

require_once 'config/Database.php';

class RoomController{
    private $db;

    public function __construct(){
        $this->db = (new DataBase()) -> getConnection();
    }

    public function saveTried($id, $email){
        $data = json_decode(file_get_contents('php://input'), true);
        $room_name = $data['room_name'] ?? null;
        $room_code = $data['room_code'] ?? null;
        $totalreq = $data['totalreq'] ?? null;
        $movements = $data['movements'] ?? null;
        $score = $data['score'] ?? null;
        $status = $data['status'] ?? null;
        $time = $data['time'] ?? null;

        $query = "INSERT INTO tried (user_id, room_id, totalreq, movements, score, status, time)
                VALUES (:user_id, :room_id, :totalreq, :movements, :score, :status, :time)";

        $stmt = $db->prepare($query);

        $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':room_id', $room_code, PDO::PARAM_INT);
        $stmt->bindParam(':totalreq', $totalreq, PDO::PARAM_INT);
        $stmt->bindParam(':movements', $movements, PDO::PARAM_INT);
        $stmt->bindParam(':score', $score, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);

        if($stmt->execute()){
            http_response_code(201);
            echo json_encode(['message' => 'Intento finalizado.']);
            return true;
        }else{
            http_response_code(500);
            echo json_encode(['message' => 'Error, no se puedo finalizar el intento']);
            return false;
        }
    }

    public function showStats($id, $email){
        $query = "SELECT t.id, t.room_id, t.totalreq, t.movements, t.score, t.status, t.time, r.room_name
                  FROM tried t
                  JOIN rooms r ON t.room_id = r.id
                  WHERE t.user_id = :user_id";

        $stmt = $db->prepare($query);

        $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);

        if($stmt->execute()){
            http_response_code(201);
            echo json_encode(['message' => 'Intento finalizado.']);
            return true;
        }else{
            http_response_code(500);
            echo json_encode(['message' => 'Error']);
            return false;
        }
    }

    public function showAllStats($id, $email){

    }
}

?>
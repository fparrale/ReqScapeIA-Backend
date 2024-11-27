<?php

require_once 'config/Database.php';
require_once 'services/RoomService.php';
class GameService
{
    public static function prepareGameContent($roomCode)
    {
        $room = RoomService::getByCode($roomCode);

        if ($room === null) {
            http_response_code(404);
            echo json_encode(['message' => 'Room not found']);
            exit;
        }

        $items_per_attempt = $room['items_per_attempt'];

        $sql = "SELECT * FROM requirements WHERE room_id = :room_id ORDER BY RAND() LIMIT $items_per_attempt";
        $stmt = Database::getConn()->prepare($sql);
        $stmt->bindParam(':room_id', $room['id']);
        $stmt->execute();
        $requirements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $formattedRequirements = [];
        $no = 1;

        foreach ($requirements as $requirement) {
            $formattedRequirements[] = [
                'no' => $no++,
                'text' => $requirement['requirementText'],
                'isValid' => $requirement['isValid'] == 1 ? true : false,
                'feedback' => $requirement['feedbackText'],
            ];
        }

        http_response_code(200);
        echo json_encode($formattedRequirements);
    }
}

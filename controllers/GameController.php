<?php

require_once 'services/GameService.php';

class GameController
{
    public static function prepareGameContent()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $roomCode = $data['roomCode'];

        return GameService::prepareGameContent($roomCode);
    }
}

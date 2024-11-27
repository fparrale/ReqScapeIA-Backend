<?php

class RoomEntity
{
    public $id;
    public $room_name;
    public $room_code;
    public $items_per_attempt;
    public $max_attempts;

    public function __construct($room_name, $room_code, $items_per_attempt, $max_attempts)
    {
        $this->room_name = $room_name;
        $this->room_code = $room_code;
        $this->items_per_attempt = $items_per_attempt;
        $this->max_attempts = $max_attempts;
    }
}

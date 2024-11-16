<?php

class AttemptEntity {

    public $id;
    public $user_id;
    public $room_id;
    public $totalreq;
    public $movements;
    public $score;
    
    public $status;
    public $time;
    public $created_at;

    public function __construct($user_id, $room_id, $totalreq, $movements, $score) {
        $this->user_id = $user_id;
        $this->room_id = $room_id;
        $this->totalreq = $totalreq;
    }



}
<?php

class ResponseEntity
{
    public $id;
    public $userId;
    public $surveyQuestionId;
    public $response;

    public function __construct($id, $userId, $surveyQuestionId, $response)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->surveyQuestionId = $surveyQuestionId;
        $this->response = $response;
    }
}

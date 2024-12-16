<?php
require_once 'services/SurveyService.php';

class SurveyController
{
    public static function markSurveySubmissionAsCompleted($userId)
    {
        SurveyService::markSurveySubmissionAsCompleted($userId);
        http_response_code(200);
        echo json_encode(['message' => 'Encuesta registrada correctamente.']);
    }

    public static function getSurveyQuestions()
    {
        $questions = SurveyService::getSurveyQuestions();
        http_response_code(200);
        echo json_encode($questions);
    }

    public static function saveSurveyResponses($userId)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $rawResponses = $data['responses'];
        $responses = array_map(function ($response) use ($userId) {
            return new ResponseEntity($response['id'], $userId, $response['surveyQuestionId'], $response['response']);
        }, $rawResponses);

        SurveyService::saveSurveyResponses($userId, $responses);
        http_response_code(200);
        echo json_encode(['message' => 'Respuestas de la encuesta registradas correctamente.']);
    }
}

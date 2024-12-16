<?php

require_once 'config/Database.php';

class SurveyService
{
    public static function markSurveySubmissionAsCompleted($userId)
    {
        $query = "INSERT INTO survey_submissions (user_id) VALUES (:userId)";
        $stmt = Database::getConn()->prepare($query);
        $stmt->bindParam(':userId', $userId);
        
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['message' => 'Error al registrar la respuesta de la encuesta.']);
            exit;
        }

        return true;
    }

    public static function getSurveyQuestions()
    {
        $query = "SELECT * FROM survey_questions";
        $stmt = Database::getConn()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function saveSurveyResponses($userId, array $responses)
    {
        Database::getConn()->beginTransaction();
        try {
            foreach ($responses as $response) {
                $query = "INSERT INTO user_responses (user_id, survey_question_id, response) VALUES (:userId, :surveyQuestionId, :response)";
                $stmt = Database::getConn()->prepare($query);
                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':surveyQuestionId', $response->surveyQuestionId);
                $stmt->bindParam(':response', $response->response);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to execute query');
                }
            }
            Database::getConn()->commit();
        } catch (Exception $e) {
            Database::getConn()->rollBack();
            http_response_code(500);
            echo json_encode(['message' => 'Error al registrar las respuestas de la encuesta.']);
            exit;
        }
    }
}

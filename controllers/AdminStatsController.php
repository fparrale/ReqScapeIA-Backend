<?php

require_once 'services/AdminService.php';

class AdminStatsController
{
    public static function getStatsByCourse($email)
    {
        $courseId = $_GET['params'][0];

        $stats = AdminService::getStatsByCourse($email, $courseId);
        http_response_code(200);
        echo json_encode($stats);
    }

    public static function getStudentsByCourse($email)
    {
        $courseId = $_GET['params'][0];

        $students = AdminService::getStudentsByCourse($email, $courseId);
        http_response_code(200);
        echo json_encode($students);
    }

    public static function getStudentById($email)
    {
        $studentId = $_GET['params'][0];

        $student = AdminService::getStudentById($email, $studentId);
        http_response_code(200);
        echo json_encode($student);
    }

    public static function getStudentGameHistory($email)
    {
        $courseId = $_GET['params'][0];
        $studentId = $_GET['params'][1];

        $history = AdminService::getStudentGameHistory($email, $courseId, $studentId);
        http_response_code(200);
        echo json_encode($history);
    }

    public static function getGeneratedRequirementsByCourse($email)
    {
        $courseId = $_GET['params'][0];

        $requirements = AdminService::getGeneratedRequirementsByCourse($email, $courseId);
        http_response_code(200);
        echo json_encode($requirements);
    }

    public static function editRequirement($email)
    {
        $requirementId = $_GET['params'][0];
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['text']) || empty($data['feedback']) || !isset($data['isValid'])) {
            http_response_code(400);
            echo json_encode(['message' => 'No se proporcionaron los datos necesarios']);
            exit;
        }

        $requirement = new RequirementEntity($requirementId, $data['text'], $data['isValid'], $data['feedback']);
        
        AdminService::editRequirement($email, $requirement);
        http_response_code(200);
        echo json_encode(['message' => 'Requisito editado con éxito']);
    }

    public static function deleteCourseRequirement($userId)
    {
        
        $courseId = $_GET['params'][0] ?? null;
        $requirementId = $_GET['params'][1] ?? null;

        if (empty($courseId) || empty($requirementId)) {
            http_response_code(400);
            echo json_encode(['message' => 'Formato no válido para courseId o requirementId']);
            exit;
        }
        
        AdminService::deleteCourseRequirement($userId, $courseId, $requirementId);
        http_response_code(200);
        echo json_encode(['message' => 'Requisito eliminado con éxito']);
    }

    public static function getAttemptResult($email)
    {
        $attemptId = $_GET['params'][0];

        $result = AdminService::getAttemptResult($email, $attemptId);
        http_response_code(200);
        echo json_encode($result);
    }
}

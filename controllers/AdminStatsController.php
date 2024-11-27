<?php

require_once 'services/AdminService.php';

class AdminStatsController
{
    public static function getStatsByCourse($email)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $courseId = $data['course_id'];

        $stats = AdminService::getStatsByCourse($email, $courseId);
        http_response_code(200);
        echo json_encode($stats);
    }

    public static function getGeneratedRequirementsByCourse($email)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $courseId = $data['course_id'];

        $requirements = AdminService::getGeneratedRequirementsByCourse($email, $courseId);
        http_response_code(200);
        echo json_encode($requirements);
    }
}
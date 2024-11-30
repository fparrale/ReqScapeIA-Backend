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

    public static function getGeneratedRequirementsByCourse($email)
    {
        $courseId = $_GET['params'][0];

        $requirements = AdminService::getGeneratedRequirementsByCourse($email, $courseId);
        http_response_code(200);
        echo json_encode($requirements);
    }
}
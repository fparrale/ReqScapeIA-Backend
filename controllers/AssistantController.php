<?php
require_once 'services/GptService.php';

class AssistantController
{
    private static $gptService;

    private static function initService()
    {
        if (!isset(self::$gptService)) {
            self::$gptService = new GptService();
        }
    }

    public static function generateRequirements()
    {
        self::initService();
        $requirements = self::$gptService->generateRequirements();

        http_response_code(200);
        echo json_encode($requirements, JSON_PRETTY_PRINT);
    }
}

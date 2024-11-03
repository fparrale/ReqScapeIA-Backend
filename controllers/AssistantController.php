<?php
require_once 'services/GptService.php';

class AssistantController {

    private $gptService;

    public function __construct() {
        $this->gptService = new GptService();
    }

    public function generateRequeriments() {
        $requeriments = $this->gptService->generateRequeriments();

        http_response_code(200);
        echo json_encode($requeriments, JSON_PRETTY_PRINT);
    }
}
<?php

class GptService
{
    private $apiKey;
    private $assistantId;
    private $threadsEndpoint = 'https://api.openai.com/v1/threads';

    private $ch;

    public function __construct()
    {
        $this->apiKey = getenv('OPENAI_API_KEY');
        $this->assistantId = getenv('SOF_REQ_ASSISTANT_ID');
    }

    private function prepareAPI($endpoint, $method, $additionalHeaders = [], $data = [])
    {
        $this->ch = curl_init($endpoint);

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'POST') {
            curl_setopt($this->ch, CURLOPT_POST, true);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
        } else {
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey,
        ];

        if (!empty($additionalHeaders)) {
            $headers = array_merge($headers, $additionalHeaders);
        }

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
    }

    private function createThread()
    {
        $this->prepareAPI($this->threadsEndpoint, 'POST', [
            'OpenAI-Beta: assistants=v2',
        ]);
        $response = curl_exec($this->ch);

        if (curl_errno($this->ch)) {
            throw new Exception('Error creating thread: ' . curl_error($this->ch));
        }

        $response = json_decode($response, true);
        return $response['id'];
    }

    private function createRun($threadId, $assistantId)
    {
        $endpoint = $this->threadsEndpoint . '/' . $threadId . '/runs';

        $data = [
            'assistant_id' => $assistantId,
        ];

        $this->prepareAPI($endpoint, 'POST', [
            'OpenAI-Beta: assistants=v2',
        ], $data);
        $response = curl_exec($this->ch);

        if (curl_errno($this->ch)) {
            throw new Exception('Error creating run: ' . curl_error($this->ch));
        }

        $response = json_decode($response, true);
        return $response['id'];
    }

    private function checkCompleteStatus($threadId, $runId)
    {
        $endpoint = $this->threadsEndpoint . '/' . $threadId . '/runs/' . $runId;

        $this->prepareAPI($endpoint, 'GET', [
            'OpenAI-Beta: assistants=v2',
        ]);
        $response = curl_exec($this->ch);

        if (curl_errno($this->ch)) {
            throw new Exception('Error checking run status: ' . curl_error($this->ch));
        }

        $response = json_decode($response, true);

        if ($response['status'] === 'completed') {
            return $response['status'];
        }

        // Sleep for 1 second before checking again
        sleep(1);
        return $this->checkCompleteStatus($threadId, $runId);
    }

    private function getGeneratedConent($threadId)
    {
        $endpoint = $this->threadsEndpoint . '/' . $threadId . '/messages';

        $this->prepareAPI($endpoint, 'GET', [
            'OpenAI-Beta: assistants=v2',
        ]);
        $response = curl_exec($this->ch);

        if (curl_errno($this->ch)) {
            throw new Exception('Error getting message list: ' . curl_error($this->ch));
        }

        $response = json_decode($response, true);
        $firstMessage = $response['data'][0];
        $content = $firstMessage['content'][0]['text']['value'];

        return $content;
    }

    public function generateRequirements()
    {
        $threadId = $this->createThread();
        $runId = $this->createRun($threadId, $this->assistantId);

        $this->checkCompleteStatus($threadId, $runId);

        $content = $this->getGeneratedConent($threadId);

        return json_decode($content, true);
    }
}

<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Services\TutorBot;

class ChatController extends BaseController
{
    private TutorBot $tutorBot;

    public function __construct()
    {
        parent::__construct();
        $this->tutorBot = new TutorBot();
    }

    public function respond(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Phương thức không được hỗ trợ.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data) || !$this->validateCsrfToken($data['csrf_token'] ?? null)) {
            http_response_code(400);
            echo json_encode(['error' => 'Yêu cầu không hợp lệ.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $message = trim((string)($data['message'] ?? ''));
        if ($message === '') {
            http_response_code(422);
            echo json_encode(['error' => 'Vui lòng nhập nội dung trao đổi.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $history = $_SESSION['chat_history'] ?? [];
        $history[] = ['role' => 'user', 'message' => $message];
        $reply = $this->tutorBot->respond($message, $history);
        $history[] = ['role' => 'assistant', 'message' => $reply];
        $_SESSION['chat_history'] = array_slice($history, -20);

        echo json_encode([
            'reply' => $reply,
        ], JSON_UNESCAPED_UNICODE);
    }
}

<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

use ChemLearn\Models\ChatbotModel;

class ChatbotController extends BaseController
{
    private ChatbotModel $chatbot;

    public function __construct()
    {
        parent::__construct();
        $this->chatbot = new ChatbotModel();
    }

    public function ask(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Phương thức không được hỗ trợ.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $sessionToken = $_SESSION['csrf'] ?? $_SESSION['csrf_token'] ?? '';
        $requestToken = $_POST['csrf'] ?? '';

        if ($sessionToken === '' || !hash_equals((string) $sessionToken, (string) $requestToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'CSRF token không hợp lệ.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $message = trim((string)($_POST['message'] ?? ''));
        if ($message === '') {
            echo json_encode([
                'ok' => false,
                'answer' => 'Bạn hãy nhập nội dung câu hỏi nhé.',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $answer = $this->chatbot->findAnswer($message);
        if ($answer === null) {
            $answer = 'Câu hỏi này hơi nâng cao. Bạn thử đặt cụ thể hơn hoặc tra cứu thêm trong các chuyên mục Bảng tuần hoàn, ' .
                'Phương trình, Cân bằng PTHH nhé!';
        }

        echo json_encode([
            'ok' => true,
            'answer' => $answer,
        ], JSON_UNESCAPED_UNICODE);
    }
}

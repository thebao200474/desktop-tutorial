<?php // Mở thẻ PHP

declare(strict_types=1); // Bật strict types để hạn chế lỗi kiểu dữ liệu

namespace ChemLearn\Controllers; // Khai báo namespace của controller

use ChemLearn\Models\ChatbotModel; // Sử dụng model Chatbot để tìm câu trả lời

class ChatbotController extends BaseController // Controller xử lý API hỏi đáp bong bóng chat
{
    private ChatbotModel $chatbot; // Thuộc tính model chatbot

    public function __construct() // Hàm khởi tạo
    {
        parent::__construct(); // Khởi tạo BaseController (session, CSRF...)
        $this->chatbot = new ChatbotModel(); // Tạo instance model chatbot
    }

    public function ask(): void // Endpoint POST /chatbot/ask
    {
        header('Content-Type: application/json; charset=utf-8'); // Trả về JSON UTF-8

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Chỉ chấp nhận POST
            http_response_code(405); // Sai phương thức => 405
            echo json_encode(['error' => 'Phương thức không được hỗ trợ.'], JSON_UNESCAPED_UNICODE); // Trả lỗi
            return; // Dừng xử lý
        }

        $sessionToken = $_SESSION['csrf'] ?? $_SESSION['csrf_token'] ?? ''; // Lấy token CSRF trong session
        $requestToken = $_POST['csrf'] ?? ''; // Token gửi từ form

        if ($sessionToken === '' || !hash_equals((string) $sessionToken, (string) $requestToken)) { // So khớp token
            http_response_code(403); // Sai token => 403
            echo json_encode(['error' => 'CSRF token không hợp lệ.'], JSON_UNESCAPED_UNICODE); // Thông báo lỗi
            return; // Dừng xử lý
        }

        $message = trim((string)($_POST['message'] ?? '')); // Lấy nội dung câu hỏi
        if ($message === '') { // Nếu bỏ trống
            echo json_encode([
                'ok' => false,
                'answer' => 'Bạn hãy nhập nội dung câu hỏi nhé.',
            ], JSON_UNESCAPED_UNICODE); // Nhắc nhập nội dung
            return; // Không xử lý tiếp
        }

        $answer = $this->chatbot->findAnswer($message); // Tìm câu trả lời theo từ khóa
        if ($answer === null) { // Nếu không khớp từ khóa nào
            $answer = 'Câu hỏi này hơi nâng cao. Bạn thử đặt cụ thể hơn hoặc tra cứu thêm trong các chuyên mục Bảng tuần hoàn, ' .
                'Phương trình, Cân bằng PTHH nhé!'; // Trả về gợi ý chung
        }

        echo json_encode([
            'ok' => true,
            'answer' => $answer,
        ], JSON_UNESCAPED_UNICODE); // Trả kết quả JSON cho client
    }
}

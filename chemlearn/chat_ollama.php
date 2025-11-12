<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'error' => 'Phương thức không được hỗ trợ. Vui lòng sử dụng POST.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$rawInput = file_get_contents('php://input');
if ($rawInput === false) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Không thể đọc dữ liệu gửi lên.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode($rawInput, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Định dạng dữ liệu không hợp lệ.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$message = isset($data['message']) ? trim((string) $data['message']) : '';
if ($message === '') {
    http_response_code(422);
    echo json_encode([
        'error' => 'Vui lòng nhập nội dung câu hỏi.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$history = [];
if (isset($data['history']) && is_array($data['history'])) {
    foreach ($data['history'] as $entry) {
        if (!is_array($entry)) {
            continue;
        }

        $role = $entry['role'] ?? '';
        $content = isset($entry['content']) ? trim((string) $entry['content']) : '';

        if ($content === '') {
            continue;
        }

        if (!in_array($role, ['user', 'assistant'], true)) {
            continue;
        }

        $history[] = [
            'role' => $role,
            'content' => $content,
        ];
    }
}

$messages = array_merge(
    [
        [
            'role' => 'system',
            'content' => 'Bạn là ChemLearn AI - gia sư Hóa học thân thiện. Hãy trả lời bằng tiếng Việt, rõ ràng, có thể gợi ý thêm thí nghiệm hoặc mẹo ghi nhớ nếu phù hợp.',
        ],
    ],
    $history,
    [
        [
            'role' => 'user',
            'content' => $message,
        ],
    ]
);

$payload = json_encode([
    'model' => 'qwen2.5:7b-instruct',
    'messages' => $messages,
    'stream' => false,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($payload === false) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Không thể mã hóa dữ liệu gửi đến Ollama.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$ch = curl_init('http://127.0.0.1:11434/api/chat');
if ($ch === false) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Không thể khởi tạo kết nối tới Ollama.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
    ],
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_TIMEOUT => 30,
]);

$responseBody = curl_exec($ch);

if ($responseBody === false) {
    $errorMessage = curl_error($ch);
    curl_close($ch);

    http_response_code(502);
    echo json_encode([
        'error' => 'Không thể lấy phản hồi từ Ollama: ' . ($errorMessage !== '' ? $errorMessage : 'Không xác định'),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 400) {
    http_response_code(502);
    echo json_encode([
        'error' => 'Ollama trả về mã lỗi ' . $httpCode . '.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$decoded = json_decode($responseBody, true);
if (!is_array($decoded)) {
    http_response_code(502);
    echo json_encode([
        'error' => 'Không thể phân tích phản hồi từ Ollama.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$reply = '';

if (isset($decoded['message']['content']) && is_string($decoded['message']['content'])) {
    $reply = trim($decoded['message']['content']);
} elseif (isset($decoded['choices'][0]['message']['content']) && is_string($decoded['choices'][0]['message']['content'])) {
    $reply = trim($decoded['choices'][0]['message']['content']);
}

if ($reply === '') {
    http_response_code(502);
    echo json_encode([
        'error' => 'Ollama chưa trả về nội dung trả lời.',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'reply' => $reply,
], JSON_UNESCAPED_UNICODE);

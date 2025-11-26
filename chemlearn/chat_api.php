<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

http_response_code(410);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'error' => 'API cũ không còn được hỗ trợ. Vui lòng gửi yêu cầu tới endpoint mới /chatbot/ask.',
], JSON_UNESCAPED_UNICODE);

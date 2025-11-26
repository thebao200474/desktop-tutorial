<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$_SESSION['flash_message'] = 'Trang làm câu hỏi đã được gỡ. Vui lòng luyện đề tại mục Đề thi.';
header('Location: ' . app_url());
exit;

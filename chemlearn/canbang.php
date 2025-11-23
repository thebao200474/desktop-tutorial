<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$_SESSION['flash_message'] = 'Trang cân bằng phương trình đã được gỡ. Bạn có thể ôn luyện bằng các đề thi mới.';
header('Location: ' . app_url());
exit;

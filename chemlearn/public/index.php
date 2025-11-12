<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Bramus\Router\Router;

$router = new Router();

$router->setBasePath('/chemlearn');

$router->get('/health', static fn() => print('OK'));

$router->get('/', static function (): void {
    echo '<h1>Trang chủ ChemLearn</h1><p>Router chạy OK.</p>
        <ul>
          <li><a href="/chemlearn/periodic-table">/periodic-table</a></li>
          <li>POST /chemlearn/ai/ask (body: {"question":"Liên kết ion là gì?"})</li>
        </ul>';
});

$router->get('/periodic-table', static function (): void {
    echo '<h2>Bảng tuần hoàn (demo)</h2><p>Trang hiện được là router đã đúng.</p>';
});

$router->post('/ai/ask', static function (): void {
    header('Content-Type: application/json; charset=utf-8');
    $input = json_decode(file_get_contents('php://input'), true);
    $q = strtolower(trim($input['question'] ?? ''));
    $ans = 'Xin lỗi, mình chỉ hỗ trợ câu hỏi hóa học cơ bản nha.';
    if (str_contains($q, 'ion')) {
        $ans = 'Liên kết ion là lực hút giữa cation và anion.';
    }
    echo json_encode(['answer' => $ans], JSON_UNESCAPED_UNICODE);
});

$router->run();

<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use Bramus\Router\Router;
use ChemLearn\Controllers\ChatbotController;
use ChemLearn\Controllers\HoiDapController;
use ChemLearn\Controllers\HomeController;
use ChemLearn\Controllers\PeriodicTableController;
use ChemLearn\Controllers\PhuongTrinhController;

$router = new Router();

$router->get('/', static function (): void {
    (new HomeController())->index();
});

$router->get('/index.php', static function (): void {
    (new HomeController())->index();
});

$router->get('/periodic-table', static function (): void {
    (new PeriodicTableController())->index();
});

$router->get('/phuongtrinh', static function (): void {
    (new PhuongTrinhController())->index();
});

$router->get('/hoi-dap', static function (): void {
    (new HoiDapController())->index();
});

$router->get('/hoi-dap/hoi', static function (): void {
    (new HoiDapController())->create();
});

$router->post('/hoi-dap/hoi', static function (): void {
    (new HoiDapController())->store();
});

$router->get('/hoi-dap/(\d+)', static function (int $id): void {
    (new HoiDapController())->show($id);
});

$router->post('/hoi-dap/(\d+)', static function (int $id): void {
    (new HoiDapController())->answer($id);
});

$router->post('/hoi-dap/(\d+)/xoa', static function (int $id): void {
    (new HoiDapController())->delete($id);
});

$router->post('/chatbot/ask', static function (): void {
    (new ChatbotController())->ask();
});

$router->set404(static function (): void {
    http_response_code(404);
    echo '<h1>404</h1><p>Không tìm thấy trang yêu cầu.</p>';
});

$router->run();

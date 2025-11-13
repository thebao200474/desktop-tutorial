<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use Bramus\Router\Router;
use ChemLearn\Controllers\ChatController;
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

$router->post('/ai/ask', static function (): void {
    (new ChatController())->respond();
});

$router->set404(static function (): void {
    http_response_code(404);
    echo '<h1>404</h1><p>Không tìm thấy trang yêu cầu.</p>';
});

$router->run();

<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use Bramus\Router\Router;
use ChemLearn\Controllers\HomeController;
use ChemLearn\Controllers\PeriodicTableController;

$router = new Router();

$router->get('/', function (): void {
    $controller = new HomeController();
    $controller->index();
});

$router->get('/periodic-table', [PeriodicTableController::class, 'index']);

$router->set404(static function (): void {
    http_response_code(404);
    echo '<h1>404</h1><p>Không tìm thấy trang yêu cầu.</p>';
});

$router->run();

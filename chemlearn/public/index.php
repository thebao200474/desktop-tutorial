<?php
// File front controller chịu trách nhiệm khởi động ứng dụng và định tuyến yêu cầu.

// Nạp autoload do Composer sinh ra để sử dụng Router và các controller.
require_once __DIR__ . '/../vendor/autoload.php';

// Sử dụng lớp Router của thư viện Bramus để quản lý route.
use Bramus\Router\Router;
use ChemLearn\Controllers\HomeController;

// Khởi tạo đối tượng Router.
$router = new Router();

// Khởi tạo controller trang chủ để tái sử dụng trong các route.
$homeController = new HomeController();

// Đăng ký route GET cho đường dẫn gốc '/', hiển thị trang chủ.
$router->get('/', function () use ($homeController) {
    $homeController->index();
});

// Đăng ký route GET cho '/home' để trỏ về cùng trang chủ.
$router->get('/home', function () use ($homeController) {
    $homeController->index();
});

// Thiết lập trang 404 đơn giản khi route không tồn tại.
$router->set404(function () {
    http_response_code(404);
    echo '404 - Trang không tồn tại';
});

// Chạy router để xử lý request hiện tại.
$router->run();

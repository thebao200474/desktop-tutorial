<?php
// File front controller chịu trách nhiệm khởi động ứng dụng và định tuyến yêu cầu.

// Nạp autoload do Composer sinh ra để sử dụng Router, controller và model.
require_once __DIR__ . '/../vendor/autoload.php';

use Bramus\Router\Router;
use ChemLearn\Controllers\HomeController;
use ChemLearn\Controllers\TopicController;
use ChemLearn\Models\LessonModel;
use ChemLearn\Models\TopicModel;

// Thiết lập kết nối PDO (có thể cấu hình qua biến môi trường khi triển khai thực tế).
$dsn = getenv('CHEMLEARN_DSN') ?: 'sqlite::memory:';
$username = getenv('CHEMLEARN_DB_USER') ?: null;
$password = getenv('CHEMLEARN_DB_PASS') ?: null;
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => true,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $exception) {
    http_response_code(500);
    echo 'Không thể kết nối cơ sở dữ liệu';
    return;
}

// Khởi tạo đối tượng Router.
$router = new Router();

// Khởi tạo các controller và model cần thiết cho ứng dụng.
$homeController = new HomeController();
$topicModel = new TopicModel($pdo);
$lessonModel = new LessonModel($pdo);
$topicController = new TopicController($topicModel, $lessonModel);

// Đăng ký route GET cho đường dẫn gốc '/', hiển thị trang chủ.
$router->get('/', function () use ($homeController) {
    $homeController->index();
});

// Đăng ký route GET cho '/home' để trỏ về cùng trang chủ.
$router->get('/home', function () use ($homeController) {
    $homeController->index();
});

// Route hiển thị chi tiết từng chủ đề: /topics/{id}.
$router->get('/topics/(\d+)', function (int $id) use ($topicController) {
    $topicController->show($id);
});

// Thiết lập trang 404 đơn giản khi route không tồn tại.
$router->set404(function () {
    http_response_code(404);
    echo '404 - Trang không tồn tại';
});

// Chạy router để xử lý request hiện tại.
$router->run();

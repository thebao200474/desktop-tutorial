<?php

namespace Bramus\Router;

/**
 * Phiên bản rút gọn của Bramus Router dùng để định tuyến cơ bản cho dự án ChemLearn.
 * Lớp này hỗ trợ đăng ký route GET đơn giản và xử lý 404.
 */
class Router
{
    /** @var array<int, array{method:string, pattern:string, handler:callable}> Danh sách các route đã đăng ký */
    protected array $routes = [];

    /** @var callable|null Hàm xử lý khi không khớp route nào */
    protected $notFoundHandler = null;

    /**
     * Đăng ký route GET với pattern và handler tương ứng.
     */
    public function get(string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'pattern' => $this->normalizePattern($pattern),
            'handler' => $handler,
        ];
    }

    /**
     * Thiết lập handler cho trang 404.
     */
    public function set404(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    /**
     * Thực thi router: tìm route phù hợp với request hiện tại và gọi handler.
     */
    public function run(): void
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $normalizedUri = $this->normalizePattern($requestUri);

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['pattern'] === $normalizedUri) {
                call_user_func($route['handler']);
                return;
            }
        }

        if ($this->notFoundHandler !== null) {
            call_user_func($this->notFoundHandler);
            return;
        }

        http_response_code(404);
        echo '404 - Not Found';
    }

    /**
     * Chuẩn hóa đường dẫn bằng cách đảm bảo có dấu gạch chéo đầu và bỏ dấu gạch chéo cuối.
     */
    protected function normalizePattern(string $pattern): string
    {
        $pattern = trim($pattern);
        if ($pattern === '') {
            return '/';
        }

        // Bảo đảm đường dẫn bắt đầu bằng '/'.
        if ($pattern[0] !== '/') {
            $pattern = '/' . $pattern;
        }

        // Loại bỏ dấu gạch chéo cuối trừ trường hợp là root '/'.
        if (strlen($pattern) > 1) {
            $pattern = rtrim($pattern, '/');
        }

        return $pattern;
    }
}

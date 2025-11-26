<?php

declare(strict_types=1);

namespace Bramus\Router;

class Router
{
    /** @var array<int, array{methods: array<int, string>, pattern: string, handler: mixed}> */
    private array $routes = [];

    /** @var callable|null */
    private $notFoundHandler = null;

    private string $basePath = '';

    public function __construct()
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        if ($basePath === '.' || $basePath === '/') {
            $basePath = '';
        }
        $this->basePath = $basePath;
    }

    public function get(string $pattern, $handler): self
    {
        return $this->map(['GET', 'HEAD'], $pattern, $handler);
    }

    public function post(string $pattern, $handler): self
    {
        return $this->map(['POST'], $pattern, $handler);
    }

    public function put(string $pattern, $handler): self
    {
        return $this->map(['PUT'], $pattern, $handler);
    }

    public function delete(string $pattern, $handler): self
    {
        return $this->map(['DELETE'], $pattern, $handler);
    }

    public function patch(string $pattern, $handler): self
    {
        return $this->map(['PATCH'], $pattern, $handler);
    }

    public function options(string $pattern, $handler): self
    {
        return $this->map(['OPTIONS'], $pattern, $handler);
    }

    /**
     * @param array<int, string> $methods
     * @param callable|array|string $handler
     */
    public function map(array $methods, string $pattern, $handler): self
    {
        $this->routes[] = [
            'methods' => array_map('strtoupper', $methods),
            'pattern' => $this->formatPattern($pattern),
            'handler' => $handler,
        ];

        return $this;
    }

    public function set404(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function run(): void
    {
        $requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        if ($this->basePath !== '' && str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath)) ?: '/';
        }

        if ($uri === '') {
            $uri = '/';
        }

        if ($uri !== '/' && str_ends_with($uri, '/')) {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            if (!in_array($requestMethod, $route['methods'], true)) {
                continue;
            }

            $pattern = $route['pattern'];
            if ($pattern === $uri) {
                $this->invoke($route['handler']);
                return;
            }
        }

        if ($this->notFoundHandler) {
            ($this->notFoundHandler)();
            return;
        }

        http_response_code(404);
        echo '404 Not Found';
    }

    private function formatPattern(string $pattern): string
    {
        if ($pattern === '') {
            return '/';
        }
        if ($pattern[0] !== '/') {
            $pattern = '/' . $pattern;
        }
        return rtrim($pattern, '/') ?: '/';
    }

    /**
     * @param callable|array|string $handler
     */
    private function invoke($handler): void
    {
        if (is_callable($handler)) {
            $handler();
            return;
        }

        if (is_array($handler) && isset($handler[0], $handler[1]) && is_string($handler[1])) {
            [$class, $method] = $handler;
            if (is_string($class) && class_exists($class)) {
                $instance = new $class();
                $instance->{$method}();
                return;
            }
        }

        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);
            if (class_exists($class)) {
                $instance = new $class();
                $instance->{$method}();
            }
        }
    }
}

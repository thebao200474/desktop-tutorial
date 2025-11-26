<?php

declare(strict_types=1);

namespace ChemLearn\Controllers;

class BaseController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', dirname(__DIR__));
        }
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $currentUser = $this->getCurrentUser();
        $csrfToken = $this->generateCsrfToken();

        include BASE_PATH . '/views/layout/header.php';
        include BASE_PATH . '/views/' . $view . '.php';
        include BASE_PATH . '/views/layout/footer.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    protected function validateCsrfToken(?string $token): bool
    {
        return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    protected function getCurrentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}

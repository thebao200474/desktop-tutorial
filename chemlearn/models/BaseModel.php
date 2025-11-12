<?php

declare(strict_types=1);

namespace ChemLearn\Models;

use ChemLearn\Config\Database;
use PDO;
use PDOException;
use RuntimeException;

abstract class BaseModel
{
    protected ?PDO $db = null;
    protected bool $connected = false;

    public function __construct()
    {
        try {
            $this->db = Database::getInstance();
            $this->connected = true;
        } catch (PDOException $exception) {
            $this->db = null;
            $this->connected = false;

            if (session_status() === PHP_SESSION_ACTIVE && empty($_SESSION['flash_message'])) {
                $_SESSION['flash_message'] = 'Không thể kết nối cơ sở dữ liệu. Một số tính năng có thể bị hạn chế.';
            }
        }
    }

    protected function hasConnection(): bool
    {
        return $this->connected && $this->db instanceof PDO;
    }

    protected function requireConnection(): PDO
    {
        if (!$this->hasConnection()) {
            throw new RuntimeException('Kết nối cơ sở dữ liệu hiện không khả dụng.');
        }

        return $this->db;
    }
}

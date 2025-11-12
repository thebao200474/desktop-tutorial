<?php

declare(strict_types=1);

namespace ChemLearn\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dbHost = $_ENV['CHEMLEARN_DB_HOST'] ?? 'localhost';
            $dbName = $_ENV['CHEMLEARN_DB_NAME'] ?? 'chemlearn';
            $dbUser = $_ENV['CHEMLEARN_DB_USER'] ?? 'root';
            $dbPass = $_ENV['CHEMLEARN_DB_PASS'] ?? '';
            $charset = 'utf8mb4';

            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $dbHost, $dbName, $charset);

            try {
                self::$instance = new PDO($dsn, $dbUser, $dbPass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $exception) {
                throw new PDOException(
                    'Không thể kết nối cơ sở dữ liệu: ' . $exception->getMessage(),
                    (int) $exception->getCode(),
                    $exception
                );
            }
        }

        return self::$instance;
    }
}

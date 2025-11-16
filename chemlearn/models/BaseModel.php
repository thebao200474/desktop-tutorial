<?php

namespace ChemLearn\Models;

use PDO;

/**
 * BaseModel cung cấp thuộc tính PDO dùng chung cho mọi model.
 */
abstract class BaseModel
{
    /**
     * Kết nối PDO dùng để thao tác cơ sở dữ liệu.
     */
    protected PDO $pdo;

    /**
     * Khởi tạo model với kết nối PDO đã được cấu hình bên ngoài.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}

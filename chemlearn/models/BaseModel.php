<?php

declare(strict_types=1);

namespace ChemLearn\Models;

use ChemLearn\Config\Database;
use PDO;

abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}

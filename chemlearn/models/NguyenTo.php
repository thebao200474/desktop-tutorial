<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class NguyenTo extends BaseModel
{
    public function all(): array
    {
        $statement = $this->db->query('SELECT * FROM nguyento ORDER BY chuky, nhom, kyhieu');
        return $statement->fetchAll();
    }
}

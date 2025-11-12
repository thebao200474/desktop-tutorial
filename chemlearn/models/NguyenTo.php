<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class NguyenTo extends BaseModel
{
    public function all(): array
    {
        if (!$this->hasConnection()) {
            return [];
        }

        $statement = $this->requireConnection()->query('SELECT * FROM nguyento ORDER BY chuky, nhom, kyhieu');
        return $statement->fetchAll();
    }
}

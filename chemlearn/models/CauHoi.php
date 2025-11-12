<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class CauHoi extends BaseModel
{
    public function all(): array
    {
        if (!$this->hasConnection()) {
            return [];
        }

        $statement = $this->requireConnection()->query('SELECT * FROM cauhoi ORDER BY ma_cauhoi');
        return $statement->fetchAll();
    }
}

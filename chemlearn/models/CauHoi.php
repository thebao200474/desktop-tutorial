<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class CauHoi extends BaseModel
{
    public function all(): array
    {
        $statement = $this->db->query('SELECT * FROM cauhoi ORDER BY ma_cauhoi');
        return $statement->fetchAll();
    }
}

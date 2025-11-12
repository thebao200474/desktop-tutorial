<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class PhanUng extends BaseModel
{
    public function findByEquation(string $equation): ?array
    {
        if (!$this->hasConnection()) {
            return null;
        }

        $statement = $this->requireConnection()->prepare('SELECT * FROM phanung WHERE mota = :equation LIMIT 1');
        $statement->bindValue(':equation', $equation, \PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch();
        return $result !== false ? $result : null;
    }
}

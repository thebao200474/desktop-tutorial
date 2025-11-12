<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class DeThi extends BaseModel
{
    public function all(): array
    {
        if (!$this->hasConnection()) {
            return [];
        }

        $statement = $this->requireConnection()->query('SELECT * FROM de_thi ORDER BY nam DESC, ngay_tao DESC');
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        if (!$this->hasConnection()) {
            return null;
        }

        $statement = $this->requireConnection()->prepare('SELECT * FROM de_thi WHERE id = :id LIMIT 1');
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();
        return $result !== false ? $result : null;
    }
}

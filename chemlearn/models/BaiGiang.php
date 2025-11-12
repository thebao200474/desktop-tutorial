<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class BaiGiang extends BaseModel
{
    public function all(): array
    {
        if (!$this->hasConnection()) {
            return [];
        }

        $statement = $this->requireConnection()->query('SELECT * FROM baigiang ORDER BY ma_baigiang DESC');
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        if (!$this->hasConnection()) {
            return null;
        }

        $statement = $this->requireConnection()->prepare('SELECT * FROM baigiang WHERE ma_baigiang = :id');
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch();
        return $result !== false ? $result : null;
    }
}

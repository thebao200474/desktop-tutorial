<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class BaiGiang extends BaseModel
{
    public function all(): array
    {
        $statement = $this->db->query('SELECT * FROM baigiang ORDER BY ma_baigiang DESC');
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare('SELECT * FROM baigiang WHERE ma_baigiang = :id');
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch();
        return $result !== false ? $result : null;
    }
}

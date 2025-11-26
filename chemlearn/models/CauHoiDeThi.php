<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class CauHoiDeThi extends BaseModel
{
    public function forExam(int $examId): array
    {
        if (!$this->hasConnection()) {
            return [];
        }

        $statement = $this->requireConnection()->prepare('SELECT * FROM cau_hoi_de_thi WHERE de_thi_id = :id ORDER BY id');
        $statement->bindValue(':id', $examId, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }
}

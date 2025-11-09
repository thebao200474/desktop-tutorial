<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class HoiDap extends BaseModel
{
    public function store(?int $userId, string $question, string $answer): void
    {
        $statement = $this->db->prepare('INSERT INTO hoi_dap_ai (ma_user, cau_hoi, cau_tra_loi) VALUES (:user, :question, :answer)');
        if ($userId === null) {
            $statement->bindValue(':user', null, \PDO::PARAM_NULL);
        } else {
            $statement->bindValue(':user', $userId, \PDO::PARAM_INT);
        }
        $statement->bindValue(':question', $question, \PDO::PARAM_STR);
        $statement->bindValue(':answer', $answer, \PDO::PARAM_STR);
        $statement->execute();
    }

    public function latest(int $limit = 5): array
    {
        $statement = $this->db->prepare('SELECT * FROM hoi_dap_ai ORDER BY thoigian DESC LIMIT :limit');
        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }
}

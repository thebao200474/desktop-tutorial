<?php

namespace ChemLearn\Models;

use PDO;

/**
 * TopicModel chịu trách nhiệm truy vấn thông tin chủ đề học tập.
 */
class TopicModel extends BaseModel
{
    /**
     * Tìm một chủ đề theo mã định danh.
     */
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM chude WHERE ma_chude = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}

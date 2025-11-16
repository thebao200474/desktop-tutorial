<?php

namespace ChemLearn\Models;

use PDO;

/**
 * TopicModel chịu trách nhiệm truy vấn thông tin chủ đề học tập.
 */
class TopicModel extends BaseModel
{
    /**
     * Lấy toàn bộ chủ đề kèm tổng số bài học trong từng chủ đề.
     */
    public function getAllWithCounts(): array
    {
        $sql = 'SELECT c.ma_chude, c.ten_chude, c.mota, c.icon,
                       COUNT(l.id) AS total_lessons
                FROM chude c
                LEFT JOIN lessons l ON l.ma_chude = c.ma_chude
                GROUP BY c.ma_chude, c.ten_chude, c.mota, c.icon
                ORDER BY c.ma_chude ASC';

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm kiếm chủ đề theo tên, trả về danh sách có tổng số bài học.
     */
    public function searchByName(string $keyword): array
    {
        $sql = 'SELECT c.ma_chude, c.ten_chude, c.mota, c.icon,
                       COUNT(l.id) AS total_lessons
                FROM chude c
                LEFT JOIN lessons l ON l.ma_chude = c.ma_chude
                WHERE c.ten_chude LIKE :kw
                GROUP BY c.ma_chude, c.ten_chude, c.mota, c.icon
                ORDER BY c.ma_chude ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':kw' => '%' . $keyword . '%']);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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

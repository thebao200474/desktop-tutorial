<?php

namespace ChemLearn\Models;

use PDO;

/**
 * LessonModel truy vấn danh sách bài giảng thuộc từng chủ đề.
 */
class LessonModel extends BaseModel
{
    /**
     * Lấy danh sách bài giảng theo mã chủ đề với phân trang.
     */
    public function getByTopic(int $topicId, int $limit = 8, int $offset = 0): array
    {
        $sql = 'SELECT id, title, slug, LEFT(content, 180) AS excerpt, updated_at
                FROM lessons
                WHERE ma_chude = :topic
                ORDER BY updated_at DESC
                LIMIT :limit OFFSET :offset';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':topic', $topicId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số bài giảng trong một chủ đề.
     */
    public function countByTopic(int $topicId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM lessons WHERE ma_chude = :topic');
        $stmt->execute([':topic' => $topicId]);

        return (int) $stmt->fetchColumn();
    }
}

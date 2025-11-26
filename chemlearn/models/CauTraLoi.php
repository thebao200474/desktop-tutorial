<?php

declare(strict_types=1);

namespace ChemLearn\Models;

use PDO;
use PDOException;
use RuntimeException;

class CauTraLoi extends BaseModel
{
    public function findByQuestion(int $cauHoiId): array
    {
        if (!$this->hasConnection()) {
            return [];
        }

        try {
            $pdo = $this->requireConnection();
            $stmt = $pdo->prepare('SELECT * FROM cau_tra_loi WHERE cau_hoi_id = :id ORDER BY is_best DESC, created_at ASC');
            $stmt->bindValue(':id', $cauHoiId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $exception) {
            return [];
        }
    }

    public function create(array $data): int
    {
        if (!$this->hasConnection()) {
            throw new RuntimeException('Không thể gửi câu trả lời lúc này.');
        }

        $sql = 'INSERT INTO cau_tra_loi (cau_hoi_id, user_id, noi_dung_html, is_best) VALUES (:cau_hoi_id, :user_id, :noi_dung_html, :is_best)';
        $pdo = $this->requireConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':cau_hoi_id', $data['cau_hoi_id'], PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $data['user_id'], $data['user_id'] === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':noi_dung_html', $data['noi_dung_html']);
        $stmt->bindValue(':is_best', !empty($data['is_best']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $pdo->lastInsertId();
    }

    public function markAsBest(int $cauHoiId, int $answerId): void
    {
        if (!$this->hasConnection()) {
            return;
        }

        try {
            $pdo = $this->requireConnection();
            $pdo->beginTransaction();

            $reset = $pdo->prepare('UPDATE cau_tra_loi SET is_best = 0 WHERE cau_hoi_id = :id');
            $reset->bindValue(':id', $cauHoiId, PDO::PARAM_INT);
            $reset->execute();

            $mark = $pdo->prepare('UPDATE cau_tra_loi SET is_best = 1 WHERE id = :answer_id');
            $mark->bindValue(':answer_id', $answerId, PDO::PARAM_INT);
            $mark->execute();

            $pdo->commit();
        } catch (PDOException $exception) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
        }
    }
}

<?php

declare(strict_types=1);

namespace ChemLearn\Models;

use PDO;
use PDOException;

class FileDinhKem extends BaseModel
{
    public function createMany(int $cauHoiId, array $items): void
    {
        if (!$this->hasConnection() || $items === []) {
            return;
        }

        try {
            $pdo = $this->requireConnection();
            $stmt = $pdo->prepare('INSERT INTO file_dinh_kem (cau_hoi_id, duong_dan, ten_goc) VALUES (:cau_hoi_id, :duong_dan, :ten_goc)');

            foreach ($items as $item) {
                if (empty($item['duong_dan'])) {
                    continue;
                }

                $stmt->bindValue(':cau_hoi_id', $cauHoiId, PDO::PARAM_INT);
                $stmt->bindValue(':duong_dan', $item['duong_dan']);
                $stmt->bindValue(':ten_goc', $item['ten_goc'] ?? basename($item['duong_dan']));
                $stmt->execute();
            }
        } catch (PDOException $exception) {
            // Bỏ qua khi không thể lưu file đính kèm
        }
    }

    public function findByQuestion(int $cauHoiId): array
    {
        if (!$this->hasConnection()) {
            return [];
        }

        try {
            $pdo = $this->requireConnection();
            $stmt = $pdo->prepare('SELECT * FROM file_dinh_kem WHERE cau_hoi_id = :id');
            $stmt->bindValue(':id', $cauHoiId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $exception) {
            return [];
        }
    }
}

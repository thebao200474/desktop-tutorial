<?php

declare(strict_types=1);

namespace ChemLearn\Models;

use PDO;
use PDOException;

class PhuongTrinhModel extends BaseModel
{
    public function getAll(): array
    {
        if (!$this->hasConnection()) {
            return [];
        }

        try {
            $pdo = $this->requireConnection();
            $stmt = $pdo->query('SELECT id, phuong_trinh, loai_phan_ung, giai_thich, nhom_phan_ung FROM phuongtrinh ORDER BY id');
            return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (PDOException $exception) {
            return [];
        }
    }

    public function search(string $keyword): array
    {
        $keyword = trim($keyword);
        if ($keyword === '' || !$this->hasConnection()) {
            return $this->getAll();
        }

        try {
            $pdo = $this->requireConnection();
            $columns = ['phuong_trinh', 'loai_phan_ung', 'giai_thich', 'nhom_phan_ung'];
            $patterns = array_unique([
                $keyword,
                strtr($keyword, ['0' => '₀', '1' => '₁', '2' => '₂', '3' => '₃', '4' => '₄', '5' => '₅', '6' => '₆', '7' => '₇', '8' => '₈', '9' => '₉']),
            ]);

            $conditions = [];
            $bindings = [];
            $index = 0;

            foreach ($patterns as $pattern) {
                if ($pattern === '') {
                    continue;
                }

                foreach ($columns as $column) {
                    $placeholder = ':kw' . $index++;
                    $conditions[] = sprintf('%s LIKE %s', $column, $placeholder);
                    $bindings[$placeholder] = '%' . $pattern . '%';
                }
            }

            if ($conditions === []) {
                return $this->getAll();
            }

            $sql = 'SELECT id, phuong_trinh, loai_phan_ung, giai_thich, nhom_phan_ung FROM phuongtrinh'
                . ' WHERE ' . implode(' OR ', $conditions)
                . ' ORDER BY id';

            $stmt = $pdo->prepare($sql);

            foreach ($bindings as $placeholder => $value) {
                $stmt->bindValue($placeholder, $value, PDO::PARAM_STR);
            }

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            return [];
        }
    }
}

<?php

declare(strict_types=1);

namespace ChemLearn\Models;

class NguoiDung extends BaseModel
{
    public function create(string $hoTen, string $tenDangNhap, string $matKhau): bool
    {
        if (!$this->hasConnection()) {
            return false;
        }

        $statement = $this->requireConnection()->prepare('INSERT INTO nguoidung (hoten, tendangnhap, matkhau, diem_rank) VALUES (:hoten, :username, :password, 0)');
        $statement->bindValue(':hoten', $hoTen, \PDO::PARAM_STR);
        $statement->bindValue(':username', $tenDangNhap, \PDO::PARAM_STR);
        $statement->bindValue(':password', password_hash($matKhau, PASSWORD_DEFAULT), \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function findByUsername(string $username): ?array
    {
        if (!$this->hasConnection()) {
            return null;
        }

        $statement = $this->requireConnection()->prepare('SELECT * FROM nguoidung WHERE tendangnhap = :username LIMIT 1');
        $statement->bindValue(':username', $username, \PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch();
        return $result !== false ? $result : null;
    }

    public function findById(int $id): ?array
    {
        if (!$this->hasConnection()) {
            return null;
        }

        $statement = $this->requireConnection()->prepare('SELECT * FROM nguoidung WHERE ma_user = :id LIMIT 1');
        $statement->bindValue(':id', $id, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();
        return $result !== false ? $result : null;
    }

    public function incrementRank(int $userId, int $points): int
    {
        if (!$this->hasConnection()) {
            return 0;
        }

        $points = max(0, $points);
        $update = $this->requireConnection()->prepare('UPDATE nguoidung SET diem_rank = diem_rank + :points WHERE ma_user = :id');
        $update->bindValue(':points', $points, \PDO::PARAM_INT);
        $update->bindValue(':id', $userId, \PDO::PARAM_INT);
        $update->execute();

        $statement = $this->db->prepare('SELECT diem_rank FROM nguoidung WHERE ma_user = :id');
        $statement->bindValue(':id', $userId, \PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch();

        return (int)($result['diem_rank'] ?? 0);
    }
}
